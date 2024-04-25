<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    //
    public function index()
    {

    }

    public function login(Request $request)
    {
        $validated = $request->validate([
            'username' => 'required',
            'password' => 'required',
        ]);

        $user = User::where('Username', $request->username)->first();

        if (!$user && !Hash::check($request->password, $user->password)) {
            return response()->json([
                "message" => 'invalid login'
            ], 401);
        }
        return response()->json([
            "token" => $user->createToken('user')->plainTextToken
        ], 200);
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();
        // auth()->user()->tokens()->delete();
        return response()->json([
            'message' => 'logout success'
        ], 200);

    }
}
