<?php

namespace App\Http\Controllers\Buyer;

use App\Buyer;
use App\Http\Controllers\ApiController;
use Illuminate\Http\Request;

class BuyerController extends ApiController
{
    public function __construct()
    {
        parent::__construct();
        $this->middleware('scope:read-general')->only('show');

    }
    /**
     *
     * Display a listing of the resource.
     * @return \Illuminate\Http\Response
     */
    public function index()
     {
         $compradores = Buyer::has('transactions')->get();
   
        /* con traits */
        return $this->showAll($compradores);
        /* return response()->json(['data'=> $compradores],200); */
    }
    

    public function show(Buyer $buyer)
    {
       /*  $compradores = Buyer::has('transactions')->findOrFail($id); */
        return $this->showOne($buyer);
        /* return response()->json(['data'=> $compradores],200); */
    }
}
   