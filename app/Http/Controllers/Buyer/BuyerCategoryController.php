<?php

namespace App\Http\Controllers\Buyer;

use App\Buyer;
use App\Http\Controllers\ApiController;
use Illuminate\Http\Request;

class BuyerCategoryController extends ApiController
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Buyer $buyer)
    {
        $categories = $buyer->transactions()->with('product.categories')
        ->get()
        ->pluck('product.categories')
        ->collapse()         //juntar una sola lista unica de categorias y que no se repitan
        ->unique('id') //juntar una sola lista
        ->values() ;        //si hay repetidas values se encarga de que no queden espacios en blanco

       return $this->showAll($categories);
    }

}
