<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    use HasFactory;


    protected $table = 'payments';
    protected $fillable = [
        'order_id',
        'customer_id',
        'service_name',
        'amount_paid',
        'payment_reference',
        'payment_method',
        'payment_status',
        'notes',
        'payment_date',
        'created_by',
        'updated_by',
        'deleted_by',
        'status',
        'created_at',
        'updated_at',
        'deleted_at',
    ];
}
