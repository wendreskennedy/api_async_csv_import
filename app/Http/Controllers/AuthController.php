<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\AdminUser;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $credentials = $request->only('user', 'password');

        if (empty($credentials['user']) || empty($credentials['password'])) {
            return response()->json(['success' => false, 'message' => 'Invalid login fields'], 400);
        }

        try {
            if (!$token = auth('admin')->attempt($credentials)) {
                return response()->json(['success' => false, 'message' => 'Invalid credentials'], 401);
            }
        } catch (JWTException $e) {
            return response()->json(['success' => false, 'message' => 'Error creating token'], $e->getCode());
        }

        return response()->json([
            'success' => true,
            'message' => 'Login successfully',
            'data' => [
                'access_token' => $token,
                'token_type' => 'bearer'
            ]
        ]);
    }

    public function logout(Request $request)
    {
        if (auth()->guard('api')->check()) {
            auth()->guard('api')->logout();
            return response()->json(['success' => true, 'message' => 'Logout successfully']);
        }
    }
}
