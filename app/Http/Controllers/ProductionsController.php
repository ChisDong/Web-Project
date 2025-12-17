<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Category;
use App\Models\Collection;
use App\Models\ProductColor;
use App\Models\Product;
use App\Models\ProductReview;
use App\Http\Requests\ProductRequest;
use App\Models\ProductImage;
use App\Http\Requests\ProductDiscountRequest;
use App\Models\Discount;
use App\Http\Requests\ProductFaqRequest;
use App\Models\ProductFaq;
use App\Http\Requests\ProductHighlightRequest;
use App\Models\ProductHighlight;
use App\Models\ProductVariant;

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

    public function search_by_name(Request $request){
        $searchTerm = $request->input('query');

        $products = Product::where('name', 'LIKE', '%' . $searchTerm . '%')->get();
        $product_images = Product::with('main_image')->where('name', 'LIKE', '%' . $searchTerm . '%')->get();

        return response()->json([
            'status' => 'success',
            'data' => $products,
            'images' => $product_images,
        ]);
    }
    public function getProductVariants($id){
        $product_Variants = ProductVariant::where('product_id', $id)->get();
        return response()->json([
            'status' => 'success',
            'data' => $product_Variants,
        ]);
    }

    //API POST FOR ADMIN DASHBOARD

    public function postProduct(ProductRequest $request){
        $data = $request->validated();

        $product = Product::create($data);

        return response()->json([
            'status' => 'success',
            'data' => $product,
        ]);
    }
    // sửa lại chỗ này
    public function postProductImage(Request $request){
        $data = $request->validate(
            [
                'product_id' => 'required|exists:products,id',
                'image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:4096',
                'role' => 'nullable|string|max:50',
            ]
        );

        $imagePath = null;

        if($request->hasFile('image')){
            $imagePath = $request->file('image')->store('product_images', 'public');
        }

        $productImage = ProductImage::create([
            'product_id' => $data['product_id'],
            'image_url' => asset('storage/'.$imagePath),
            'role' => $data['role'] ?? null,
        ]);

        return response()->json([
            'status' => 'success',
            'data' => $productImage,
        ]);
    }
    public function postDicount(ProductDiscountRequest $request){
        $data = $request->validated();

        $productDiscount = Discount::create($data);

        return response()->json([
            'status' => 'success',
            'data' => $productDiscount,
        ]);
    }

    public function postProductColor(Request $request){
        $data = $request->all();

        $productColor = ProductColor::create($data);

        return response()->json([
            'status' => 'success',
            'data' => $productColor,
        ]);
    }

    public function postProductFaq(ProductFaqRequest $request){
        $data = $request->validated();
        $maxOrder = ProductFaq::where('product_id', $data['product_id'])->max('sort_order');
        $data['sort_order'] = $maxOrder + 1;
        $productFaq = ProductFaq::create($data);

        return response()->json([
            'status' => 'success',
            'data' => $productFaq,
        ]);
    }

    public function postHighLight(ProductHighlightRequest $request){
        $data = $request->validated();
        $maxOrder = ProductHighlight::where('product_id', $data['product_id'])->max('sort_order');
        $data['sort_order'] = $maxOrder + 1;
        $productHighlight = ProductHighlight::create($data);

        return response()->json([
            'status' => 'success',
            'data' => $productHighlight,
        ]);
    }

}
