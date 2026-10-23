<?php

namespace App\Policies;

use App\Models\Ticket;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class TicketPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any tickets.
     */
    public function viewAny(User $user): bool
    {
        // All authenticated users can view tickets list (filtered by their access)
        return true;
    }

    /**
     * Determine whether the user can view the ticket.
     */
    public function view(User $user, Ticket $ticket): bool
    {
        // Admin can view all tickets
        if ($user->isAdmin()) {
            return true;
        }

        // Creator can view their own ticket
        if ($ticket->created_by === $user->id) {
            return true;
        }

        // Assigned user can view the ticket
        if ($ticket->assigned_to === $user->id) {
            return true;
        }

        // Support staff can view tickets in their department
        if ($user->isSupport() && $ticket->department_id === $user->department_id) {
            return true;
        }

        // Department supervisor can view all tickets in their department
        if ($user->supervisedDepartments()->where('id', $ticket->department_id)->exists()) {
            return true;
        }

        return false;
    }

    /**
     * Determine whether the user can create tickets.
     */
    public function create(User $user): bool
    {
        // All active users can create tickets
        return $user->is_active;
    }

    /**
     * Determine whether the user can update the ticket.
     */
    public function update(User $user, Ticket $ticket): bool
    {
        // Admin can update any ticket
        if ($user->isAdmin()) {
            return true;
        }

        // Creator can update their own open tickets
        if ($ticket->created_by === $user->id && $ticket->isOpen()) {
            return true;
        }

        // Assigned support can update the ticket
        if ($ticket->assigned_to === $user->id) {
            return true;
        }

        // Department supervisor can update tickets in their department
        if ($user->supervisedDepartments()->where('id', $ticket->department_id)->exists()) {
            return true;
        }

        return false;
    }

    /**
     * Determine whether the user can delete the ticket.
     */
    public function delete(User $user, Ticket $ticket): bool
    {
        // Only admin can delete tickets
        if ($user->isAdmin()) {
            return true;
        }

        // Creator can delete their own ticket if it's still open and has no replies
        if ($ticket->created_by === $user->id
            && $ticket->status === 'open'
            && $ticket->replies()->count() === 0) {
            return true;
        }

        return false;
    }

    /**
     * Determine whether the user can restore the ticket.
     */
    public function restore(User $user, Ticket $ticket): bool
    {
        // Only admin can restore tickets
        return $user->isAdmin();
    }

    /**
     * Determine whether the user can permanently delete the ticket.
     */
    public function forceDelete(User $user, Ticket $ticket): bool
    {
        // Only admin can force delete tickets
        return $user->isAdmin();
    }

    /**
     * Determine whether the user can reply to the ticket.
     */
    public function reply(User $user, Ticket $ticket): bool
    {
        // Must be able to view the ticket first
        if (!$this->view($user, $ticket)) {
            return false;
        }

        // Can't reply to closed tickets unless admin
        if ($ticket->isClosed() && !$user->isAdmin()) {
            return false;
        }

        return true;
    }

    /**
     * Determine whether the user can assign the ticket.
     */
    public function assign(User $user, Ticket $ticket): bool
    {
        // Admin can assign any ticket
        if ($user->isAdmin()) {
            return true;
        }

        // Support staff can assign tickets in their department
        if ($user->isSupport() && $ticket->department_id === $user->department_id) {
            return true;
        }

        // Department supervisor can assign tickets in their department
        if ($user->supervisedDepartments()->where('id', $ticket->department_id)->exists()) {
            return true;
        }

        return false;
    }

    /**
     * Determine whether the user can change ticket status.
     */
    public function changeStatus(User $user, Ticket $ticket): bool
    {
        // Admin can change any ticket status
        if ($user->isAdmin()) {
            return true;
        }

        // Assigned user can change status
        if ($ticket->assigned_to === $user->id) {
            return true;
        }

        // Department supervisor can change status
        if ($user->supervisedDepartments()->where('id', $ticket->department_id)->exists()) {
            return true;
        }

        return false;
    }

    /**
     * Determine whether the user can view internal notes.
     */
    public function viewInternalNotes(User $user, Ticket $ticket): bool
    {
        // Only support staff, assigned user, and admin can view internal notes
        return $user->isAdmin()
            || $user->isSupport()
            || $ticket->assigned_to === $user->id;
    }

    /**
     * Determine whether the user can add internal notes.
     */
    public function addInternalNote(User $user, Ticket $ticket): bool
    {
        // Only support staff and admin can add internal notes
        return $user->isAdmin() || $user->isSupport();
    }
}
