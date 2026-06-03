<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    protected $fillable = [
        'app_name',
        'logo',
        'address',
        'phone',
        'email',
        'bank_name',
        'iban',
        'swift_code',
    ];
}