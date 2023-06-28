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

Route::prefix('admin')->middleware('auth')->group(function() {
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