<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductColor extends Model
{
    use HasFactory;
    protected $fillable = [
        'product_id',
        'color_name',
        'color_code',
        'main_image',
    ];
    public function products(){
        return $this->belongsTo(Product::class);
    }
}
