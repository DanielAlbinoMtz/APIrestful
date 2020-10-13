<?php

namespace App\Http\Controllers\Seller;

use App\Http\Controllers\ApiController;
use App\Seller;
use Illuminate\Http\Request;

class SellerController extends ApiController
{
    public function __construct()
    {
        parent::__construct();
        $this->middleware('scope:read-general')->only('show');
        $this->middleware('can:view,seller')->only('show');

    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $this->allowedAdminAction();

        $vendedores = Seller::has('products')->get();

        /* return response()->json(['data'=> $vendedores],200); */
        /* con traits */
        return $this->showAll($vendedores);
    }

    public function show(Seller $seller)
    {
        /*  $vendedores = Seller::has('products')->findOrFail($id); */
        return $this->showOne($seller);
        /* return response()->json(['data'=> $vendedores],200); */
    }
}
