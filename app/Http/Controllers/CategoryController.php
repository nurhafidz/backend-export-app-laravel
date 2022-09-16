<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use App\Http\Requests\CategoryRequest;

class CategoryController extends Controller
{
    public function index()
    {
        $categories = Category::whereNull('parent_id')->with('child')->get();
        return response()->json([
            'status' => 'success',
            'message' => 'Category was successfully retrieved',
            'data' => $categories
        ], 200);
    }

    public function storeSingleData(CategoryRequest $request)
    {
        if ($request->parent_id) {
            try {
                $category = Category::findOrFail($request->parent_id);
            } catch (\Throwable $th) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Parent id could not be found',
                ], 400);
            }
            $category = Category::create([
                'title' => Str::lower($request->title),
                'type' => Str::lower($request->type),
                'parent_id' => $request->parent_id
            ]);
        } else {
            $category = Category::create([
                'title' => Str::lower($request->title),
                'type' => Str::lower($request->type),
            ]);
        }
        return response()->json([
            'status' => 'success',
            'message' => 'Category created successfully',
            'data' => $category
        ], 200);
    }

    public function update(CategoryRequest $request, $id)
    {
        try {

            $getCategory = Category::findOrFail($id);

            if ($request->parent_id) {
                try {
                    Category::findOrFail($request->parent_id);
                } catch (\Throwable $th) {
                    return response()->json([
                        'status' => 'error',
                        'message' => 'Parent id could not be found',
                    ], 400);
                }
                $getCategory->update([
                    'title' => Str::lower($request->title),
                    'type' => Str::lower($request->type),
                    'parent_id' => $request->parent_id
                ]);
            } else {
                $getCategory->update([
                    'title' => Str::lower($request->title),
                    'type' => Str::lower($request->type),
                ]);
            }
            return response()->json([
                'status' => 'success',
                'message' => 'Category update successfully',
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => 'error',
                'message' => 'Id could not be found',
            ], 400);
        }
    }

    public function destroy(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'id' => 'required|string',
        ]);
        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => $validator->errors()->all(),
            ], 422);
        }

        $idCategory = explode(",", $request->id);

        //remove same id
        $idCategory = array_unique($idCategory);

        try {
            foreach ($idCategory as $category) {
                Category::FindOrFail($category);
            }

            Category::destroy($idCategory);

            return response()->json([
                'status' => 'success',
                'message' => 'Category destroy successfully',
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Id category does not exist',
            ], 400);
        }
    }

    public function searchById(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id' => 'required',
        ]);
        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => $validator->errors()->all(),
            ], 422);
        }

        try {
            $category = Category::where('id', $request->id)->with('child')->first();
            // dd($category);
            return response()->json([
                'status' => 'success',
                'message' => 'Category was successfully retrieved',
                'data' => $category
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => 'error',
                'message' => 'Category not found',
            ], 400);
        }
    }
    public function searchBySimilarTitle(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required|string',
        ]);
        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => $validator->errors()->all(),
            ], 422);
        }
        try {
            $category = Category::where('title', 'LIKE', '%' . $request->title . '%')->with('child')->get();
            return response()->json([
                'status' => 'success',
                'message' => 'Category was successfully retrieved',
                'data' => $category
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => 'error',
                'message' => 'Category not found',
            ], 400);
        }
    }
    public function searchByType(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'type' => 'required|string',
        ]);
        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => $validator->errors()->all(),
            ], 422);
        }
        try {
            $category = Category::where('type', $request->type)->with('child')->get();
            return response()->json([
                'status' => 'success',
                'message' => 'Category was successfully retrieved',
                'data' => $category
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => 'error',
                'message' => 'Category not found',
            ], 400);
        }
    }
}
