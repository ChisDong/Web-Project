<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderItems extends Model
{
    use HasFactory;

    protected $tab_productVariants = 'ProductVariants';
    protected $tab_product = 'Products';

    protected $fillable = [
        'order_id',
        'variant_id',
        'quantity',
        'price',
    ];

    public function productVariant(){
        return $this->belongsTo(ProductVariant::class, 'variant_id');
    }
    public function order(){
        return $this->belongsTo(Order::class, 'order_id');
    }

}
