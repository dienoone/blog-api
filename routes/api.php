<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use App\Http\Controllers\Api\TestController;


/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

// API Version 1
Route::prefix('v1')->group(function () {

    // Public routes
    Route::prefix('/test')->group(function () {
        // Test routes (remove in production)
        Route::get('/success', [TestController::class, 'testSuccess']);
        Route::get('/bad-request', [TestController::class, 'testBadRequest']);
        Route::get('/not-found', [TestController::class, 'testNotFound']);
        Route::get('/validation', [TestController::class, 'testValidation']);
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
