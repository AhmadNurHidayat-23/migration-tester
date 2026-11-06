<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\MigrationController;
require __DIR__.'/auth.php';
Route::get('/',fn()=>redirect(auth()->check()?'/migrations':'/login'));
Route::middleware('auth')->group(function(){Route::get('/migrations',[MigrationController::class,'index']);Route::post('/runs',[MigrationController::class,'store'])->middleware('throttle:5,1');Route::get('/runs/{run}',[MigrationController::class,'show']);Route::get('/runs/{run}/report',[MigrationController::class,'export']);});
