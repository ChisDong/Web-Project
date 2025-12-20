<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'total_price',
        'status' => 'pending',
        'address',
        'shipping_addressline',
        'shipping_ward',
        'shipping_district',
        'shipping_city',
        'payment_method',
    ];

    public function orderItems(){
        return $this->hasMany(OrderItems::class, 'order_id');
    }

    public function user(){
        return $this->belongsTo(User::class);
    }

}
