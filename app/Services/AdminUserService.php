<?php

namespace App\Services;

use App\Models\AdminUser;
use Illuminate\Support\Facades\Hash;
use stdClass;

class AdminUserService
{
    public static function registerAdminUser(stdClass $data): AdminUser
    {
        return AdminUser::create([
            'name' => $data->name,
            'user' => $data->user,
            'password' => Hash::make($data->password),
        ]);
    }
}
