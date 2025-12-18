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
}
