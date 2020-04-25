<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    protected $fillable = ['user_id', 'billing_address', 'shipping_address', 'birthdate', 'phone'];

    public function user()
    {
        return $this->belongsTo('App\User');
    }
}
