<?php

namespace App\Http\Controllers;

use App\Models\AdminUser;
use App\Services\AdminUserService;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;

class AdminUserController extends Controller
{
    public function register(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'name' => 'required|string|max:255',
                'user' => 'required|string|max:255|unique:admin_users',
                'password' => 'required|string|confirmed',
            ]);

            if ($validator->fails()) {
                return response()->json($validator->errors(), 422);
            }

            $data = (object) $request->all();
            AdminUserService::registerAdminUser($data);

            return response()->json(['success' => true, 'message' => 'User admin created successfully'], 201);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Error creating admin user'], $e->getCode());
        }
    }
}
