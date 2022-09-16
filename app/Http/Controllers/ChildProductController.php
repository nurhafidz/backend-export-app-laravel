<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ChildProduct;
use Illuminate\Support\Facades\Validator;
use App\Models\Product;
use App\Models\Media;

class ChildProductController extends Controller
{
    public function storeSingleData(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'media_id' => 'array|nullable',
            'product_id' => 'required|uuid',
            'title' => 'required|string|max:255',
            'price' => 'required',
            'details' => 'required',
            'description' => 'string|nullable',
            'location' => 'string|nullable',
            'minimum' => 'integer|nullable',
            'status' => 'boolean|nullable',
        ]);
        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => $validator->errors()->all(),
            ], 422);
        }
        try {
            Product::findOrFail($request->product_id);
            $media = Media::findOrFail($request->media_id);

            $childProduct = ChildProduct::create($request->only(
                'product_id',
                'title',
                'price',
                'description',
                'details',
                'location',
                'minimum',
                'status',
            ));
            $childProduct->medias()->attach($media);
            return response()->json([
                'status' => 'success',
                'message' => 'Product successfully created',
                'data' => $childProduct,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage(),
            ], 400);
        }
    }
}
