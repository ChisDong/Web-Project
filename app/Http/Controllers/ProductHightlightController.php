<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ProductHighlight;
class ProductHightlightController extends Controller
{
    public function deleteProductHighlight($highlight_id){
        $highlight = ProductHighlight::findOrFail($highlight_id);
        $highlight->delete();
        return response()->json([
            'status' => 'success',
            'message' => 'Product highlight deleted successfully',
        ]);
    }
    public function updateProductHighlight(Request $request, $highlight_id){
        $highlight = ProductHighlight::findOrFail($highlight_id);
        $data = $request->validate([
            'product_id' => 'required|exists:products,id',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'sort_order' => 'nullable|integer',
            'image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:4096',
        ]);

        $imagePath = null;
        if($request->hasFile('image')){
            $imagePath = $request->file('image')->store('productHighlight_images', 'public');
        }
        $highlight->product_id = $data['product_id'] == null ? $highlight->product_id : $data['product_id'];
        $highlight->title = $data['title'] == null ? $highlight->title : $data['title'];
        $highlight->description = $data['description'] == null ? $highlight->description : $data['description'];
        $highlight->sort_order = $data['sort_order'] == null ? $highlight->sort_order : $data['sort_order'];
        $highlight->image_url = $imagePath == null ? $highlight->image_url : asset('storage/'.$imagePath);
        $highlight->save();
        return response()->json([
            'status' => 'success',
            'data' => $highlight,
        ]);
    }

}
