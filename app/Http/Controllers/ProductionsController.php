<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Category;
use App\Models\Collection;
use App\Models\ProductColor;
use App\Models\Product;
use App\Models\ProductReview;

class ProductionsController extends Controller
{
    public function index(){
        return view('products.index');
    }

     public function index_category(Category $category){
        $products = $category->products()->get();
        return response()->json([
            'status' => 'success',
            'data' => $products,
        ]);
    }

    public function index_collection(Collection $collection){
        $products = $collection->products()->get();
        return response()->json([
            'status' => 'success',  
            'data' => $products,
        ]);
    }

    public function get_colors($id){
      $products = Product::with('colors')->findOrFail($id);
      $colorsCodes = $products->colors->pluck('color_code')->toArray();
      return $colorsCodes;
    }

    public function get_products_by_color($id){
      $colorsCodes = $this->get_colors($id);
      foreach($colorsCodes as $code){
        $productsByColor[] = ProductColor::with('products')->where('color_code', $code)->get();
      }
      return response()->json([
          'status' => 'success',  
          'data' => $productsByColor,
      ]);
    }

   public function get_reviews($id){
       $products = Product::with('reviews')->findOrFail($id);
        $avgRating = $products->reviews->avg('rating');
       return response()->json([
        'status'      => 'success',
        'product_id'  => $products->id,
        'avg_rating'  => round($avgRating, 1),
        'total_reviews' => $products->reviews->count(),
        'reviews'     => $products->reviews,
       ]);
   }

   public function get_images($id){
       $products = Product::with('images')->findOrFail($id);
       return response()->json([
        'status'      => 'success',
        'product_id'  => $products->id,
        'images'     => $products->images,
       ]);
   }

   public function get_highlights($id){
       $products = Product::with('highlights')->findOrFail($id);
       return response()->json([
        'status'      => 'success',
        'product_id'  => $products->id,
        'highlights'  => $products->highlights,
       ]);
   }

    public function get_faqs($id){
         $products = Product::with('faqs')->findOrFail($id);
         return response()->json([
          'status'      => 'success',
          'product_id'  => $products->id,
          'faqs'        => $products->faqs,
         ]);
    }
}
