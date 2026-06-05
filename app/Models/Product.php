<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Product extends Model
{
   protected $fillable = [
    'name',
    'item_code',
    'item_class',
    'category_id',
    'manufacturer_id',
    'hsn_code',
    'regional_name',
    'unit',
    'image',
    'purchase_price',
    'purchase_tax_percent', // Ensure this is here
    'purchase_tax_inclusive',
    'selling_price',        // Ensure this is here
    'sale_price',           // Ensure this is here
    'vat',                  // Ensure this is here
    'gst_vat_percent',      // Ensure this is here
    'sale_tax_inclusive',
    'discount_percentage',  // Ensure this is here
    'discount_percent',     // Ensure this is here
    'cess_percentage',      // Ensure this is here
    'cess_percent',         // Ensure this is here
    'additional_cess',
    'is_weighing_item',
    'qty',
    'moq',
    'description',
    'status',
];

    protected $casts = [
        'purchase_price'         => 'decimal:2',
        'purchase_tax_percent'   => 'decimal:2',
        'purchase_tax_inclusive' => 'boolean',
        'sale_price'             => 'decimal:2',
        'gst_vat_percent'        => 'decimal:2',
        'sale_tax_inclusive'     => 'boolean',
        'discount_percent'       => 'decimal:2',
        'cess_percent'           => 'decimal:2',
        'additional_cess'        => 'decimal:2',
        'is_weighing_item'       => 'boolean',
        'status'                 => 'boolean',
    ];

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function manufacturer(): BelongsTo
    {
        return $this->belongsTo(Manufacturer::class);
    }
}
