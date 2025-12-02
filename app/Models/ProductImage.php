<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductImage extends Model
{
    use hasFactory;

    protected $fillable = [
        'product_id',
        'image_url',
        'role',
    ];

    public function products(){
        return $this->belongsTo(Product::class, 'product_id');
    }
}
