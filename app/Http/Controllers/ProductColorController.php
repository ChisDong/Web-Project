<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProductColorRequest;
use App\Models\ProductColor;
use Illuminate\Http\Request;

class ProductColorController extends Controller
{
    // Function get all product colors
    public function getAllProductColors(ProductColor $productColor){
        $productColors = $productColor->all();
        return response()->json([
            'status' => 'success',
            'data' => $productColors,
        ]);

    }
    // Function post a new product color
    public function postProductColor(ProductColorRequest $request){
        $data = $request->validated();
        $imagePath = null;
        if($request->hasFile('image')){
            $imagePath = $request->file('image')->store('productcolor_images', 'public');
        }
        $productColor = ProductColor::create([
            'product_id' => $data['product_id'],
            'color_name' => $data['color_name'],
            'color_code' => $data['color_code'],
            'image_url' => asset('storage/'.$imagePath),
        ]);

        return response()->json([
            'status' => 'success',
            'data' => $productColor,
        ]);
    }
    // Function update  product color status
    public function updateProductColorStatus($product_color_id){

        $productColor = ProductColor::findOrFail($product_color_id);
        $productColor->status = 'deactivate';
        $productColor->save();

        return response()->json([
            'status' => 'success',
            'data' => $productColor,
        ]);
    }

    public function deleteProductColor($product_color_id){
        $productColor = ProductColor::findOrFail($product_color_id);
        $productColor->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Product color deleted successfully',
        ]);
    }

    public function getProductColorById($product_color_id){
        $productColor = ProductColor::findOrFail($product_color_id);
        return response()->json([
            'status' => 'success',
            'data' => $productColor,
        ]);
    }
}
