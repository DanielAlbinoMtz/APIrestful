<?php

namespace App\Http\Controllers\Transaction;

use App\Http\Controllers\ApiController;
use App\Transaction;
use Illuminate\Http\Request;

class TransactionCategoryController extends ApiController
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Transaction $transaction)
    {
       /*  las categorias del producto involucrado son las categorias de esa transacction misma */
       $categories = $transaction->product->categories;
       return $this->showAll($categories);
    }

    
}
