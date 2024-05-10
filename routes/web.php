<?php

use App\Http\Controllers\Authcontroller;
use App\Http\Controllers\TransactionController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
 */

Route::middleware('guest')->controller(Authcontroller::class)->group(function () {
    Route::get('/register', 'register')->name('register');
    Route::post('/store-register', 'storeRegister')->name('storeRegister');

    Route::get('/', 'login')->name('login');
    Route::post('/store-login', 'storeLogin')->name('storeLogin');
});

Route::middleware(['auth'])->controller(TransactionController::class)->group(function () {
    Route::post('/logout', function (Request $request) {
        auth()->logout();
        session()->flash('message', 'Logout successfull');

        return to_route('login');
    })->name('logout');
    Route::get('/welcome', 'welcome')->name('welcome');
    Route::get('/transaction-list', 'transactionList')->name('transactionList');

    Route::get('/deposit-list', 'depositList')->name('depositList');
    Route::post('/store-deposit', 'storeDeposit')->name('storeDeposit');

    Route::get('/withdrawal-list', 'withdrawalList')->name('withdrawalList');
    Route::post('/store-withdrawal', 'storeWithdrawal')->name('storeWithdrawal');
});