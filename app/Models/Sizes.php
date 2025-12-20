<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Sizes extends Model
{
    use HasFactory;

    public function productVariants(){
        return $this->hasMany(ProductVariant::class, 'size_id');
    }
}
