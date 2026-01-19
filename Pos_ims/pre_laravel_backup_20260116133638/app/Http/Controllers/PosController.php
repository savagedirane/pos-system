<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class PosController extends Controller
{
    public function index()
    {
        return view('pos.index');
    }

    public function addToCart(Request $request)
    {
        // placeholder: handle add to cart
        return response()->json(['ok' => true]);
    }
}
