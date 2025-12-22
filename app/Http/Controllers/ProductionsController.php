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
    public function getProductByName(Request $request){
        $name = $request->input('name');
        $product = Product::where('name', $name)->firstOrFail();
        return response()->json([
            'status' => 'success',
            'data' => $product,
        ]);
    }
    //     $product = Product::findOrFail($id);
    //     return response()->json([
    //         'status' => 'success',
    //         'data' => $product->name,
    //     ]);
    // }
    public function getAllProductsNamesId(){
        $products = Product::all(['id', 'name']);
        return response()->json([
            'status' => 'success',
            'data' => $products,
        ]);
    }

    public function index_product(Product $product){
        $products = Product::all();
        return response()->json([
            'status' => 'success',
            'data' => $products,
        ]);
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

    public function getAllProducts(){
        $products = Product::with('category.products', 'collection.products')->get();
        $data = $products->map(function ($it) {
            $c = $it->category;
            $co = $it->collection;
            return [
                'id' => $it->id,
                'name' => $it->name,
                'slug' => $it->slug,
                'description' => $it->description,
                'base_price' => $it->base_price,
                'discount_percent' => $it->discount_percent,
                'gender' => $it->gender,
                'status' => $it->status,
                'category_id' => $it->category_id,
                'collection_id' => $it->collection_id,
                'category_name' => $c?->name,
                'collection_name' => $co?->name,
            ];
        });
        return response()->json([
            'status' => 'success',
            'data' => $data,
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

    public function getAllProductsImages(){
        $productImages = ProductImage::with('products')->get();
        $data = $productImages->map(function ($it) {
            $p = $it->products;
            return [
                'id' => $it->id,
                'image' => $it->image_url,
                'product_id' => $it->product_id,
                'role' => $it->role,
                'product_name' => $p?->name,
            ];
        });
        return response()->json([
            'status' => 'success',
            'data' => $data,
        ]);
    }

    public function deleteProductImage($image_id){
        $productImage = ProductImage::findOrFail($image_id);
        $productImage->delete();
        return response()->json([
            'status' => 'success',
            'message' => 'Product image deleted successfully',
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
        $imagePath = null;
        if($request->hasFile('image')){
            $imagePath = $request->file('image')->store('productHighlight_images', 'public');
            $data['image_url'] = asset('storage/'.$imagePath);
        }
        $maxOrder = ProductHighlight::where('product_id', $data['product_id'])->max('sort_order');
        $data['sort_order'] = $maxOrder + 1;
        $productHighlight = ProductHighlight::create($data);
        return response()->json([
            'status' => 'success',
            'data' => $productHighlight,
        ]);
    }

    public function putProductStatus($product_id){
        $product = Product::findOrFail($product_id);
        $product->status = 'deactive';
        $product->save();
        return response()->json([
            'status' => 'success',
            'data' => $product,
        ]);
    }

    public function updateProduct(Request $request, $product_id){
        $data = $request->validate([
            'category_id' => 'sometimes|exists:categories,id',
            'collection_id' => 'sometimes|exists:collections,id',
            'name' => 'sometimes|string|max:255',
            'slug' => 'sometimes|string|max:255|unique:products,slug,'.$product_id,
            'description' => 'sometimes|string',
            'base_price' => 'sometimes|numeric|min:0',
            'discount_percent' => 'sometimes|numeric|min:0|max:100',
        ]);

        $product = Product::findOrFail($product_id);
        $product->category_id = $data['category_id'] == null ? $product->category_id : $data['category_id'];
        $product->collection_id = $data['collection_id'] == null ? $product->collection_id : $data['collection_id'];
        $product->name = $data['name'] == null ? $product->name : $data['name'];
        $product->slug = $data['slug'] == null ? $product->slug : $data['slug'];
        $product->description = $data['description'] == null ? $product->description : $data['description'];
        $product->base_price = $data['base_price'] == null ? $product->base_price : $data['base_price'];
        $product->discount_percent = $data['discount_percent'] == null ? $product->discount_percent : $data['discount_percent'];
        $product->save();

        return response()->json([
            'status' => 'success',
            'data' => $product,
        ]);
    }

    public function deleteProduct($product_id){
        $product = Product::findOrFail($product_id);
        $product->delete();
        return response()->json([
            'status' => 'success',
            'message' => 'Product deleted successfully',
        ]);
    }
    public function updateProductStatus(Request $request, $product_id){
        $data = $request->validate([
            'status' => 'required|in:active,deactive',
        ]);

        $product = Product::findOrFail($product_id);
        $product->status = $data['status'];
        $product->save();

        return response()->json([
            'status' => 'success',
            'data' => $product,
        ]);
    }
}
