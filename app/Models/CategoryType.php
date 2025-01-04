<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CategoryType extends Model
{
    
    public function categories()
    {
        return $this->belongsTo(Category::class);
    }

}
