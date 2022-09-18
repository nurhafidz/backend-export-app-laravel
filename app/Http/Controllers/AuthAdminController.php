<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\Admin;
use App\Http\Controllers\ValidationException;

class AuthAdminController extends Controller
{
    public function __construct()
    {
        $this->middleware(['assign.guard:admin', 'jwt.auth'], ['except' => ['login', 'register']]);
    }

    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|string|email',
            'password' => 'required|string',
        ]);
        $credentials = $request->only(['email', 'password']);


        $token = auth()->guard('admin')->attempt($credentials);

        if (!$token) {
            return response()->json([
                'status' => 'error',
                'message' => 'Email or Password is invalid',
            ], 401);
        }


        $admin = Auth::guard('admin')->user();
        return response()->json([
            'status' => 'success',
            'user' => $admin,
            'authorisation' => [
                'token' => $token,
                'type' => 'bearer',
                'expires_in' => auth()->guard('admin')->factory()->getTTL() * 60
            ]
        ]);
    }

    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:6',
        ]);

        $admin = Admin::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        $token = auth()->guard('admin')->login($admin);
        if (auth()->guard('admin')->attempt($request->only('email', 'password'))) {
            return response()->json([
                'status' => 'success',
                'message' => 'Admin created successfully',
                'user' => $admin,
                'authorization' => [
                    'token' => $token,
                    'type' => 'bearer',
                    'expires_in' => auth()->guard('admin')->factory()->getTTL() * 60
                ]
            ]);
        }
        return response()->json([
            'status' => 'error',
            'message' => 'error cant login',

        ], 400);
    }

    public function getProfile()
    {
        return response()->json(Auth::guard('admin')->user());
    }

    public function logout()
    {
        Auth::logout();
        return response()->json([
            'status' => 'success',
            'message' => 'Successfully logged out',
        ]);
    }

    public function refresh()
    {
        return response()->json([
            'status' => 'success',
            'user' => Auth::guard('admin')->user(),
            'authorization' => [
                'token' => Auth::refresh(),
                'type' => 'bearer',
                'expires_in' => auth()->guard('admin')->factory()->getTTL() * 60
            ]
        ]);
    }
}
