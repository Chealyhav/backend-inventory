<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;

class Subproduct extends Model
{
    use    HasFactory;
    protected $table = "subproducts"; // Table name
    protected $fillable = [
        'code',
        'pieces',
        'thickness',
        'length',
        'unit_weight',
        'total_weight',
        'sale_price',
        'buy_price',
        'discount',
        'status',
        'created_at',
        'updated_at',
        'created_by',
        'updated_by',
        'deleted_at',
        'deleted_by',
        'product_id', // Foreign key for products table
        'color_id', // Foreign key for colors table
    ];

    // Define relationships with other models
    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id');
    }

    public function color()
    {
        return $this->belongsTo(Color::class, 'color_id');
    }
}
