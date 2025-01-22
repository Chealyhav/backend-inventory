<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\SubCategory;

class Category extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'categories';

    protected $fillable = [
        'id',
        'name',
        'SN',
        'created_by',
        'description',
        'input_date',
        'updated_by',
        'deleted_at',

    ];

    public function subcategories()
    {
        return $this->hasMany(SubCategory::class);
    }
}
