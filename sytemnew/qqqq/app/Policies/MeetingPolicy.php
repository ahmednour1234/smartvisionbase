<?php

namespace App\Policies;

use App\Models\Meeting;
use App\Models\User;

class MeetingPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->is_active;
    }

    public function view(User $user, Meeting $meeting): bool
    {
        if (! $user->is_active) {
            return false;
        }

        return $user->role?->slug !== 'sales' || $meeting->user_id === $user->id;
    }

    public function create(User $user): bool
    {
        return $user->is_active;
    }

    public function update(User $user, Meeting $meeting): bool
    {
        if (! $user->is_active) {
            return false;
        }

        return $user->role?->slug !== 'sales' || $meeting->user_id === $user->id;
    }

    public function delete(User $user, Meeting $meeting): bool
    {
        if (! $user->is_active) {
            return false;
        }

        return $user->role?->slug !== 'sales' || $meeting->user_id === $user->id;
    }
}
