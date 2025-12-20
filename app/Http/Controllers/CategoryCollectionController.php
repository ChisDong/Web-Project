<?php

namespace App\Http\Controllers;

use App\Http\Requests\CollectionRequest;
use App\Http\Requests\CategoryRequest;
use Illuminate\Http\Request;
use App\Models\Category;
use App\Models\Collection;

class CategoryCollectionController extends Controller
{
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

        return response()->json([
            'status' => 'success',
            'data' => $collection,
        ]);
    }
    public function updateCategoryStatus($category_id){

        $category = Category::findOrFail($category_id);
        $category->status = 'deactivate';
        $category->save();

        return response()->json([
            'status' => 'success',
            'data' => $category,
        ]);
    }
    public function updateCollectionStatus($collection_id){

        $collection = Collection::findOrFail($collection_id);
        $collection->status = 'deactivate';
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
            'slug' => 'sometimes|in:active,deactivate',
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
            'slug' => 'sometimes|in:active,deactivate',
            'image' => 'sometimes|image|max:4086',
        ]);
        $imagePath = null;

        if($request->hasFile('image')){
            $imagePath = $request->file('image')->store('collection_images', 'public');
        }

        $collection = Collection::findOrFail($collection_id);
        $collection->name = $data['name'] ?? $collection->name;
        $collection->description = $data['description'] ?? $collection->description;
        $collection->slug = $data['slug'] ?? $collection->slug;
        $collection->image = $imagePath ? asset('storage/'.$imagePath) : $collection->image;
        $collection->save();

        return response()->json([
            'status' => 'success',
            'data' => $collection,
        ]);
    }
}
