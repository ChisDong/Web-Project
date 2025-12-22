<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProductColorRequest;
use App\Models\ProductColor;
use Illuminate\Http\Request;

class ProductColorController extends Controller
{
    // Function get all product colors
    public function getAllProductColors(ProductColor $productColor){
        $productColors = $productColor::with('products')->get();
        $data = $productColors->map(function ($it) {
            $p = $it->products;
            return [
                'id' => $it->id,
                'main_image' => $it->main_image,
                'product_id' => $it->product_id,
                'color_code' => $it->color_code,
                'status' => $it->status,
                'product_name' => $p?->name,
            ];
        });
        return response()->json([
            'status' => 'success',
            'data' => $data,
        ]);

    }

    // Function post a new product color
    public function postProductColor(ProductColorRequest $request){
        $data = $request->validated();
        $productColor = ProductColor::create($data);
        $imagePath = null;
        if($request->hasFile('image')){
            $imagePath = $request->file('image')->store('productcolor_images', 'public');
            $productColor->main_image = asset('storage/'.$imagePath);
            $productColor->save();

        }
        return response()->json([
            'status' => 'success',
            'data' => $imagePath,
        ]);
    }
    // Function update  product color status
    public function updateProductColorStatus($product_color_id){

        $productColor = ProductColor::findOrFail($product_color_id);
        $productColor->status = 'deactive';
        $productColor->save();

        return response()->json([
            'status' => 'success',
            'data' => $productColor,
        ]);
    }

    public function updateProductColor(Request $request, $product_color_id){
        $data = $request->validate([
            'product_id' => 'nullable|exists:products,id',
            'color_name' => 'nullable|string|max:100',
            'color_code' => 'nullable|string|max:7',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:4096',
            ]
        );
        $productColor = ProductColor::findOrFail($product_color_id);

        if($request->hasFile('image')){
            $imagePath = $request->file('image')->store('productcolor_images', 'public');
            $productColor->main_image = asset('storage/'.$imagePath);
        }else{
            $productColor->main_image = $productColor->main_image;
        }

        $productColor->color_name = $data['color_name'] == null ? $productColor->color_name : $data['color_name'];
        $productColor->color_code = $data['color_code'] == null ? $productColor->color_code : $data['color_code'];
        $productColor->product_id = $data['product_id'] == null ? $productColor->product_id : $data['product_id'];
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
