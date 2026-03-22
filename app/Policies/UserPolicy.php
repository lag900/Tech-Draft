<?php

namespace App\Policies;

use App\Models\User;
use Illuminate\Auth\Access\Response;

class UserPolicy
{
    /**
     * Compare role levels
     */
    private function canManageTargetUser(User $authUser, User $targetUser): bool
    {
        // Fallback to 0 if roleModel is missing for some reason
        $authLevel = $authUser->roleModel->level ?? 0;
        $targetLevel = $targetUser->roleModel->level ?? 0;

        return $authLevel >= $targetLevel;
    }

    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, User $model): bool
    {
        return true;
    }

    public function create(User $user): bool
    {
        return true;
    }

    public function update(User $user, User $model): bool
    {
        return $this->canManageTargetUser($user, $model);
    }

    public function delete(User $user, User $model): bool
    {
        if ($model->role === 'superadmin' && $user->id !== $model->id) {
            return false;
        }
        return $this->canManageTargetUser($user, $model);
    }

    public function restore(User $user, User $model): bool
    {
        return $this->canManageTargetUser($user, $model);
    }

    public function forceDelete(User $user, User $model): bool
    {
        if ($model->role === 'superadmin') return false;
        return $this->canManageTargetUser($user, $model);
    }
}
