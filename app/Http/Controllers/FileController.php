<?php

namespace App\Http\Controllers;

use App\Models\File;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class FileController extends Controller
{
    protected $UPLOAD_DIR;

    public function __construct()
    {
        $this->UPLOAD_DIR = env('UPLOAD_DIR');
    }

    public function uploadFile(Request $request)
    {
        $file = $request->file('file');
        $fileName = $request->input('fileName');
        $fileSize = $file->getSize();
        $fileSizeLimit = 100 * 1024; // 100KB in bytes

        // Check if the file size exceeds the limit (100KB)
        if ($fileSize > $fileSizeLimit) {
            return response()->json(['message' => 'File size exceeds the limit'], 500);
        }

        // Replace the existing file if the same filename is uploaded again
        Storage::delete("{$this->UPLOAD_DIR}/{$fileName}");

        // Store the file in the specified UPLOAD_DIR
        $file->storeAs($this->UPLOAD_DIR, $fileName);

        // Save file details in the database
        $fileModel = new File();
        $fileModel->file_name = $fileName;
        $fileModel->file_path = "{$this->UPLOAD_DIR}/{$fileName}";
        $fileModel->file_size = $fileSize;
        $fileModel->save();

        return response()->json(['message' => 'File uploaded successfully'], 201);
    }

    public function downloadFile(Request $request)
    {
        $fileName = $request->query('fileName');

        // Check if the file exists in the database
        $fileModel = File::where('file_name', $fileName)->first();

        if (!$fileModel) {
            return response()->json(['message' => 'File not found'], 404);
        }

        // Download the file with status code 200
        return response()->download(storage_path("app/{$fileModel->file_path}"));
    }
}
