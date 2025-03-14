<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;


class Color extends Model
{
    use HasFactory;
    protected $table = 'colors';
    protected $fillable = ['name', 'code', 'status'];


    public function subcategories()
    {
        return $this->hasMany(SubCategory::class);
    }


}
