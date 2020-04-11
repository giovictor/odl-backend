<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model
{
    use SoftDeletes;

    public function categories()
    {
        return $this->belongsToMany('App\Category', 'product_categories');
    }

    public function orders()
    {
        return $this->belongsToMany('App\Order', 'order_details');
    }
}
