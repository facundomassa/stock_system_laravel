<?php


use App\Http\Controllers\ArticleController;
use App\Http\Controllers\DirectionController;
use App\Http\Controllers\PersonController;
use App\Models\Direction;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Auth::routes();

Route::resource('person', PersonController::class);
Route::resource('article', ArticleController::class);
Route::resource('direction', DirectionController::class);

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');