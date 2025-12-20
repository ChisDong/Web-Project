<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Sizes;

class SizeController extends Controller
{
    public function getSizeById($size_id){
        $size = Sizes::findOrFail($size_id);
        return response()->json([
            'status' => 'success',
            'data' => $size,
        ]);
    }
}
