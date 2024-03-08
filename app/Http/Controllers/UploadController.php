<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Plank\Mediable\Exceptions\MediaUpload\ConfigurationException;
use Plank\Mediable\Exceptions\MediaUpload\FileExistsException;
use Plank\Mediable\Exceptions\MediaUpload\FileNotFoundException;
use Plank\Mediable\Exceptions\MediaUpload\FileNotSupportedException;
use Plank\Mediable\Exceptions\MediaUpload\FileSizeException;
use Plank\Mediable\MediaUploader as MediaUploader;
use Illuminate\Support\Facades\Storage;
use Plank\Mediable\Media;

class UploadController extends Controller
{
    /**
     * @throws FileSizeException
     * @throws FileNotSupportedException
     * @throws FileExistsException
     * @throws ConfigurationException
     * @throws FileNotFoundException
     */
    public function upload(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'file' => 'required|file|mimes:doc,txt,pdf|max:10240', // Adjust the max file size as needed
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 400);
        }

        $file = $request->file('file');

        $media = Media::createFrom($file)->setName($file->getClientOriginalName())->save();

        // Continue with the file upload logic
        $media = MediaUploader::fromSource($file)
            ->toDirectory('Uploads')
            ->upload();

        return response()->json(['success' => true, 'media' => $media]);
    }
}
