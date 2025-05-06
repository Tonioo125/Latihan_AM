<?php

namespace App\Http\Controllers;

use App\Models\FileCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage; // Make sure this is included


class FileCategoryController extends Controller
{
    // GET /api/file-categories
    public function index()
    {
        return response()->json(FileCategory::all());
    }

    public function getCategories()
    {
        // Fetch categories from the database
        $categories = FileCategory::all();  // Fetch all categories (adjust if you want specific ones)

        return response()->json($categories);
    }

    // POST /api/file-categories
    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|unique:file_categories,name',
            'img_category_url' => 'nullable|image|max:2048', // Optional image validation
        ]);

        // If the file exists, store it and set the URL
        if ($request->hasFile('img_category_url')) {
            $path = $request->file('img_category_url')->store('file-categories-icon', 'public');
            $data['image_url'] = asset('storage/' . $path);  // Update field to match column name
        }

        try {
            // Create the category and return a response
            $category = FileCategory::create($data);
            return response()->json($category, 201);
        } catch (\Exception $e) {
            \Log::error('Error creating category:', ['error' => $e->getMessage()]);
            return response()->json(['error' => 'Failed to insert data'], 500);
        }
    }


    // GET /api/file-categories/{id}
    public function show(FileCategory $fileCategory)
    {
        return response()->json($fileCategory);
    }

    // PUT/PATCH /api/file-categories/{id}
    public function update(Request $request, $id)
    {
        $category = FileCategory::findOrFail($id);

        $data = $request->validate([
            'name' => 'required|unique:file_categories,name,' . $id,
            'img_category_url' => 'nullable|image|max:2048',
        ]);

        $category->name = $data['name'];

        if ($request->hasFile('img_category_url')) {
            // Delete old image if it exists
            if ($category->image_url) {
                $oldPath = str_replace(asset('storage') . '/', '', $category->image_url);
                if (Storage::disk('public')->exists($oldPath)) {
                    Storage::disk('public')->delete($oldPath);
                }
            }

            // Store new image
            $path = $request->file('img_category_url')->store('file-categories-icon', 'public');
            $category->image_url = asset('storage/' . $path);
        }

        $category->save();

        return response()->json($category);
    }

    // DELETE /api/file-categories/{id}
    public function destroy(FileCategory $fileCategory)
    {
        // Extract the relative file path from the stored img_category_url
        if ($fileCategory->image_url) {
            // If you stored it using asset(), strip the asset URL part
            $filePath = str_replace(asset('storage') . '/', '', $fileCategory->image_url);

            if (\Storage::disk('public')->exists($filePath)) {
                \Storage::disk('public')->delete($filePath);
            }
        }

        $fileCategory->delete();

        return response()->json(null, 204);
    }
}
