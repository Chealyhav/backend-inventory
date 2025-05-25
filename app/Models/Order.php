<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    protected $table = 'orders';
    protected $fillable = [
        'customer_id',
        'sale_type',
        'total_price',
        'order_date',
        'status',
        'created_by',
        'updated_by',
        'deleted_by',
        'notes',
        'title',
    ];
}
