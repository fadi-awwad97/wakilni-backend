<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;




class AuthController extends Controller
{
    public function signup(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'username' => 'required|string|max:255',
            'email' => 'required|string|email|unique:users|max:255',
            'password' => 'required|string|min:6',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 422);
        }

        $user = User::create([
            'username' => $request->username,
            'email' => $request->email,
            'password' => bcrypt($request->password),
        ]);

        try {
            if (!$token = JWTAuth::fromUser($user)) {
                return response()->json(['error' => 'Could not create token'], 500);
            }
        } catch (JWTException $e) {
            return response()->json(['error' => 'Could not create token'], 500);
        }

        return response()->json(['token' => $token], 200);
    }

    public function login(Request $request)
    {

        $credentials = $request->only('email', 'password');

        // Check if the user exists in the database
        $user = Auth::attempt($credentials);


        if (!$user) {
            return response()->json(['error' => 'Invalid credentials'], 401);
        } else {
            $user = auth()->user();
        }

        try {
            if (!$token = JWTAuth::fromUser($user)) {
                return response()->json(['error' => 'Could not create token'], 500);
            }
        } catch (JWTException $e) {
            return response()->json(['error' => 'Could not create token'], 500);
        }

        return response()->json(['token' => $token], 200);
    }

    public function logout()
    {
        // Invalidate the current user's token
        JWTAuth::invalidate(JWTAuth::getToken());

        // Log the user out of the application
        Auth::guard('api')->logout();

        return response()->json(['message' => 'Logged out successfully'], 200);
    }
}
