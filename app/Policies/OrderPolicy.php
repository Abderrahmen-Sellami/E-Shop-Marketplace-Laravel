<?php

namespace App\Policies;

use App\Models\Order;
use App\Models\User;

class OrderPolicy
{
    /**
     * L'administrateur a tous les droits
     */
    public function before(User $user)
    {
        if ($user->role === 'admin') {
            return true;
        }
    }

    /**
     * Un utilisateur peut voir sa propre commande
     */
    public function view(User $user, Order $order): bool
    {
        
        return $user->id == $order->user_id;
    }
}