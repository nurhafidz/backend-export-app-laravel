<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Media;
use App\Models\Category;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class ProductController extends Controller
{


    public function createSingleProduct(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'category_id' => 'required|uuid',
            'media_id' => 'required|uuid',
            'title' => 'required|max:255',
            'description' => 'nullable',
            'divider_type' => 'required',
            'unit' => 'required',
            'status' => 'boolean|nullable',
        ]);
        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => $validator->errors()->all(),
            ], 400);
        }
        try {
            Category::findOrFail($request->category_id);
            Media::findOrFail($request->media_id);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage(),
            ], 400);
        }
        $request['slug'] = Str::slug($request->title);
        $product = Product::create($request->only('category_id', 'media_id', 'title', 'description', 'divider_type', 'unit', 'slug', 'status'));

        return response()->json([
            'status' => 'success',
            'message' => 'Product successfully created',
            'data' => $product,
        ], 200);
    }

    public function getAll()
    {
        $products = Product::with('category', 'media', 'childproduct')->get();
        return response()->json([
            'status' => 'success',
            'message' => 'Product successfully retrieved',
            'data' => $products,
        ], 200);
    }

    public function getProductList(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'status' => 'boolean|nullable',
        ]);
        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => $validator->errors()->all(),
            ], 400);
        }
        switch ($request->status) {
            case 1 || "1" || true:
                $products = Product::where('status', true)->with('category:id,title', 'media:id,link,alt', 'childproduct')->get();
                break;
            case 0 || "0" || false:
                $products = Product::where('status', false)->with('category:id,title', 'media:id,link,alt', 'childproduct')->get();
                break;
            default:
                $products = Product::with('category:id,title', 'media:id,link,alt', 'childproduct')->get();
                break;
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Product successfully retrieved',
            'data' => $products,
        ], 200);
    }

    public function filterProduct(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'category_id' => 'array|nullable',
            'sort' => 'nullable|in:newst,oldest,a-z,z-a'
        ]);
        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => $validator->errors()->all(),
            ], 400);
        }

        switch ($request->sort) {
            case 'newst':
                $products = Product::whereIn('category_id', $request->category_id)->sortBy('created_at')->get();
                break;
            case 'oldest':
                $products = Product::whereIn('category_id', $request->category_id)->sortDescBy('created_at')->get();
                break;
            case 'a-z':
                $products = Product::whereIn('category_id', $request->category_id)->sortBy('title')->get();
                break;
            case 'z-a':
                $products = Product::whereIn('category_id', $request->category_id)->sortDescBy('title')->get();
                break;
            default:
                $products = Product::whereIn('category_id', $request->category_id)->get();
                break;
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Product successfully created',
            'data' => $products,
        ], 200);
    }

    public function updateSingleProduct(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'category_id' => 'uuid',
            'media_id' => 'uuid',
            'title' => 'max:255',
            'description' => 'nullable',
            'divider_type' => 'required',
            'status' => 'boolean|nullable',
        ]);
        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => $validator->errors()->all(),
            ], 400);
        }
        try {
            if ($request->category_id) {
                Category::findOrFail($request->category_id);
            }
            if ($request->media_id) {
                Media::findOrFail($request->media_id);
            }
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage(),
            ], 400);
        }
        if ($request->title) $request['slug'] = Str::slug($request->title);

        try {
            $product = tap(Product::findOrFail($id))
                ->update($request->all())
                ->first();
            return response()->json([
                'status' => 'success',
                'message' => 'Product successfully update',
                'data' => $product,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage(),
            ], 400);
        }
    }

    public function destroySingleProduct(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'product_id' => 'required|uuid',
        ]);
        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => $validator->errors()->all(),
            ], 400);
        }
        try {
            Product::destroy($request->product_id);

            return response()->json([
                'status' => 'success',
                'message' => 'Product successfully destroy',
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage(),
            ], 400);
        }
    }

    public function findById(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'product_id' => 'required|uuid',
        ]);
        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => $validator->errors()->all(),
            ], 400);
        }
    }
}
