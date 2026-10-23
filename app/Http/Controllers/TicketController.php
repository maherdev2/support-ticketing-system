<?php

namespace App\Http\Controllers;

use App\Events\TicketUpdated;
use App\Http\Requests\StoreTicketRequest;
use App\Http\Requests\StoreTicketReplyRequest;
use App\Http\Requests\UpdateTicketRequest;
use App\Models\Attachment;
use App\Models\Department;
use App\Models\Equipment;
use App\Models\Ticket;
use App\Models\TicketReply;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Inertia\Inertia;

class TicketController extends Controller
{
    /**
     * Display a listing of tickets.
     */
    public function index(Request $request)
    {
        Gate::authorize('viewAny', Ticket::class);

        $user = $request->user();

        // Base query for user access
        $baseQuery = Ticket::query();
        if (!$user->isAdmin()) {
            $baseQuery->where(function ($q) use ($user) {
                $q->where('created_by', $user->id)
                    ->orWhere('assigned_to', $user->id);

                if ($user->isSupport() && $user->department_id) {
                    $q->orWhere('department_id', $user->department_id);
                }
            });
        }

        // Stats calculation
        $stats = [
            'total' => (clone $baseQuery)->count(),
            'open' => (clone $baseQuery)->where('status', 'open')->count(),
            'in_progress' => (clone $baseQuery)->whereIn('status', ['assigned', 'in_progress', 'waiting_user'])->count(),
            'resolved' => (clone $baseQuery)->whereIn('status', ['resolved', 'closed'])->count(),
        ];

        // Filtered tickets query
        $query = (clone $baseQuery)->with(['creator', 'assignedUser', 'department', 'equipment'])
            ->withCount('replies')
            ->orderBy('created_at', 'desc');

        // Apply filters
        if ($request->filled('status') && $request->status !== 'all') {
            $query->where('status', $request->status);
        }

        if ($request->filled('priority') && $request->priority !== 'all') {
            $query->where('priority', $request->priority);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('ticket_number', 'like', "%{$search}%")
                    ->orWhere('title', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
            });
        }

        $tickets = $query->paginate(15)->withQueryString();

        return Inertia::render('Tickets/Index', [
            'tickets' => $tickets,
            'stats' => $stats,
            'filters' => [
                'status' => $request->status ?? 'all',
                'priority' => $request->priority ?? 'all',
                'search' => $request->search ?? '',
            ],
        ]);
    }

    /**
     * Show the form for creating a new ticket.
     */
    public function create()
    {
        Gate::authorize('create', Ticket::class);

        $departments = Department::where('is_active', true)->get();
        $equipment = Equipment::where('assigned_to', auth()->id())
            ->orWhere('status', 'available')
            ->get();

        return Inertia::render('Tickets/Create', [
            'departments' => $departments,
            'equipment' => $equipment,
        ]);
    }

    /**
     * Store a newly created ticket in storage.
     */
    public function store(StoreTicketRequest $request)
    {
        Gate::authorize('create', Ticket::class);

        DB::beginTransaction();
        try {
            // Create ticket
            $ticket = Ticket::create([
                'title' => $request->title,
                'description' => $request->description,
                'priority' => $request->priority,
                'category' => $request->category,
                'equipment_id' => $request->equipment_id,
                'department_id' => $request->department_id ?? $request->user()->department_id,
                'created_by' => $request->user()->id,
                'status' => 'open',
            ]);

            // Handle attachments
            if ($request->hasFile('attachments')) {
                $this->handleAttachments($request->file('attachments'), $ticket, $request->user()->id);
            }

            // Broadcast event if reverb/broadcasting configured
            try {
                broadcast(new TicketUpdated($ticket->load(['creator', 'assignedUser', 'department'])));
            } catch (\Throwable $e) {
                // Ignore broadcast failure if offline
            }

            DB::commit();

            return redirect()
                ->route('tickets.show', $ticket)
                ->with('success', 'تم إنشاء التذكرة بنجاح برقم: ' . $ticket->ticket_number);

        } catch (\Exception $e) {
            DB::rollBack();
            return back()
                ->withInput()
                ->with('error', 'حدث خطأ أثناء إنشاء التذكرة: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified ticket.
     */
    public function show(Ticket $ticket)
    {
        Gate::authorize('view', $ticket);

        $ticket->load([
            'creator.role',
            'assignedUser.role',
            'department',
            'equipment',
            'attachments.uploader',
            'replies' => function ($query) use ($ticket) {
                if (!Gate::allows('viewInternalNotes', $ticket)) {
                    $query->where('is_internal', false);
                }
                $query->with(['user.role', 'attachments.uploader'])->orderBy('created_at', 'asc');
            },
        ]);

        $departments = Department::where('is_active', true)->get();
        $supportUsers = User::whereHas('role', function ($q) {
            $q->whereIn('name', ['admin', 'support']);
        })->where('is_active', true)->get(['id', 'name', 'role_id', 'department_id']);

        return Inertia::render('Tickets/Show', [
            'ticket' => $ticket,
            'departments' => $departments,
            'supportUsers' => $supportUsers,
            'canUpdate' => Gate::allows('update', $ticket),
            'canReply' => Gate::allows('reply', $ticket),
            'canAssign' => Gate::allows('assign', $ticket),
            'canChangeStatus' => Gate::allows('changeStatus', $ticket),
            'canAddInternalNote' => Gate::allows('addInternalNote', $ticket),
            'canDelete' => Gate::allows('delete', $ticket),
        ]);
    }

    /**
     * Update the specified ticket.
     */
    public function update(UpdateTicketRequest $request, Ticket $ticket)
    {
        Gate::authorize('update', $ticket);

        DB::beginTransaction();
        try {
            $oldStatus = $ticket->status;
            $oldAssignedTo = $ticket->assigned_to;

            // Update ticket
            $ticket->update($request->validated());

            // Track status changes
            if ($request->has('status') && $oldStatus !== $request->status) {
                $this->handleStatusChange($ticket, $oldStatus, $request->status);
            }

            // Track assignment changes
            if ($request->has('assigned_to') && $oldAssignedTo !== $request->assigned_to) {
                $this->handleAssignmentChange($ticket, $oldAssignedTo, $request->assigned_to);
            }

            try {
                broadcast(new TicketUpdated($ticket->fresh()->load(['creator', 'assignedUser', 'department'])));
            } catch (\Throwable $e) {
                // Ignore broadcast error
            }

            DB::commit();

            return back()->with('success', 'تم تحديث التذكرة بنجاح');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'حدث خطأ أثناء تحديث التذكرة: ' . $e->getMessage());
        }
    }

    /**
     * Store a reply to the ticket.
     */
    public function reply(StoreTicketReplyRequest $request, Ticket $ticket)
    {
        Gate::authorize('reply', $ticket);

        DB::beginTransaction();
        try {
            // Create reply
            $reply = $ticket->replies()->create([
                'user_id' => $request->user()->id,
                'message' => $request->message,
                'is_internal' => $request->boolean('is_internal', false) && Gate::allows('addInternalNote', $ticket),
                'is_system' => false,
            ]);

            // Handle attachments
            if ($request->hasFile('attachments')) {
                $this->handleAttachments($request->file('attachments'), $reply, $request->user()->id);
            }

            try {
                broadcast(new TicketUpdated($ticket->fresh()->load(['creator', 'assignedUser', 'department'])));
            } catch (\Throwable $e) {
                // Ignore broadcast error
            }

            DB::commit();

            return back()->with('success', 'تم إضافة الرد بنجاح');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'حدث خطأ أثناء إضافة الرد: ' . $e->getMessage());
        }
    }

    /**
     * Handle file attachments securely.
     */
    private function handleAttachments(array $files, $attachable, int $userId): void
    {
        foreach ($files as $file) {
            $storedName = Str::random(40) . '.' . $file->getClientOriginalExtension();
            $path = $file->storeAs('attachments', $storedName, 'private');

            $hash = hash_file('sha256', $file->getRealPath());

            $attachable->attachments()->create([
                'uploaded_by' => $userId,
                'original_name' => $file->getClientOriginalName(),
                'stored_name' => $storedName,
                'mime_type' => $file->getMimeType(),
                'size' => $file->getSize(),
                'path' => $path,
                'disk' => 'private',
                'hash' => $hash,
                'is_scanned' => true,
                'is_safe' => true,
            ]);
        }
    }

    /**
     * Handle ticket status changes.
     */
    private function handleStatusChange(Ticket $ticket, string $oldStatus, string $newStatus): void
    {
        if ($newStatus === 'resolved') {
            $ticket->update(['resolved_at' => now()]);
        } elseif ($newStatus === 'closed') {
            $ticket->update(['closed_at' => now()]);
        }

        $statusMessages = [
            'open' => 'مفتوحة',
            'assigned' => 'معينة',
            'in_progress' => 'قيد المعالجة',
            'waiting_user' => 'في انتظار المستخدم',
            'resolved' => 'محلولة',
            'closed' => 'مغلقة',
        ];

        $oldName = $statusMessages[$oldStatus] ?? $oldStatus;
        $newName = $statusMessages[$newStatus] ?? $newStatus;

        $ticket->replies()->create([
            'user_id' => auth()->id(),
            'message' => "تم تغيير حالة التذكرة من [{$oldName}] إلى [{$newName}]",
            'is_internal' => false,
            'is_system' => true,
        ]);
    }

    /**
     * Handle ticket assignment changes.
     */
    private function handleAssignmentChange(Ticket $ticket, ?int $oldAssignedTo, ?int $newAssignedTo): void
    {
        $ticket->update(['assigned_at' => $newAssignedTo ? now() : null]);

        if ($newAssignedTo) {
            $assignedUser = User::find($newAssignedTo);
            $message = "تم تعيين التذكرة إلى: {$assignedUser->name}";
        } else {
            $message = "تم إلغاء تعيين التذكرة";
        }

        $ticket->replies()->create([
            'user_id' => auth()->id(),
            'message' => $message,
            'is_internal' => false,
            'is_system' => true,
        ]);
    }

    /**
     * Download attachment securely.
     */
    public function downloadAttachment(Attachment $attachment)
    {
        if ($attachment->attachable_type === Ticket::class) {
            $ticket = $attachment->attachable;
            Gate::authorize('view', $ticket);
        } elseif ($attachment->attachable_type === TicketReply::class) {
            $reply = $attachment->attachable;
            Gate::authorize('view', $reply->ticket);
        } else {
            abort(403);
        }

        if (!Storage::disk($attachment->disk)->exists($attachment->path)) {
            abort(404, 'الملف غير موجود على الخادم');
        }

        return Storage::disk($attachment->disk)->download(
            $attachment->path,
            $attachment->original_name
        );
    }
}
