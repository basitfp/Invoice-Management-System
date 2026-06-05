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
        'gender',
        'birthdate',
        'address',
        'shipping_address',
        'city',
        'pin_code',
        'state',
        'country',
        'landmark',
        'area_id',
        'credit_days',
        'credit_limit',
        'vat_registered',
        'vat_number',
        'status',
    ];

    protected $casts = [
        'vat_registered' => 'boolean',
        'status'         => 'boolean',
        'birthdate'      => 'date',
        'credit_limit'   => 'decimal:2',
    ];

    // ----------------------------
    // Relationships
    // ----------------------------
    public function area()
    {
        return $this->belongsTo(Area::class);
    }
}