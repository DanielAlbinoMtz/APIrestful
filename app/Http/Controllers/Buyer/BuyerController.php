<?php

namespace App\Http\Controllers\Buyer;

use App\Buyer;
use App\Http\Controllers\ApiController;
use Illuminate\Http\Request;

class BuyerController extends ApiController
{
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
    

    public function show($id)
    {
        $compradores = Buyer::has('transactions')->findOrFail($id);
        return $this->showOne($compradores);
        /* return response()->json(['data'=> $compradores],200); */
    }
}
   