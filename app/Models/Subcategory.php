<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Category;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;


class Subcategory extends Model
{

    use HasFactory;
    protected $table = 'sub_category';
    protected $fillable = [
        'name',
        'SN',
        'category_id',
        'created_by',
        'description',
        'input_date',
        'updated_by',
        'deleted_at',
        'status',
    ];

    public function category()
    {
        return $this->belongsTo(Category::class, 'category_id');
    }

    public function products()
    {
        return $this->hasMany(Product::class, 'sub_category_id'); // Correct foreign key
    }

}
