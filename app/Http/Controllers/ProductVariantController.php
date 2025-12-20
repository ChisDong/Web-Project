<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ProductVariant;

class ProductVariantController extends Controller
{
    //Public function get all product variants
    public function getAllProductVariants(){
        $productVariants = ProductVariant::all();
        return response()->json([
            'status' => 'success',
            'data' => $productVariants,
        ]);
    }
    //Public function change product variant status
    public function updateProductVariantStatus( $id)
    {
        $productVariant = ProductVariant::find($id);
        if (!$productVariant) {
            return response()->json(['status' => 'error', 'message' => 'Product variant not found'], 404);
        }
        $productVariant->status = 'deactivate';
        $productVariant->save();
        return response()->json(['status' => 'success', 'data' => $productVariant]);
    }
    //Public function delete product variant
    public function deleteProductVariant( $id)
    {
        $productVariant = ProductVariant::find($id);
        if (!$productVariant) {
            return response()->json(['status' => 'error', 'message' => 'Product variant not found'], 404);
        }
        $productVariant->delete();
        return response()->json(['status' => 'success', 'message' => 'Product variant deleted successfully']);
    }
    //Public function update product variant
    public function updateProductVariant(Request $request, $variant_id)
    {
        $productVariant = ProductVariant::findOrFail($variant_id);
        $data = $request->validate([
            'product_id' => 'required|exists:products,id',
            'variant_name' => 'required|string|max:255',
            'sku' => 'required|string|max:100|unique:product_variants,sku,' . $variant_id,
            'price' => 'required|numeric|min:0',
            'stock' => 'required|integer|min:0',
            'weight' => 'required|numeric|min:0',
        ]);

        $productVariant->product_id = $data['product_id'] == null ? $productVariant->product_id : $data['product_id'];
        $productVariant->variant_name = $data['variant_name'] == null ? $productVariant->variant_name : $data['variant_name'];
        $productVariant->sku = $data['sku'] == null ? $productVariant->sku : $data['sku'];
        $productVariant->price = $data['price'] == null ? $productVariant->price : $data['price'];
        $productVariant->stock = $data['stock'] == null ? $productVariant->stock : $data['stock'];
        $productVariant->weight = $data['weight'] == null ? $productVariant->weight : $data['weight'];
        $productVariant->save();

        return response()->json([
            'status' => 'success',
            'data' => $productVariant,
        ]);
    }

    //Public function post product variant
    public function postProductVariant(Request $request)
    {
        $data = $request->validate([
            'product_id' => 'required|exists:products,id',
            'variant_name' => 'required|string|max:255',
            'sku' => 'required|string|max:100|unique:product_variants,sku',
            'price' => 'required|numeric|min:0',
            'stock' => 'required|integer|min:0',
            'weight' => 'required|numeric|min:0',
        ]);
        $productVariant = ProductVariant::create($data);
        return response()->json([
            'status' => 'success',
            'data' => $productVariant,
        ]);
    }

    public function getProductVariantById($variant_id){
        $productVariant = ProductVariant::findOrFail($variant_id);
        return response()->json([
            'status' => 'success',
            'data' => $productVariant,
        ]);
    }

}
