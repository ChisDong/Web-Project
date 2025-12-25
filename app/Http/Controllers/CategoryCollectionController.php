<?php

namespace App\Http\Controllers;

use App\Http\Requests\CollectionRequest;
use App\Http\Requests\CategoryRequest;
use Illuminate\Http\Request;
use App\Models\Category;
use App\Models\Collection;

class CategoryCollectionController extends Controller
{
    public function getAllCategories(){
        $categories = Category::all();

        return response()->json([
            'status' => 'success',
            'data' => $categories,
        ]);
    }
    public function getAllCollections(){
        $collections = Collection::all();

        return response()->json([
            'status' => 'success',
            'data' => $collections,
        ]);
    }
    public function postCategory(CategoryRequest $request){
        $data = $request->validated();

        $category = Category::create($data);

        return response()->json([
            'status' => 'success',
            'data' => $category,
        ]);
    }

    public function postCollection(CollectionRequest $request){
        $data = $request->validated();

        $collection = Collection::create($data);

        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('collection_images', 'public');
            $collection->banner = asset('storage/' . $imagePath);
            $collection->save();
        }

        return response()->json([
            'status' => 'success',
            'data' => $collection,
        ]);
    }

    public function updateCategoryStatus(Request $request, $category_id){
        $data = $request->validate([
            'status' => 'required|in:active,deactive',
        ]);

        $category = Category::findOrFail($category_id);
        $category->status = $data['status'];
        $category->save();

        return response()->json([
            'status' => 'success',
            'data' => $category,
        ]);
    }
    public function updateCollectionStatus(Request $request, $collection_id){
        $data = $request->validate([
            'status' => 'required|in:active,deactive',
        ]);

        $collection = Collection::findOrFail($collection_id);
        $collection->status = $data['status'];

        $collection->save();

        return response()->json([
            'status' => 'success',
            'data' => $collection,
        ]);
    }

    public function updateCategory(Request $request, $category_id){
        $data = $request->validate([
            'name' => 'sometimes|string|max:255',
            'description' => 'sometimes|string',
            'slug' => 'string|max:255|unique:categories,slug,' . $category_id,
        ]);
        $category = Category::findOrFail($category_id);
        $category->name = $data['name'] ?? $category->name;
        $category->description = $data['description'] ?? $category->description;
        $category->slug = $data['slug'] ?? $category->slug;
        $category->save();

        return response()->json([
            'status' => 'success',
            'data' => $category,
        ]);
    }

    public function updateCollection(Request $request, $collection_id){
        $data = $request->validate([
            'name' => 'sometimes|string|max:255',
            'description' => 'sometimes|string',
            'slug' => 'string|max:255|unique:categories,slug,' . $collection_id,
            'banner' => 'sometimes|image|max:4086',
        ]);
        $imagePath = null;

        if($request->hasFile('banner')){
            $imagePath = $request->file('banner')->store('collection_images', 'public');
        }

        $collection = Collection::findOrFail($collection_id);
        $collection->name = $data['name'] ?? $collection->name;
        $collection->description = $data['description'] ?? $collection->description;
        $collection->slug = $data['slug'] ?? $collection->slug;
        $collection->banner = $imagePath ? asset('storage/'.$imagePath) : $collection->banner;
        $collection->save();

        return response()->json([
            'status' => 'success',
            'data' => $collection,
        ]);
    }

    public function deleteCategory($category_id){
        $category = Category::findOrFail($category_id);
        $category->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Category deleted successfully',
        ]);
    }

    public function deleteCollection($collection_id){
        $collection = Collection::findOrFail($collection_id);
        $collection->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Collection deleted successfully',
        ]);
    }

    public function getAllCategoryReturnId(){
        $categories = Category::all(['id', 'name']);

        return response()->json([
            'status' => 'success',
            'data' => $categories,
        ]);
    }

    public function getAllCollectionReturnId(){
        $collections = Collection::all(['id', 'name']);

        return response()->json([
            'status' => 'success',
            'data' => $collections,
        ]);
    }

    public function getCategoryByname(Request $request){
        $data = $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $category = Category::where('name', $data['name'])->first();

        return response()->json([
            'status' => 'success',
            'data' => $category->id,
        ]);
    }

    public function getCollectionByname(Request $request){
        $data = $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $collection = Collection::where('name', $data['name'])->first();

        return response()->json([
            'status' => 'success',
            'data' => $collection->id,
        ]);
    }
}
