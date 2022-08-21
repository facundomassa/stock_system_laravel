<?php


use App\Http\Controllers\ArticleController;
use App\Http\Controllers\DirectionController;
use App\Http\Controllers\EnterpriseController;
use App\Http\Controllers\PersonController;
use App\Http\Controllers\StockcenterController;
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

Auth::routes(['register' => false, 'reset' => false]);

Route::resource('person', PersonController::class)->middleware('auth');
Route::resource('article', ArticleController::class)->middleware('auth');
Route::resource('direction', DirectionController::class)->middleware('auth');
Route::resource('enterprise', EnterpriseController::class)->middleware('auth');
Route::resource('stockcenter', StockcenterController::class)->middleware('auth');

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
