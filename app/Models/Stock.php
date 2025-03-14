<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;  // Add this line for soft deletes
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;

class Stock extends Model
{
    use HasFactory;  // Use the SoftDeletes trait to handle soft deletes

    protected $table = "stocks";  // Define the table name if it's not the default (plural form)

    // Define which attributes are mass assignable
    protected $fillable = [
        'stock_in',
        'stock_out',
        'stock',
        'stock_date',
        'subproduct_id',
        'status',
        'deleted_by',
        'updated_by',
        'created_by',
    ];

    public function subproduct()
    {
        return $this->belongsTo(SubProduct::class, 'subproduct_id');
    }
}
