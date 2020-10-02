<?php

namespace App\Http\Controllers\Buyer;

use App\Buyer;
use App\Http\Controllers\ApiController;
use Illuminate\Http\Request;


class BuyerProductController extends ApiController
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Buyer $buyer)
    {
        //Relacion uno a muchos
        $products = $buyer->transactions()->with('product')->get()->pluck('product');
        /* asi estamos obteniendo un query builder donde podemos mandar a llamar la consulta que nos muestar como respuesta los productos de una transaccion*/
        return $this->showAll($products);
    }
}
