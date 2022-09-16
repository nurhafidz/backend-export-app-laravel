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
            ], 500);
        }


        if ($request->type == "link") {
            $validator = Validator::make($request->all(), [
                'link' => 'required|url',
            ]);
            if ($validator->fails()) {
                return response()->json([
                    'status' => 'error',
                    'message' => $validator->errors()->all(),
                ], 500);
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
                ], 500);
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
            ], 500);
        }

        try {
            $media = Media::findOrFail($id);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => 'error',
                'message' => "Id doesn't exist",
            ], 500);
        }

        if ($request->type == "link") {
            $validator = Validator::make($request->all(), [
                'link' => 'required|url',
            ]);
            if ($validator->fails()) {
                return response()->json([
                    'status' => 'error',
                    'message' => $validator->errors()->all(),
                ], 500);
            }
            if (($request->type == "link") != ($media->type == "link")) {
                $this->removeFile($media);
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
                ], 500);
            }

            $this->removeFile($media);

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
}
