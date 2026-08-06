<?php

namespace App\Policies;

use App\Models\Company;
use App\Models\User;

class CompanyPolicy
{
    public function viewAny(User $user): bool
    {
        if (! $user->is_active) {
            return false;
        }

        return $user->hasPermission('company.view');
    }

    public function view(User $user, Company $company): bool
    {
        if (! $user->is_active) {
            return false;
        }

        if (! $user->hasPermission('company.view')) {
            return false;
        }

        if ($user->hasPermission('company.view.any')) {
            return true;
        }

        return $company->owner_id === $user->id || $company->created_by === $user->id;
    }

    public function create(User $user): bool
    {
        if (! $user->is_active) {
            return false;
        }

        return $user->hasPermission('company.create');
    }

    public function update(User $user, Company $company): bool
    {
        if (! $user->is_active) {
            return false;
        }

        if (! $user->hasPermission('company.update')) {
            return false;
        }

        if ($user->hasPermission('company.update.any')) {
            return true;
        }

        return $company->owner_id === $user->id || $company->created_by === $user->id;
    }

    public function delete(User $user, Company $company): bool
    {
        if (! $user->is_active) {
            return false;
        }

        if (! $user->hasPermission('company.delete')) {
            return false;
        }

        if ($user->hasPermission('company.delete.any')) {
            return true;
        }

        return $company->owner_id === $user->id || $company->created_by === $user->id;
    }
}
