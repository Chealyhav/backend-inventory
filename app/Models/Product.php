<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class Product extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'product_name',
        'product_code',
        'img_url',
        'sub_category_id',  // Ensure the column name matches the database
        'availableStock',
        'stockType',
        'status',
        'created_by', // Optional if you want to fill user IDs
        'updated_by', // Optional if you want to track updates
        'deleted_by', // Optional for soft delete tracking
    ];

    // Optionally, you can define relationships if you have them, e.g.:
    public function subCategory()
    {
        return $this->belongsTo(SubCategory::class, 'sub_category_id');
    }
    public function subProduct()
    {
        return $this->belongsTo(SubProduct::class,'sub_product_id');
    }


    // You can also use the boot method to automatically set the 'created_by', 'updated_by' fields based on the current user
    // protected static function boot()
    // {
    //     parent::boot();

    //     static::creating(function ($product) {
    //         // Automatically set created_by and updated_by if user is logged in
    //         $user = auth()->user();
    //         if ($user) {
    //             $product->created_by = $user->id;
    //         }
    //     });

    //     static::updating(function ($product) {
    //         // Automatically set updated_by if user is logged in
    //         $user = auth()->user();
    //         if ($user) {
    //             $product->updated_by = $user->id;
    //         }
    //     });
    // }
}
