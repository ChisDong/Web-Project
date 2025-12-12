<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CustomerAddresses extends Model
{
    protected $table = 'customer_addresses';
    protected $primaryKey = 'address_id';

    public $incrementing = true;
    protected $keyType = 'int';
    protected $fillable = [
        'customer_id',
        'address_line',
        'ward',
        'district',
        'city',
        'country',
    ];
    public function user(){
        return $this->belongsTo(User::class);
    }
}
