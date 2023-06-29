<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
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
})->name('home');

Route::match(['get','post'],'/login', [UserController::class,'login'])->name('login');
Route::match(['get','post'],'/register', [UserController::class,'register'])->name('register');
Route::match(['get','post'],'/forgot-password', [UserController::class,'forgotPassword'])->name('forgot-password');
Route::get('/verify/{token}', [UserController::class,'verify'])->name('verify');
Route::get('/reset-password/{token}', [UserController::class,'resetPassword'])->name('reset-password');
Route::post('/reset-password', [UserController::class,'handleResetPassword'])->name('handle-reset-password');
Route::get('logout', [UserController::class,'logout'])->name('logout');

Route::prefix('admin')->middleware('checkUser')->group(function() {
    Route::get('/', function() {
        if (Auth::viaRemember())
        {
            echo "WOOOOOOOOOOOOOOOO";

        }
        else {
            echo 'booooooo';
        }
        return 'dashboard';
    })->name('admin');
});