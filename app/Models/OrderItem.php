<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderItem extends Model
{
    use HasFactory;

    protected $table = 'order_items';
    protected $fillable = [
        'order_id',
        'subproduct_id',
        'sale_type_id',
        'quantity',
        'unit_price',
        'total_price',
        'notes',
        'title',
        'order_date',
        'delivery_date',
        'status',
        'invoice_id',
        'created_by',
        'updated_by',
        'deleted_by'
    ];

}
