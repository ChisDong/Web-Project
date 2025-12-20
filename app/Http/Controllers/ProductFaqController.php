<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ProductFaq;

class ProductFaqController extends Controller
{
    public function putProductFaq(Request $request, $faq_id){
        $faq = ProductFaq::findOrFail($faq_id);
        $data = $request->validate([
            'product_id' => 'required|exists:products,id',
            'question' => 'required|string|max:500',
            'answer' => 'required|string|max:1000',
        ]);
        $faq->product_id = $data['product_id'] == null ? $faq->product_id : $data['product_id'];
        $faq->question = $data['question'] == null ? $faq->question : $data['question'];
        $faq->answer = $data['answer'] == null ? $faq->answer : $data['answer'];
        $faq->save();
        return response()->json([
            'status' => 'success',
            'data' => $faq,
        ]);
    }

    public function deleteProductFaq($faq_id){
        $faq = ProductFaq::findOrFail($faq_id);
        $faq->delete();
        return response()->json([
            'status' => 'success',
            'message' => 'FAQ deleted successfully',
        ]);
    }
}
