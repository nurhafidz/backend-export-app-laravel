<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Media;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;


class MediaController extends Controller
{

    public function storeSingleData(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'string|max:255|nullable',
            'alt' => 'string|max:255|nullable',
            'file' => 'image:jpeg,png,jpg,gif,svg|max:2048|nullable',
            'type' => 'required|in:image_product,file_product,image_blog,file_blog,link,image_index,image_galery,file',
            'status' => 'boolean|nullable'
        ]);
        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => $validator->errors()->all(),
            ], 400);
        }


        if ($request->type == "link") {
            $validator = Validator::make($request->all(), [
                'link' => 'required|url',
            ]);
            if ($validator->fails()) {
                return response()->json([
                    'status' => 'error',
                    'message' => $validator->errors()->all(),
                ], 400);
            }

            $media = Media::create([
                'title' => $request->title,
                'alt' => ($request->alt) ? $request->alt : $request->link,
                'link' => $request->link,
                'type' => $request->type,
                'status' => ($request->status) ? $request->status : true,
            ]);

            return response()->json([
                'status' => 'success',
                'message' => 'File Uploaded Successfully',
                'data' => $media
            ], 200);
        } else {
            $validator = Validator::make($request->all(), [
                'file' => 'required',
            ]);
            if ($validator->fails()) {
                return response()->json([
                    'status' => 'error',
                    'message' => $validator->errors()->all(),
                ], 400);
            }

            $image = $request->file('file');
            $uploadFolder = $request->type . "/" . $image->extension();
            $image_uploaded_path = $image->store($uploadFolder, 'public');
            $original_name = basename($image_uploaded_path);

            $dataMedia = ([
                'title' => $request->title,
                'alt' => ($request->alt) ? $request->alt : $original_name,
                'original_name' => $original_name,
                'link' => Storage::disk('public')->url($image_uploaded_path),
                'type' => $request->type,
                'status' => ($request->status) ? $request->status : true,
            ]);

            $media = Media::create($dataMedia);

            return response()->json([
                'status' => 'success',
                'message' => 'File Uploaded Successfully',
                'data' => $media
            ], 200);
        }
    }

    private function removeFile($media)
    {

        $extension = explode('.', $media->original_name);
        $uploadFolder = $media->type . "/" . end($extension);
        $image_path = public_path() . '/storage/' . $uploadFolder . '/' . $media->original_name;
        unlink($image_path);
    }

    public function updateSingleData(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'string|max:255|nullable',
            'alt' => 'string|max:255|nullable',
            'file' => 'image:jpeg,png,jpg,gif,svg|max:2048|nullable',
            'type' => 'in:image_product,file_product,image_blog,file_blog,link,image_index,image_galery,file',
            'status' => 'boolean|nullable'
        ]);
        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => $validator->errors()->all(),
            ], 400);
        }

        try {
            $media = Media::findOrFail($id);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => 'error',
                'message' => "Id doesn't exist",
            ], 400);
        }

        if ($request->type == "link") {
            $validator = Validator::make($request->all(), [
                'link' => 'required|url',
            ]);
            if ($validator->fails()) {
                return response()->json([
                    'status' => 'error',
                    'message' => $validator->errors()->all(),
                ], 400);
            }
            if (($request->type == "link") != ($media->type == "link")) {
                try {
                    $this->removeFile($media);
                } catch (\Throwable $th) {
                    return response()->json([
                        'status' => 'error',
                        'message' => "Image not available in storage",
                    ], 500);
                };
            }

            $media = $media->update([
                'title' => ($request->title) ? $request->title : $media->title,
                'alt' => ($request->alt) ? $request->alt : $request->link,
                'original_name' => null,
                'link' => $request->link,
                'type' => ($request->type) ? $request->type : $media->type,
                'status' => ($request->status) ? $request->status : $media->status,
            ]);

            return response()->json([
                'status' => 'success',
                'message' => 'Edit successful',
                'data' => $media
            ], 200);
        }

        if ($request->file or ($request->type != 'link') == ($media->type != 'link')) {

            $validator = Validator::make($request->all(), [
                'file' => 'required',
            ]);
            if ($validator->fails()) {
                return response()->json([
                    'status' => 'error',
                    'message' => $validator->errors()->all(),
                ], 400);
            }

            try {
                $this->removeFile($media);
            } catch (\Throwable $th) {
                return response()->json([
                    'status' => 'error',
                    'message' => "Image not available in storage",
                ], 500);
            }

            $image = $request->file('file');
            $uploadFolder = ($request->type) ? $request->type : $media->type . "/" . $image->extension();
            $image_uploaded_path = $image->store($uploadFolder, 'public');
            $original_name = basename($image_uploaded_path);

            $media = $media->update([
                'title' => ($request->title) ? $request->title : $media->title,
                'alt' => ($request->alt) ? $request->alt : $media->alt,
                'original_name' => $original_name,
                'link' => Storage::disk('public')->url($image_uploaded_path),
                'type' => ($request->type) ? $request->type : $media->type,
                'status' => ($request->status) ? $request->status : $media->status,
            ]);

            return response()->json([
                'status' => 'success',
                'message' => 'File Uploaded Successfully',
                'data' => $media
            ], 200);
        } else {
            $media = $media->update([
                'title' => ($request->title) ? $request->title : $media->title,
                'alt' => ($request->alt) ? $request->alt : $media->alt,
                'status' => ($request->status) ? $request->status : $media->status,
            ]);

            return response()->json([
                'status' => 'success',
                'message' => 'Edit successful',
                'data' => $media
            ], 200);
        }
    }

    public function destroySingleData(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id' => 'required',
        ]);
        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => $validator->errors()->all(),
            ], 400);
        }
        try {
            $media = Media::findOrFail($request->id);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => 'error',
                'message' => "Id doesn't exist",
            ], 400);
        }


        if ($media->type != "link") {
            try {
                $this->removeFile($media);
            } catch (\Throwable $th) {
                return response()->json([
                    'status' => 'error',
                    'message' => "Image not available in storage",
                ], 500);
            }
            Media::destroy($media->id);
            return response()->json([
                'status' => 'success',
                'message' => 'Delete media successfully',
            ], 200);
        } else {
            Media::destroy($media->id);
            return response()->json([
                'status' => 'success',
                'message' => 'Delete media successfully',
            ], 200);
        }
    }

    public function findByOriginalName(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'original_name' => 'required',
        ]);
        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => $validator->errors()->all(),
            ], 400);
        }
        try {
            $media = Media::where('original_name', $request->original_name)->get();

            return response()->json([
                'status' => 'success',
                'message' => 'Media was successfully retrieved',
                'data' => $media
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => 'error',
                'message' => "Original name doesn't exist",
            ], 400);
        }
    }

    public function findByLink(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'link' => 'required',
        ]);
        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => $validator->errors()->all(),
            ], 400);
        }
        try {
            $media = Media::where('link', $request->link)->get();

            return response()->json([
                'status' => 'success',
                'message' => 'Media was successfully retrieved',
                'data' => $media
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => 'error',
                'message' => "Link doesn't exist",
            ], 400);
        }
    }

    public function findByTitle(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required',
        ]);
        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => $validator->errors()->all(),
            ], 400);
        }
        try {
            $media = Media::where('title', 'LIKE', '%' . $request->title . '%')->get();

            return response()->json([
                'status' => 'success',
                'message' => 'Media was successfully retrieved',
                'data' => $media
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => 'error',
                'message' => "Title doesn't exist",
            ], 400);
        }
    }

    public function findById(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id' => 'required',
        ]);
        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => $validator->errors()->all(),
            ], 400);
        }
        try {
            $media = Media::findOrFail($request->id);

            return response()->json([
                'status' => 'success',
                'message' => 'Media was successfully retrieved',
                'data' => $media
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => 'error',
                'message' => "Id doesn't exist",
            ], 400);
        }
    }

    public function getAll(Request $requesy)
    {
        $media = Media::all();
        return response()->json([
            'status' => 'success',
            'message' => 'Media was successfully retrieved',
            'data' => $media
        ], 200);
    }

    public function getByStatus(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'status' => 'required|boolean',
        ]);
        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => $validator->errors()->all(),
            ], 400);
        }
        $media = Media::where('status', $request->status)->get();
        return response()->json([
            'status' => 'success',
            'message' => 'Media was successfully retrieved',
            'data' => $media
        ], 200);
    }

    public function getByType(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'type' => 'required|in:image_product,file_product,image_blog,file_blog,link,image_index,image_galery,file',
        ]);
        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => $validator->errors()->all(),
            ], 400);
        }
        $media = Media::where('type', $request->type)->get();
        return response()->json([
            'status' => 'success',
            'message' => 'Media was successfully retrieved',
            'data' => $media
        ], 200);
    }
}
