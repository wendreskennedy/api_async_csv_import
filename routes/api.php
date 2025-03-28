<?php

use App\Http\Controllers\AdminUserController;
use App\Http\Controllers\UploadController;
use App\Http\Controllers\ImportStatusController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\AuthController;

Route::post('/register', [AdminUserController::class, 'register']);


Route::post('login', [AuthController::class, 'login'])->name('login');
Route::post('logout', [AuthController::class, 'logout'])->middleware('auth:api');


Route::post('/upload', [UploadController::class, 'upload'])->middleware('auth:api');
Route::get('/import-status/{id}', [ImportStatusController::class, 'getStatus'])->middleware('auth:api');
Route::get('/users', [UserController::class, 'getUsers'])->middleware('auth:api');
