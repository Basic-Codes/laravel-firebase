<?php

use App\Http\Controllers\HomeController;
use App\Http\Controllers\QManagementCtrl;
use App\Http\Controllers\TestController;
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

Route::get('test', [TestController::class, 'firebaseTest']);

Route::get('/', [HomeController::class, 'index'])->name('home');
Route::post('add-user', [HomeController::class, 'addUser'])->name('user.add');
Route::post('add-shop', [HomeController::class, 'addShop'])->name('shop.add');
Route::get('active-shop', [HomeController::class, 'activeShop'])->name('shop.active');

Route::post('add-to-q', [QManagementCtrl::class, 'addToQ'])->name('q.add');
Route::post('vall', [QManagementCtrl::class, 'call'])->name('q.call');
