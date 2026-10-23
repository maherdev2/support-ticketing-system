<?php

namespace App\Http\Controllers;

use App\Models\Department;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Inertia\Inertia;

class SettingsController extends Controller
{
    /**
     * Main settings page - users list
     */
    public function index(Request $request)
    {
        Gate::authorize('admin', $request->user());

        $users = User::with(['role', 'department'])
            ->when($request->search, fn($q) => $q->where('name', 'like', "%{$request->search}%")
                ->orWhere('email', 'like', "%{$request->search}%")
                ->orWhere('employee_id', 'like', "%{$request->search}%"))
            ->when($request->role_id, fn($q) => $q->where('role_id', $request->role_id))
            ->when($request->department_id, fn($q) => $q->where('department_id', $request->department_id))
            ->orderBy('name')
            ->paginate(15)
            ->withQueryString();

        return Inertia::render('Settings/Index', [
            'users'       => $users,
            'roles'       => Role::orderBy('name')->get(),
            'departments' => Department::where('is_active', true)->orderBy('name_ar')->get(),
            'filters'     => $request->only(['search', 'role_id', 'department_id']),
            'stats'       => [
                'total'   => User::count(),
                'admins'  => User::whereHas('role', fn($q) => $q->where('name', 'admin'))->count(),
                'support' => User::whereHas('role', fn($q) => $q->where('name', 'support'))->count(),
                'users'   => User::whereHas('role', fn($q) => $q->where('name', 'user'))->count(),
                'active'  => User::where('is_active', true)->count(),
            ],
        ]);
    }

    /**
     * Store a new user
     */
    public function store(Request $request)
    {
        Gate::authorize('admin', $request->user());

        $validated = $request->validate([
            'name'          => 'required|string|max:255',
            'email'         => 'required|email|unique:users,email',
            'password'      => 'required|string|min:8',
            'role_id'       => 'required|exists:roles,id',
            'department_id' => 'nullable|exists:departments,id',
            'phone'         => 'nullable|string|max:20',
            'employee_id'   => 'nullable|string|max:50|unique:users,employee_id',
            'is_active'     => 'boolean',
        ]);

        $validated['password'] = Hash::make($validated['password']);
        $validated['is_active'] = $validated['is_active'] ?? true;

        User::create($validated);

        return back()->with('success', 'تم إضافة الموظف بنجاح');
    }

    /**
     * Update an existing user
     */
    public function update(Request $request, User $user)
    {
        Gate::authorize('admin', $request->user());

        $validated = $request->validate([
            'name'          => 'required|string|max:255',
            'email'         => ['required', 'email', Rule::unique('users')->ignore($user->id)],
            'password'      => 'nullable|string|min:8',
            'role_id'       => 'required|exists:roles,id',
            'department_id' => 'nullable|exists:departments,id',
            'phone'         => 'nullable|string|max:20',
            'employee_id'   => ['nullable', 'string', 'max:50', Rule::unique('users')->ignore($user->id)],
            'is_active'     => 'boolean',
        ]);

        if (!empty($validated['password'])) {
            $validated['password'] = Hash::make($validated['password']);
        } else {
            unset($validated['password']);
        }

        $user->update($validated);

        return back()->with('success', 'تم تحديث بيانات الموظف بنجاح');
    }

    /**
     * Delete a user
     */
    public function destroy(Request $request, User $user)
    {
        Gate::authorize('admin', $request->user());

        // Prevent deleting yourself
        if ($user->id === $request->user()->id) {
            return back()->with('error', 'لا يمكنك حذف حسابك الخاص');
        }

        // Prevent deleting user with tickets
        if ($user->createdTickets()->count() > 0 || $user->assignedTickets()->count() > 0) {
            // Soft disable instead
            $user->update(['is_active' => false]);
            return back()->with('success', 'تم تعطيل الحساب (لا يمكن حذفه لوجود تذاكر مرتبطة به)');
        }

        $user->delete();

        return back()->with('success', 'تم حذف الموظف بنجاح');
    }

    /**
     * Toggle user active status
     */
    public function toggleActive(Request $request, User $user)
    {
        Gate::authorize('admin', $request->user());

        if ($user->id === $request->user()->id) {
            return back()->with('error', 'لا يمكنك تعطيل حسابك الخاص');
        }

        $user->update(['is_active' => !$user->is_active]);
        $msg = $user->is_active ? 'تم تفعيل الحساب بنجاح' : 'تم تعطيل الحساب بنجاح';

        return back()->with('success', $msg);
    }
}
