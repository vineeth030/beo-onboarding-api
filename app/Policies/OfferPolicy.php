<?php

namespace App\Policies;

use App\Models\Offer;
use App\Models\User;

class OfferPolicy
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
     * Listing all offers is restricted to admins (handled by before()).
     */
    public function viewAny(User $user): bool
    {
        return false;
    }

    /**
     * A user may only view the offer that belongs to their employee record.
     */
    public function view(User $user, Offer $offer): bool
    {
        return $user->id === $offer->employee->user_id;
    }

    /**
     * Only admins may create offers (handled by before()).
     */
    public function create(User $user): bool
    {
        return false;
    }

    /**
     * A user may update (accept/decline) their own offer.
     */
    public function update(User $user, Offer $offer): bool
    {
        return $user->id === $offer->employee->user_id;
    }

    /**
     * Only admins may delete offers (handled by before()).
     */
    public function delete(User $user, Offer $offer): bool
    {
        return false;
    }
}
