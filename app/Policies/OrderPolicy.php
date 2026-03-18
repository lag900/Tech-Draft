<?php

namespace App\Policies;

use App\Models\Order;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class OrderPolicy
{
    /**
     * Determine whether the user can view the model.
     */
    public function viewOrder(User $user, Order $order): bool
    {
        if ($user->role === 'superadmin') {
            return true;
        }

        if ($user->role === 'client') {
            return $order->client?->user_id === $user->id;
        }
        
        return true; 
    }

    /**
     * Determine whether the user can update the model.
     */
    public function updateOrder(User $user, Order $order): bool
    {
        if ($user->role === 'superadmin') {
            return true;
        }
        return in_array($user->role, ['admin', 'manager']);
    }

    /**
     * Determine whether the user can approve for sampling
     */
    public function approveSampling(User $user, Order $order): bool
    {
        if ($user->role === 'superadmin') {
            return true;
        }
        return in_array($user->role, ['admin', 'manager']) && $order->status === 'in_review';
    }

    /**
     * Determine whether the user can export tech pack
     */
    public function exportTechPack(User $user, Order $order): bool
    {
        if ($user->role === 'superadmin') {
            return true;
        }
        return in_array($user->role, ['admin', 'manager']);
    }
}
