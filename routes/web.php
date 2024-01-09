<?php


use App\Http\Controllers\ArticleController;
use App\Http\Controllers\DirectionController;
use App\Http\Controllers\EnterpriseController;
use App\Http\Controllers\MovementController;
use App\Http\Controllers\PersonController;
use App\Http\Controllers\StockcenterController;
use App\Http\Controllers\ReferController;
use App\Http\Controllers\StockController;
use App\Http\Controllers\HomeController;
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

    Route::patch('refer/finalized/{refer}', [ReferController::class, 'finalized']);
    Route::get('refer/emited/{refer}', [ReferController::class, 'emited']);
    Route::get('refer/pdf/{refer}', [ReferController::class, 'getpdf']);

    Route::get('movement', [MovementController::class, 'index']);
    Route::get('movement/create/{refer}', [MovementController::class, 'create']);
    Route::get('movement/{refer}/edit', [MovementController::class, 'create']);
    Route::post('movement', [MovementController::class, 'store']);
    Route::get('movement/show/{refer}', [MovementController::class, 'show']);

    Route::get('stock', [StockController::class, 'index']);
    Route::get('stock/{stock}', [StockController::class, 'show']);
    Route::put('stock/{stock}', [StockController::class, 'update']);
    
    Route::get('stock/get/pdf', [StockController::class, 'getpdf']);
    Route::get('stock/get/excel', [StockController::class, 'getexcel']);
    Route::post('stock', [StockController::class, 'store']);

    Route::get('home', [HomeController::class, 'index']);
    Route::get('howtouse', [HomeController::class, 'howtouse']);
});





