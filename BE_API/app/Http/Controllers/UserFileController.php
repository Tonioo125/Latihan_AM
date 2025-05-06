<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\UserFile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class UserFileController extends Controller
{
    public function index()
    {
        return response()->json(
            UserFile::with(['user', 'fileCategory'])->get(),
            User::with('photoFile')->get()
        );
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'user_id' => 'required|uuid|exists:users,id',
            'file_category_id' => 'required|uuid|exists:file_categories,id',
            'file_url' => 'nullable|string',
        ]);

        $validated['id'] = Str::uuid();

        $userFile = UserFile::create($validated);

        return response()->json($userFile, 201);
    }

    public function show($id)
    {
        $userFile = UserFile::with(['user', 'fileCategory'])->findOrFail($id);
        return response()->json($userFile);
    }

    public function update(Request $request, $id)
    {
        $userFile = UserFile::findOrFail($id);

        $validated = $request->validate([
            'file_url' => 'nullable|string',
            'file_category_id' => 'nullable|uuid|exists:file_categories,id',
        ]);

        $userFile->update($validated);

        return response()->json($userFile);
    }

    public function destroy($id)
    {
        UserFile::findOrFail($id)->delete();
        return response()->json(['message' => 'Deleted successfully']);
    }

    //Upload function
    public function upload(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'file' => 'required|file|mimes:pdf,doc,docx,png,jpg,jpeg|max:2048',
            'file_category_id' => 'required|exists:file_categories,id'
        ]);

        $path = $request->file('file')->store('user_files', 'public');

        $userFile = UserFile::create([
            'user_id' => $request->user_id,
            'file_category_id' => $request->file_category_id,
            'file_url' => $path,
        ]);

        return response()->json([
            'message' => 'File uploaded successfully',
            'file' => $userFile,
        ]);
    }

    public function download($path)
    {
        $filePath = 'public/' . $path;

        if (!Storage::exists($filePath)) {
            logger("File not found: " . $filePath);
            abort(404, 'File not found');
        }

        return Storage::download($filePath);
    }
}
