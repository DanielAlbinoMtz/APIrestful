<?php

namespace App\Http\Controllers\Product;

use App\Http\Controllers\ApiController;
use App\Product;
use Illuminate\Http\Request;

class ProductTransactionController extends ApiController
{
    public function __construct()
    {
        parent::__construct();
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Product $product) //transacciones del producto con id 1
    {
        $this->allowedAdminAction();

        $transaction = $product -> transactions;

        return $this->showAll($transaction);
    }

   
}
