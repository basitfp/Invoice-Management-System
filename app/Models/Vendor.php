<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Vendor extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'company',
        'phone',
        'email',
        'tax_reg_number',
        'address_line_1',
        'address_line_2',
        'city',
        'pin_code',
        'state',
        'country',
        'status',
    ];

    protected $casts = [
        'status' => 'boolean',
    ];
}