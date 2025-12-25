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
    // Append computed URL for responses
    protected $appends = ['main_image_url'];
    public function products(){
        return $this->belongsTo(Product::class, 'product_id');
    }

    public function getMainImageUrlAttribute()
    {
        if (!$this->main_image) {
            return null;
        }
        // If main_image already looks like a full URL, return it
        if (str_starts_with($this->main_image, 'http')) {
            return $this->main_image;
        }
        return url('storage/' . $this->main_image);
    }
}
