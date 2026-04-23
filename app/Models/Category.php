<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Category extends Model
{
    protected $fillable = ['name', 'slug', 'description', 'image', 'parent_id'];

    protected static function boot()
    {
        parent::boot();
        static::creating(fn($c) => $c->slug = Str::slug($c->name));
    }

    public function products() { return $this->hasMany(Product::class); }
    public function parent()   { return $this->belongsTo(Category::class, 'parent_id'); }
    public function children() { return $this->hasMany(Category::class, 'parent_id'); }
}
