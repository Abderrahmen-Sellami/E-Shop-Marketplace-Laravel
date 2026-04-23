<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $fillable = ['user_id', 'status', 'total', 'shipping_address', 'payment_method'];

    public function user()       { return $this->belongsTo(User::class); }
    public function items()      { return $this->hasMany(OrderItem::class); }

    public function isPending()   { return $this->status === 'pending'; }
    public function isValidated() { return $this->status === 'validated'; }
    public function isCancelled() { return $this->status === 'cancelled'; }

    public function getStatusLabelAttribute(): string
    {
        return match($this->status) {
            'pending'   => 'En attente',
            'validated' => 'Validée',
            'cancelled' => 'Annulée',
            default     => $this->status,
        };
    }
}
