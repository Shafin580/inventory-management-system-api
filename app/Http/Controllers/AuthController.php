<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function login(Request $request){
        $credentials = $request->only('email', 'password');

        if (Auth::attempt($credentials)) {
            $user = Auth::user();
            $token = $user->createToken('AuthToken')->accessToken;

            return response()->json(['status_code' => 200, 'token' => "Bearer " . $token], 200);
        }

        return response()->json(['status_code' => 401, 'message' => 'Invalid Credentials'], 401);
    }

    public function register(Request $request){
        
        $request->validate([
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:6',
        ]);

        $user = User::create([
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        return response()->json(['status_code' => 201, 'message' => 'User registered successfully!'], 201);
    }
}
