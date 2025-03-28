<?php

namespace App\Http\Controllers;

use App\Models\User;

class UserController extends Controller
{
    public function getUsers()
    {
        return response()->json(User::paginate(5));
    }
}
