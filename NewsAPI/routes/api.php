<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\NewsFeedController;
use App\Http\Controllers\Api\UserPreferenceController;
use App\Http\Controllers\Api\NewsPreferencesController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/


Route::prefix('users')->group(function () {

    Route::prefix('auth')->as('auth.')->controller(AuthController::class)->group(function () {
        Route::post('/login', 'login')->name('login');
        Route::post('/register', 'register')->name('register');
        Route::post('/logout', 'logout')->name('logout')->middleware('auth:sanctum');
    });

    Route::middleware(['auth:sanctum','checkToken'])->group(function () {

        Route::get('/news-feed', [NewsFeedController::class, 'getNewsFeed'])->name('user.showNewsFeed');

        Route::prefix('userPreferences')->as('userPreferences.')->group(function () {
            Route::get('/', [UserPreferenceController::class, 'get'])->name('get');
            Route::put('/', [UserPreferenceController::class, 'update'])->name('update');
        });
    });
});


Route::prefix('news')->group(function () {

    Route::middleware('auth:sanctum')->group(function () {

        Route::controller(NewsFeedController::class)->group(function () {
            Route::post('/search', 'searchArticles')->name('searchArticles');
            Route::post('/filter', 'filterArticles')->name('filterArticles');
        });

        Route::prefix('preferences')->as('preferences.')->controller(NewsPreferencesController::class)->group(function () {
            Route::get('/categories', 'getCategories')->name('getCategories');
            Route::get('/sources', 'getSources')->name('getSources');
            Route::get('/authors', 'getAuthors')->name('getAuthors');
        });
    });
});
