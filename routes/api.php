<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\PropertiesController;
use App\Http\Controllers\Api\ClientController;
use App\Http\Controllers\Api\UsersController;
use App\Http\Controllers\Api\TasksController;

/*
|--------------------------------------------------------------------------
|                                 Auth
|--------------------------------------------------------------------------
*/
Route::post('/auth/login', [AuthController::class, 'login']);
Route::middleware(['auth:api'])->group(function () {
    Route::post('/auth/signup', [AuthController::class, 'signUp']);
    Route::get('user', [AuthController::class, 'user']);
});


/*
|--------------------------------------------------------------------------
|                                 Properties
|--------------------------------------------------------------------------
*/
Route::post('properties', [PropertiesController::class, 'getAllProperties']);
Route::get('properties/{id}', [PropertiesController::class, 'getPropertyById']);

/*
|--------------------------------------------------------------------------
|                                 Clients
|--------------------------------------------------------------------------
*/
Route::post('clients', [ClientController::class, 'getAllClients']);


/*
|--------------------------------------------------------------------------
|                                 Users
|--------------------------------------------------------------------------
*/
Route::get('users', [UsersController::class, 'getAllUsers']);

/*
|--------------------------------------------------------------------------
|                                 Tasks
|--------------------------------------------------------------------------
*/
Route::post('tasks', [TaskController::class, 'createTask']);