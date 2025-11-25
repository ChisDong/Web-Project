<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ProductionsController extends Controller
{
    public function index(){
        return view('products.index');
    }
}
