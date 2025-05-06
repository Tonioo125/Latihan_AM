<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class UserController extends Controller
{
    public function dashboard()
    {
        return response()->json(['message' => 'Welcome to the User Dashboard']);
    }
}
