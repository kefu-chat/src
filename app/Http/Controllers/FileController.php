<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;

class FileController extends Controller
{
    /**
     * 上传图片
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function upload(Request $request)
    {
        $file = $request->file('file');
        if (!$file) {
            throw ValidationException::withMessages([
                'file' => 'File is required!',
            ]);
        }

        $path = 'upload/';
        $filename = md5_file($file->getPathname()) . '.' . ($file->getExtension() ?: $file->getClientOriginalExtension());

        $file->storeAs($path, $filename);
        $url = Storage::url($path . $filename, now()->addDay());

        return response()->success(['url' => $url, 'mime' => $file->getMimeType(),]);
    }
}
