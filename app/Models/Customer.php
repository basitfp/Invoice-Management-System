<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Customer extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'email',
        'phone',
        'customer_type',
        'address',
        'vat_registered',
        'vat_number',
        'status'
    ];

    protected $casts = [
        'vat_registered' => 'boolean',
        'status' => 'boolean',
    ];
}