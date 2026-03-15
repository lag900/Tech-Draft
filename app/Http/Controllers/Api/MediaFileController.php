<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\MediaFile;
use Illuminate\Http\Request;

class MediaFileController extends Controller
{
    public function index(Request $request)
    {
        $query = MediaFile::query();
        if ($id = $request->input('attachable_id')) {
            $query->where('attachable_id', $id);
        }
        if ($type = $request->input('attachable_type')) {
            $query->where('attachable_type', $type);
        }
        return $query->get();
    }

    public function store(Request $request)
    {
        $request->validate([
            'file' => 'required|file|max:10240',
            'attachable_id' => 'required',
            'attachable_type' => 'required|string',
        ]);

        $file = $request->file('file');
        $path = $file->store('attachments', 'public');

        return MediaFile::create([
            'file_name' => $file->getClientOriginalName(),
            'file_type' => $file->getClientOriginalExtension(),
            'file_path' => $path,
            'attachable_id' => $request->attachable_id,
            'attachable_type' => $request->attachable_type,
        ]);
    }

    public function destroy(MediaFile $mediaFile)
    {
        $mediaFile->delete();
        return response()->noContent();
    }
}
