<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Invoice extends Model
{
    protected $fillable = [
        'invoice_number',
        'customer_id',
        'agent_id',
        'invoice_date',
        'total_vat',
        'total_amount',
        'status',
        'due_date',
    ];

    // Invoice ke sare items
    public function items()
    {
        return $this->hasMany(InvoiceItem::class);
    }

    // Invoice ka customer
    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    // Invoice creator (Agent)
    public function agent()
    {
        return $this->belongsTo(User::class, 'agent_id');
    }
}