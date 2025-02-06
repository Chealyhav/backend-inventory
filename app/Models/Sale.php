<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Sale extends Model
{
    protected $table = 'sales';
    protected $fillable = [
        'customer_id',
        'total_price',
        'total_discount',
        'total_payment',
        'total_change',
        'status',
        'created_by',
        'updated_by',
        'deleted_by',
    ];
}
