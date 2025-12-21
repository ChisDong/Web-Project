<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\ProductColor;
use Illuminate\Support\Str;

class ColorController extends Controller
{
    private function normalizeColorCode(string $code): string
    {
        $code = urldecode(trim($code));          // phòng trường hợp FE gửi %23xxxxxx
        $code = Str::lower($code);

        // nếu FE gửi "000000" -> "#000000"
        if ($code !== '' && $code[0] !== '#') {
            $code = '#'.$code;
        }

        return $code;
    }

    public function get_products_by_color($color_code)
    {
        $color_code = $this->normalizeColorCode($color_code);

        // validate nhẹ: "#"+6 hex
        if (!preg_match('/^#[0-9a-f]{6}$/', $color_code)) {
            return response()->json([
                'status' => 'error',
                'message' => 'Invalid color_code'
            ], 422);
        }

        $productIds = ProductColor::where('color_code', $color_code)
            ->pluck('product_id')
            ->unique()
            ->values()
            ->toArray();

        $products = Product::whereIn('id', $productIds)->get();

        return response()->json([
            'status' => 'success',
            'data' => $products,
        ]);
    }
}
