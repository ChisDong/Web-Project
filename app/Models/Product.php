<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    public function category(){
        return $this->belongsTo(Category::class);
    }

    public function collection(){
        return $this->belongsTo(Collection::class);
    }

    public function colors(){
        return $this->hasMany(ProductColor::class);
    }
    public function reviews(){
        return $this->hasMany(ProductReview::class);
    }

    public function images(){
        return $this->hasMany(ProductImage::class);
    }

    public function highlights(){
        return $this->hasMany(ProductHighlight::class);
    }

    public function faqs(){
        return $this->hasMany(ProductFaq::class);
    }
}

