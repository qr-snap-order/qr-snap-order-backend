<?php

namespace App\Policies;

use App\Models\OrderItemHistory;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class OrderItemHistoryPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        throw new \Exception('unimplemented.');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, OrderItemHistory $orderItemHistory): bool
    {
        throw new \Exception('unimplemented.');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        throw new \Exception('unimplemented.');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, OrderItemHistory $orderItemHistory): bool
    {
        throw new \Exception('unimplemented.');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, OrderItemHistory $orderItemHistory): bool
    {
        throw new \Exception('unimplemented.');
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, OrderItemHistory $orderItemHistory): bool
    {
        throw new \Exception('unimplemented.');
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, OrderItemHistory $orderItemHistory): bool
    {
        throw new \Exception('unimplemented.');
    }
}
