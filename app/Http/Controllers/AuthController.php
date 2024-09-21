<?php

namespace App\Http\Controllers;

use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    // Register User
    public function register(Request $request)
    {
        $validated_data = $request->validate([
            'name' => ['required', 'string'],
            'email' => ['required', 'email', 'unique:users'],
            'password' => ['required', 'min:8'],
        ]);

        $user = User::create($validated_data);
        $token = $user->createToken('auth_token')->plainTextToken;

        return [
            'user' => $user,
            'token' => $token,
        ];
    }

    // Login
    public function login(Request $request)
    {
        $validated_data = $request->validate([
            'email' => ['required', 'email', 'exists:users'],
            'password' => ['required', 'min:8'],
        ]);

        $user = User::where('email', $validated_data['email'])->first();

        if (!$user || !Hash::check($validated_data['password'], $user->password)) {
            return response([
                'message' => 'Password is not correct!'
            ], 401);
        }

        $token = $user->createToken('auth_token')->plainTextToken;

        return [
            'user' => $user,
            'token' => $token,
        ];
    }

    // Get User Profile
    public function userProfile()
    {
        $user_data = auth()->user();

        return response()->json([
            'status' => true,
            'message' => 'User Login Profile',
            'data' => $user_data,
            'id' => auth()->user()->id
        ], 200);
    }

    // User Resources
    public function userResource()
    {
        $user_data = new UserResource(User::findOrFail(auth()->user()->id));

        return response()->json([
            'status' => true,
            'message' => 'User Login Profile using API Resource',
            'data' => $user_data,
            'id' => auth()->user()->id
        ], 200);
    }

    // User Resources as Collection
    public function userResourceCollection()
    {
        $user_data = UserResource::collection(User::all());

        return response()->json([
            'status' => true,
            'message' => 'User Login Profile using API Resource as Collection',
            'data' => $user_data,
            'id' => ''
        ], 200);
    }

    // Logout
    public function logout()
    {
        auth()->user()->tokens()->delete();

        return response()->json([
            'status' => true,
            'message' =>  'Logout token',
            'data' => []
        ], 200);
    }
}
