<?php

namespace App;

use App\Product;
use App\Scopes\SellerScope;
use Illuminate\Database\Eloquent\Model;

class Seller extends User
{

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
