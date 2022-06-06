<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Couponexcludeproduct extends Model
{
    protected $fillable = [
        'id ', 'coupon_id', 'product_id','active','created_at', 'updated_at', 'deleted_at'
    ];
}
