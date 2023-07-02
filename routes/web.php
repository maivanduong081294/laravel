<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\Admin\GeneralController;
use App\Http\Controllers\Admin\UserController as AdminUserController;
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
    return phpinfo();
})->name('home');

Route::match(['get','post'],'/login', [UserController::class,'login'])->name('login');
Route::match(['get','post'],'/register', [UserController::class,'register'])->name('register');
Route::match(['get','post'],'/forgot-password', [UserController::class,'forgotPassword'])->name('forgot-password');
Route::get('/verify/{token}', [UserController::class,'verify'])->name('verify');
Route::get('/reset-password/{token}', [UserController::class,'resetPassword'])->name('reset-password');
Route::post('/reset-password', [UserController::class,'handleResetPassword'])->name('handle-reset-password');
Route::get('logout', [UserController::class,'logout'])->name('logout');

Route::get('admin', [GeneralController::class,'index'])->middleware('checkUser')->name('admin');    
Route::prefix('admin')->name('admin.')->middleware('checkUser')->group(function() {
    Route::resources([
        'users' => AdminUserController::class,
    ]);
});