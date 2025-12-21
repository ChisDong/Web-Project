<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductVariant extends Model
{
    use HasFactory;
    protected $fillable = [
        'product_id',
        'color_id',
        'size_id',
        'sku',
        'price',
        'stock',
        'weight'
    ];

    public function product(){
        return $this->belongsTo(Product::class);
    }
    public function color(){
        return $this->belongsTo(ProductColor::class, 'color_id');
    }

    public function size(){
        return $this->belongsTo(Sizes::class, 'size_id');
    }
}
