<?php

namespace App;

use App\Scopes\BuyerScope;
use App\Transaction;
use Illuminate\Database\Eloquent\Model;

class Buyer extends User
{
    protected static function boot()
    {
        //con este metodo solo llamamos a los buyers que tengan transacciones haciendo referencia al archivo BuyerScope.php
        parent::boot();

        static::addGlobalScope(new BuyerScope);
    }

    public function transactions()
    {
        return $this->hasMany(Transaction::class);
    }
}
