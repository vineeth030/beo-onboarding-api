<?php

namespace App\Policies;

use App\Models\Employee;
use App\Models\User;

class EmployeePolicy
{
    /**
     * Admins bypass all policy checks automatically via `before()`.
     */
    public function before(User $user, string $ability): ?bool
    {
        if ($user->role === 'admin') {
            return true;
        }

        return null; // defer to individual policy methods for non-admins
    }

    /**
     * Listing all employees is restricted to admins (handled by before()).
     * A candidate should never see the full list.
     */
    public function viewAny(User $user): bool
    {
        return false;
    }

    /**
     * A user may view their own employee record.
     */
    public function view(User $user, Employee $employee): bool
    {
        return $user->id === $employee->user_id;
    }

    /**
     * Only admins may create employees (handled by before()).
     */
    public function create(User $user): bool
    {
        return false;
    }

    /**
     * A user may update their own employee record.
     */
    public function update(User $user, Employee $employee): bool
    {
        return $user->id === $employee->user_id;
    }

    /**
     * Only admins may delete employees (handled by before()).
     */
    public function delete(User $user, Employee $employee): bool
    {
        return false;
    }

    /**
     * Only admins may perform admin-only workflow actions (handled by before()).
     * Shared method referenced by controller authorize() calls.
     */
    public function adminOnly(User $user): bool
    {
        return false;
    }
}
