<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    protected $table = 'roles';
    protected $fillable = ['name', 'status'];

    // A role can have many users
    public function users()
    {
        return $this->belongsToMany(User::class); // Define the relationship with the User model
    }
}
