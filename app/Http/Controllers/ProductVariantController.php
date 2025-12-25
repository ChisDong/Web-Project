<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ProductVariant;

class ProductVariantController extends Controller
{
    //Public function get all product variants
    public function getAllProductVariants(){
        $productVariants = ProductVariant::with('product', 'color', 'size')->get();
        $data = $productVariants->map(function ($item) {
            $p = $item->product;
            $c = $item->color;
            $s = $item->size;
            return [
                'id' => $item->id,
                'product_id' => $item->product_id,
                'color_id' => $item->color_id,
                'size_id' => $item->size_id,
                'sku' => $item->sku,
                'price' => $item->price,
                'stock' => $item->stock,
                'weight' => $item->weight,
                'status' => $item->status,
                'product_name' => $p?->name,
                'product_price' => $p?->base_price,
                'color' => $c?->color_name,
                'size_id' => $s?->id,
                'size_name' => $s?->size_name,
            ];
        });
        return response()->json([
            'status' => 'success',
            'data' => $data,
        ]);
    }
    //Public function change product variant status
    public function updateProductVariantStatus( $id)
    {
        $productVariant = ProductVariant::find($id);
        if (!$productVariant) {
            return response()->json(['status' => 'error', 'message' => 'Product variant not found'], 404);
        }
        $productVariant->status = 'deactive';
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
            'product_id' => 'nullable|exists:products,id',
            'color_id' => 'nullable|exists:product_colors,id',
            'size_id' => 'nullable|exists:sizes,id',
            'sku' => 'nullable|string|max:100|unique:product_variants,sku,' . $variant_id,
            'price' => 'nullable|numeric|min:0',
            'stock' => 'nullable|integer|min:0',
            'weight' => 'nullable|numeric|min:0',
        ]);

        $productVariant->product_id = $data['product_id'] ?? $productVariant->product_id;
        $productVariant->color_id = $data['color_id'] ?? $productVariant->color_id;
        $productVariant->size_id = $data['size_id'] ?? $productVariant->size_id;
        $productVariant->sku = $data['sku'] ?? $productVariant->sku;
        $productVariant->price = $data['price'] ?? $productVariant->price;
        $productVariant->stock = $data['stock'] ?? $productVariant->stock;
        $productVariant->weight = $data['weight'] ?? $productVariant->weight;
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
            'color_id' => 'required|exists:product_colors,id',
            'size_id' => 'required|exists:sizes,id',
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

    public function getVariantsIdBycolorIdAndSizeId(Request $request){
        $data = $request->validate([
            'product_id' => 'required|exists:products,id',
            'color_id' => 'required|exists:product_colors,id',
            'size_id' => 'required|exists:product_sizes,id',
        ]);
        $productVariant = ProductVariant::where('product_id', $data['product_id'])
            ->where('color_id', $data['color_id'])
            ->where('size_id', $data['size_id'])
            ->first();

        if (!$productVariant) {
            return response()->json(['status' => 'error', 'message' => 'Product variant not found'], 404);
        }

        return response()->json([
            'status' => 'success',
            'data' => $productVariant,
        ]);
    }

}
