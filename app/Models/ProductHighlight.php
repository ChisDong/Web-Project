<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductHighlight extends Model
{
    use HasFactory;
    protected $fillable = [
        'product_id',
        'title',
        'description',
        'image_url',
        'sort_order',
    ];
    
    public function products(){
        return $this->belongsTo(Product::class);
    }
}
