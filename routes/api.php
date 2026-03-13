<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\BookController;
use App\Http\Controllers\BorrowController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\StatistiqueController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');


Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);



Route::middleware('auth:sanctum')->group(function () {

    Route::post('/logout', [AuthController::class, 'logout']);

    Route::apiResource('categories', CategoryController::class)->only(['index' , 'show'])  ;
    Route::apiResource('books', BookController::class)->only(['index' , 'show'])  ;

    Route::post('/books/{book}/borrow', [BorrowController::class, 'emprunter']) ;
    Route::post('/borrows/{borrow}/return', [BorrowController::class, 'retourner']) ;

    Route::get('/books/statistics/most/viewed', [StatistiqueController::class, 'mostViewedBooks']) ;
    Route::get('/books/statistics/most/borrowed', [StatistiqueController::class, 'mostBorrowedBooks']) ;



    Route::middleware('is_admin')->group(function () {
        Route::apiResource('categories', CategoryController::class) ->except(['index' , 'show']) ;
        Route::apiResource('books', BookController::class) ->except(['index' , 'show']) ;
    });

});




