<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

<<<<<<< HEAD
Route::middleware('auth:api')->get('/user', function (Request $request) {
=======
Route::middleware('Auth:sanctum')->get('/user', function (Request $request) {
>>>>>>> 74ba0951e6b64f358c0d3b230295efb4db24237a
    return $request->user();
});
