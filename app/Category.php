<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Category extends Model
{
    use SoftDeletes;

    protected $fillable = ['name', 'slug'];

    protected $hidden = ['pivot'];

    public function products()
    {
        return $this->belongsToMany('App\Product', 'product_categories');
    }
}
