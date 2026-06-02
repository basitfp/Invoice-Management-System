<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $fillable = [
        'name',
        'description',
        'category_id',
        'qty',
        'purchase_price',
        'selling_price',
        'vat',
        'moq',
        'status',
    ];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }
}
