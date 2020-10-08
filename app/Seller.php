<?php

namespace App;

use App\Product;
use App\Scopes\SellerScope;
use App\Transformers\SellerTransformer;
use Illuminate\Database\Eloquent\Model;

class Seller extends User
{
    public $transformer = SellerTransformer::class;


    protected static function boot()
    {
        //con este metodo solo llamamos a los buyers que tengan transacciones haciendo referencia al archivo BuyerScope.php
        parent::boot();

        static::addGlobalScope(new SellerScope);
    }

    public function products()
    {
        return $this->hasMany(Product::class);
    }
}
