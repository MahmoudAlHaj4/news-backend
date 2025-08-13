<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Tymon\JWTAuth\Facades\JWTAuth;
use Illuminate\Routing\Controller as BaseController;

class AuthController extends BaseController
{
    public function __construct()
    {
        
        $this->middleware('auth:api', ['except' => ['login', 'register']]);
    }
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|unique:users',
            'password' => 'required|string|min:6', 
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        $token = JWTAuth::fromUser($user);

       return $this->respondWithToken($token, $user, 'تم تسجيل الدخول بنجاح');
    }


 public function login(Request $request) 
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required|string|min:6',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'error' => 'Validation failed',
                'messages' => $validator->errors()
            ], 422);
        }

        $credentials = $request->only('email', 'password');

        if (!$token = JWTAuth::attempt($credentials)) {
            return response()->json(['error' => 'بيانات الدخول غير صحيح'], 401);
        }

        $user = JWTAuth::user();
        
        return $this->respondWithToken($token, $user, 'تم تسجيل الدخول بنجاح');
    }


    public function me()
    {
        return response()->json(JWTAuth::user());
    }


    public function logout()
    {
        JWTAuth::logout();

        return response()->json(['message' => 'Successfully logged out']);
    }


    public function refresh()
    {
        $token = JWTAuth::refresh();

        return $this->respondWithToken($token);
    }

   protected function respondWithToken($token, $user = null, $message = null)
    {
        $response = [
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => JWTAuth::factory()->getTTL() * 60,
        ];

        if ($message) {
            $response['message'] = $message;
        }

        if ($user) {
            $response['user'] = [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'created_at' => $user->created_at,
                'updated_at' => $user->updated_at,
            ];
        }

        return response()->json($response);
    }

    public function getAllUsers()
    {
        $users = User::all();
        return response()->json($users);
    }

    public function deleteUser($id)
    {
        $user = User::find($id);
        if (!$user) {
            return response()->json(['error' => 'User not found'], 404);
        }

        $user->delete();
        return response()->json(['message' => 'User deleted successfully']);
    }
}
