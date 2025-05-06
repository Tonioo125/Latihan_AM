<?php

namespace App\Http\Controllers;

use App\Models\University;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class UniversityController extends Controller
{
    // GET /api/universities
    public function index()
    {
        return response()->json(University::with('users')->get());
    }

    // POST /api/universities
    public function store(Request $request)
    {
        $validated = $request->validate([
            'pt_code' => 'required|string|max:255',
            'name'    => 'required|string|max:255',
            'address' => 'nullable|string',
            'website' => 'nullable|string',
        ]);

        $validated['id'] = Str::uuid(); // generate UUID manually if not using auto

        $university = University::create($validated);

        return response()->json($university, 201);
    }

    // GET /api/universities/{id}
    public function show($id)
    {
        $university = University::with('users')->findOrFail($id);
        return response()->json($university);
    }

    // PUT/PATCH /api/universities/{id}
    public function update(Request $request, $id)
    {
        $university = University::findOrFail($id);

        $university->update($request->only(['pt_code', 'name', 'address', 'website']));

        return response()->json($university);
    }

    // DELETE /api/universities/{id}
    public function destroy($id)
    {
        University::findOrFail($id)->delete();

        return response()->json(['message' => 'Deleted successfully']);
    }
}
