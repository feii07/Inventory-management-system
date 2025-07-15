<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Item extends Model
{
    use SoftDeletes;
    
    protected $fillable = [
        'name',
        'description',
        'sku',
        'price',
        'stock',
        'min_stock',
        'category',
        'unit',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'price' => 'integer',
        'stock' => 'decimal:2',
        'min_stock' => 'integer',
    ];

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updater()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function logs()
    {
        return $this->hasMany(ItemLog::class);
    }

    public function isLowStock()
    {
        return $this->stock <= $this->min_stock;
    }

    public function getStockAttribute($value)
    {
        return number_format((float)$value, 2, '.', '');
    }
}
