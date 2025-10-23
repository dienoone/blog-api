<?php

use App\Http\Controllers\Api\CategoryController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use App\Http\Controllers\Api\TestController;
use App\Http\Controllers\Api\AuthController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

// API Version 1
Route::prefix('v1')->group(function () {

    // Public routes
    Route::group([], function () {
        // Authentication routes
        Route::prefix('/auth')->group(function () {
            Route::post('/register', [AuthController::class, 'register']);
            Route::post('/login', [AuthController::class, 'login']);
        });

        // Test routes
        Route::prefix('/test')->group(function () {
            // Test routes (remove in production)
            Route::get('/success', [TestController::class, 'testSuccess']);
            Route::get('/bad-request', [TestController::class, 'testBadRequest']);
            Route::get('/not-found', [TestController::class, 'testNotFound']);
            Route::get('/validation', [TestController::class, 'testValidation']);
        });


        // Categories routes
        Route::prefix('/categories')->group(function () {
            Route::get('/', [CategoryController::class, 'index']);
            Route::get('/{category}', [CategoryController::class, 'show']);
        });
    });

    // Protected middlewares
    Route::middleware('auth:sanctum')->group(function () {
        // Auth routes
        Route::prefix('auth')->group(function () {
            Route::get('/profile', [AuthController::class, 'profile']);
            Route::post('/logout', [AuthController::class, 'logout']);
            Route::post('/logout-all', [AuthController::class, 'logoutAll']);
        });


        // Categories routes
        Route::prefix('/categories')->group(function () {
            Route::post('/', [CategoryController::class, 'store']);
            Route::put('/{category}', [CategoryController::class, 'update']);
            Route::delete('/{category}', [CategoryController::class, 'destroy']);
        });
    });
});

// Temporary DB check route (remove in production)
Route::get('/v1/db-check', function () {
    try {
        $tables = DB::select('SHOW TABLES');
        $tableNames = array_map(function ($table) {
            $tableName = 'Tables_in_' . env('DB_DATABASE');
            return $table->$tableName;
        }, $tables);

        $tableInfo = [];
        foreach ($tableNames as $table) {
            $columns = Schema::getColumnListing($table);
            $tableInfo[$table] = [
                'columns' => $columns,
                'row_count' => DB::table($table)->count()
            ];
        }

        return response()->json([
            'status' => 'success',
            'database' => env('DB_DATABASE'),
            'tables' => $tableInfo
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'status' => 'error',
            'message' => $e->getMessage()
        ], 500);
    }
})->name('db.check');
