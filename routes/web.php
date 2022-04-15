<?php

use App\Http\Controllers\Game\GameController;
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

Route::get('game', [GameController::class, 'index'])->name('game');
Route::post('new', [GameController::class, 'new'])->name('new');
Route::get('play', [GameController::class, 'play'])->name('play');
Route::post('record', [GameController::class, 'record'])->name('record');
Route::post('round', [GameController::class, 'round'])->name('round');
