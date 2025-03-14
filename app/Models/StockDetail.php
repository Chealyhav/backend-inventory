<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;

class StockDetail extends Model
{
    use HasFactory;
    protected $table = "stockdetails";
    protected $fillable = [
        'stock_id',
        'total_stockin',
        'total_stockout',
        'total_stock',
    ];

    public function stock()
    {
        return $this->belongsTo(Stock::class);
    }
}
