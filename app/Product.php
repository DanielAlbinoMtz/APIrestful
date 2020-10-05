<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Seller;
use App\Transaction;
use App\Category;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model
{
    use SoftDeletes;
    const PRODUCTO_DISPONIBLE = 'disponible';
    const PRODUCTO_NO_DISPONIBLE = 'no disponible';
    protected $dates = ['deleted_at'];
    protected $fillable = [
        'name',
        'description',
        'quantity',
        'status', //disponible y no disponible
        'image',
        'seller_id'
    ];

    protected $hidden = [
        'pivot'
    ];
    /* funcion que determina si es disponible o no */

    public function estaDisponible()
    {
        return $this->status == Product::PRODUCTO_DISPONIBLE;
    }

    public function categories()
    {
        return $this->belongsToMany(Category::class);
    }

   /*  1 producto posee muchas transacciones ,tiene muchas transacciones hasMany*/   
    public function transactions()
    {
        return $this->hasMany(Transaction::class);
    }
    /* 1 producto tiene un vendedor, el vendedor posse muchos productos*/
    public function seller()
    {
        return $this->belongsTo(Seller::class);
    }
}
