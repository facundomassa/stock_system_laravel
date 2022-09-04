<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\api\ApiCountryController;
use App\Http\Controllers\ArticleController;
use App\Http\Controllers\MovementController;
use App\Http\Controllers\UserController;

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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});


Route::post('register', [UserController::class,'register']);
Route::post('login', [UserController::class, 'authenticate']);
Route::get('country', [ApiCountryController::class, 'country']);
Route::get('state/{country}', [ApiCountryController::class, 'state']);

Route::group(['middleware' => ['jwt.verify']], function() {
    Route::get('user', [UserController::class,'getAuthenticatedUser']);
    
});

Route::get('article', [ArticleController::class,'filters']);