<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class DocumentController extends Controller
{
    public function showDocuments() {
        $documents = Document::all();
        return view('documents.index', compact('documents'));
    }

    public function downloadDocument($id) {
        $decryptedId = decrypt($id);
        $document = Document::find($decryptedId);

        return response()->download(storage_path("app/documents/{$document->filename}"));
    }
    public function previewDocument($id) {
        $decryptedId = decrypt($id);
        $document = Document::find($decryptedId);

        return view('documents.preview', compact('document'));
    }

    public function uploadImage(Request $request)
    {
        
        $file = $request->file('file');
        
        if ($file) {
            // Generate a unique filename
            $filename = time() . '_' . $file->getClientOriginalName();
            
            // Define the public directory path
            $destinationPath = public_path('admin_assets/images/notice/');
            
            // Move the file to the specified path
            $file->move($destinationPath, $filename);
            
            // Generate the URL for the uploaded file
            $scheme = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http';
            $host = $_SERVER['HTTP_HOST'];
            $urlPath = 'admin_assets/images/notice/' . $filename;
            $url = $scheme . '://' . $host . '/' . $urlPath;
            
            return response()->json(['location' => $url],200);
        }

        return response()->json(['error' => 'No file uploaded'], 400);
    }
}
