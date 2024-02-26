<?php

// app/Http/Controllers/JWTAuthController.php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Facades\JWTAuth;

class JWTAuthController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        // Apply the jwt.auth middleware to all methods in this controller
        // except for the login and register methods.
        $this->middleware('jwt.auth', ['except' => ['login', 'register']]);
    }

    /**
     * Register a new user and return a JWT token.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function register(Request $request)
    {
        // Validate the request data
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'phone' => 'required|string|digits:11|unique:users',
            'password' => 'required|string|min:8|confirmed',
        ]);

        // Return validation errors if any
        if ($validator->fails()) {
            return response()->json([
                'message' => implode("\n", $validator->errors()->all()),
                'errors' => $validator->errors()
            ], 422);
        }

        // Create a new user with the request data
        $user = User::create([
            'name' => $request->name,
            'phone' => $request->phone,
            'password' => Hash::make($request->password),
        ]);

        // Generate a JWT token for the user
        $token = JWTAuth::fromUser($user);

        // Return a successful response with the token
        return response()->json([
            'success' => true,
            'data' => [
                'id' => $user->id,
                'name' => $user->name,
                'phone' => $user->phone,
                'token' => 'Bearer ' . $token,
                'user' => $user
            ]
        ], 201);
    }

    /**
     * Authenticate a user and return a JWT token.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function login(Request $request)
    {
        // Validate the request data
        $validator = Validator::make($request->all(), [
            'phone' => 'required|string|digits:11',
            'password' => 'required|string|min:8',
        ]);

        // Return validation errors if any
        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        // Attempt to authenticate the user with the credentials
        $credentials = $request->only('phone', 'password');
        try {
            if (!$token = JWTAuth::attempt($credentials)) {
                return response()->json(['error' => 'Invalid credentials'], 401);
            }
        } catch (JWTException $e) {
            return response()->json(['error' => 'Could not create token'], 500);
        }

        // Return a successful response with the token
        return response()->json([
            'success' => true,
            'data' => [
                'id' => Auth::user()->id,
                'name' => Auth::user()->name,
                'phone' => Auth::user()->phone,
                'token' => 'Bearer ' . $token
            ]
        ], 200);
    }

    /**
     * Logout a user and invalidate the JWT token.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout(Request $request)
    {
        // Get the token from the request header
        $token = $request->header('Authorization');

        // Try to invalidate the token
        try {
            JWTAuth::invalidate($token);
            return response()->json(['success' => true, 'message' => 'User logged out successfully'], 200);
        } catch (JWTException $e) {
            return response()->json(['success' => false, 'error' => 'Failed to logout, please try again.'], 500);
        }
    }

    /**
     * Refresh a JWT token and return a new one.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function refresh(Request $request)
    {
        // Get the token from the request header
        $token = $request->header('Authorization');

        // Try to refresh the token
        try {
            $token = JWTAuth::refresh($token);
            return response()->json(['success' => true, 'data' => ['token' => 'Bearer ' . $token]], 200);
        } catch (JWTException $e) {
            return response()->json(['success' => false, 'error' => 'Failed to refresh token, please login again.'], 401);
        }
    }
}
