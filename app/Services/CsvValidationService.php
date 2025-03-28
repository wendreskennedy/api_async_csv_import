<?php

namespace App\Services;

use Illuminate\Support\Facades\Validator;

class CsvValidationService
{
    public static function validateUser(array $data): array
    {
        $userData = [
            'name' => explode(";", $data[0])[0] ?? null,
            'email' => explode(";", $data[0])[1] ?? null,
            'birthdate' => explode(";", $data[0])[2] ?? null,
        ];

        $validator = Validator::make($userData, [
            'name' => 'required|string|min:3',
            'email' => 'required|email|unique:users,email',
            'birthdate' => 'required|date_format:Y-m-d',
        ]);

        if ($validator->fails()) {
            return [
                'success' => false,
                'message' => $validator->errors()->toArray(),
            ];
        }

        return [
            'success' => true,
            'data' => $userData
        ];
    }
}
