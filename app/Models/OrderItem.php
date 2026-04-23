<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrderItem extends Model
{
    // Ajout de service_id dans le fillable
    protected $fillable = ['order_id', 'product_id', 'service_id', 'quantity', 'unit_price'];

    public function order()   { return $this->belongsTo(Order::class); }
    public function product() { return $this->belongsTo(Product::class); }

    
    public function service() { return $this->belongsTo(Service::class); }

    public function getSubtotalAttribute(): float
    {
        return $this->quantity * $this->unit_price;
    }
}