<?php


use App\Http\Controllers\ArticleController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\DirectionController;
use App\Http\Controllers\OperationController;
use App\Http\Controllers\MovementController;
use App\Http\Controllers\PersonController;
use App\Http\Controllers\StockcenterController;
use App\Http\Controllers\ReferController;
use App\Http\Controllers\StockController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\TechnicalController;
// use App\Notifications\NotificationController;
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

Auth::routes(['register' => true, 'reset' => false]);

Route::middleware('auth','checkIfUserIsActive')->group(function () {

    Route::match(['get', 'post'], '/operation-select', [HomeController::class, 'operationSelect'])->name('operation.select');

    // Route::delete('notifications/{notification}', [NotificationController::class, 'destroy'])->name('notifications.delete');
    
    Route::group(['middleware' => ['role:admin']], function () { 
        Route::get('admin/users', [AdminController::class, 'index'])->name('admin.users.index');
        Route::get('admin/users/{user}/edit', [AdminController::class, 'edit'])->name('admin.users.edit');
        Route::put('admin/users/{user}', [AdminController::class, 'update'])->name('admin.users.update');
        Route::get('admin/users/create', [AdminController::class, 'create'])->name('admin.users.create');
        Route::resource('operation', OperationController::class);
    });

    Route::middleware('checkSelectedOperation')->group(function () {
        Route::group(['middleware' => ['role:tecnico']], function () { 
            Route::resource('technical', TechnicalController::class); //Ruta para ordenes tecnicas
        });

        Route::get('/home', [HomeController::class, 'index'])->name('home');

        Route::resource('person', PersonController::class);
        Route::resource('article', ArticleController::class);
        Route::resource('direction', DirectionController::class);
        Route::resource('stockcenter', StockcenterController::class);
        Route::resource('refer', ReferController::class);

        Route::patch('refer/finalized/{refer}', [ReferController::class, 'finalized']);
        Route::get('refer/emited/{refer}', [ReferController::class, 'emited']);
        Route::get('refer/get/pdf/{refer}', [ReferController::class, 'getpdf']);

        Route::get('movement', [MovementController::class, 'index']);
        Route::get('transit', [MovementController::class, 'transit']);
        Route::get('movement/create/{refer}', [MovementController::class, 'create']);
        Route::get('movement/{refer}/edit', [MovementController::class, 'create']);
        Route::post('movement', [MovementController::class, 'store']);
        Route::get('movement/show/{refer}', [MovementController::class, 'show'])->name('movement.show');;

        Route::get('stock', [StockController::class, 'index'])->name('stock.index');;
        Route::get('stock/{stock}', [StockController::class, 'show'])->name('stock.show');;
        Route::put('stock/{stock}', [StockController::class, 'update'])->name('stock.update');

        Route::get('stock/get/pdf', [StockController::class, 'getpdf']);
        Route::get('stock/get/excel', [StockController::class, 'getexcel']);
        Route::post('stock', [StockController::class, 'store']);

        Route::get('/home/reportRpFyS', [HomeController::class, 'reportRpFyS'])->name('home.reportRpFyS');
        Route::get('/home/reportAllMovement', [HomeController::class, 'reportAllMovement'])->name('home.reportAllMovement');

        // Notificaciones
        Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications.index');
        Route::get('/notifications/count', [NotificationController::class, 'count'])->name('notifications.count');
        Route::post('/notifications/{id}/read', [NotificationController::class, 'markAsRead'])->name('notifications.markAsRead');
        Route::delete('/notifications/{id}', [NotificationController::class, 'delete'])->name('notifications.delete');
        Route::post('/notifications/mark-all-read', [NotificationController::class, 'markAllAsRead'])->name('notifications.markAllRead');
        Route::delete('/notifications/clear-all', [NotificationController::class, 'clearAll'])->name('notifications.clearAll');

        // Rutas con filtros predefinidos para el dashboard
        Route::get('/stock/negative', [StockController::class, 'index'])->name('stock.negative')
            ->defaults('filter', 'negative');
            
        Route::get('/stock/dead', [StockController::class, 'index'])->name('stock.dead')
            ->defaults('filter', 'dead');
            
        Route::get('/stock/alerts', [StockController::class, 'index'])->name('stock.alerts')
            ->defaults('filter', 'alerts');
            
        Route::get('/refer/pending', [ReferController::class, 'index'])->name('refer.pending')
            ->defaults('status', 'E');
    });
});





