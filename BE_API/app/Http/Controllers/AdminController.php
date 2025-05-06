<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class AdminController extends Controller
{

    public function index()
    {
        $users = User::with('photoFile')->where('role', 'User')
            ->where('approval', 'Pending')
            ->get();

        return response()->json($users);
    }
    public function all_user()
    {
        $users = User::with('photoFile')->get();

        return response()->json($users);
    }

    public function show($id)
    {
        $user = User::with(['university', 'files.fileCategory'])->findOrFail($id);

        $user->files = $user->files->map(function ($file) {
            return [
                'file_url' => $file->file_url,
                'url' => asset('storage/' . $file->file_url),
                'name' => $file->fileCategory->name ?? 'Unnamed File',
            ];
        });

        return response()->json(['user' => $user]);
    }
    public function approve($id)
    {
        $user = User::findOrFail($id);
        $user->approval = 'Approved';
        $user->save();

        return response()->json(['message' => 'User approved successfully']);
    }

    public function delete($id)
    {
        $user = User::findOrFail($id);
        $user->delete();

        return response()->json(['message' => 'User deleted successfully']);
    }
}
