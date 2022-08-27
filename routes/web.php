<?php


use App\Http\Controllers\ArticleController;
use App\Http\Controllers\DirectionController;
use App\Http\Controllers\EnterpriseController;
use App\Http\Controllers\MovementController;
use App\Http\Controllers\PersonController;
use App\Http\Controllers\StockcenterController;
use App\Http\Controllers\ReferController;
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

Route::group(['middleware' => 'auth'], function () {
    Route::resource('person', PersonController::class);
    Route::resource('article', ArticleController::class);
    Route::resource('direction', DirectionController::class);
    Route::resource('enterprise', EnterpriseController::class);
    Route::resource('stockcenter', StockcenterController::class);
    Route::resource('refer', ReferController::class);

    Route::get('movement', [MovementController::class, 'index']);
    Route::get('movement/create/{refer}', [MovementController::class, 'create']);
    Route::get('movement/{refer}/edit', [MovementController::class, 'create']);
    Route::post('movement', [MovementController::class, 'store']);
    Route::get('movement/show/{refer}', [MovementController::class, 'show']);
});




Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
