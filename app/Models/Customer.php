<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;



class Customer extends Model
{
    use HasFactory;
    protected $table = 'customers';
    protected $fillable = ['id','name', 'gender','company_name','email', 'phone_number', 
    'address','vat_number','status'];
}
