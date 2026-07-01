<?php

use App\Http\Controllers\Api\V1\CategoryController;
use App\Http\Controllers\Api\V1\HomeController;
use App\Http\Controllers\Api\V1\PageController;
use App\Http\Controllers\Api\V1\ProductController;
use App\Http\Controllers\Api\V1\SettingController;
use App\Http\Controllers\Api\V1\TagController;
use App\Http\Middleware\SetLocale;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API v1
|--------------------------------------------------------------------------
| Public, read-only endpoints powering the Next.js storefront live under
| /api/v1. SetLocale negotiates the request language (?locale= →
| Accept-Language → default) so translatable models return the right content.
| Authenticated/customer routes (future commerce) will get their own group.
*/

Route::prefix('v1')->name('api.v1.')->middleware(SetLocale::class)->group(function () {
    Route::get('/health', function () {
        return response()->json([
            'data' => [
                'status' => 'ok',
                'app' => config('app.name'),
                'locale' => app()->getLocale(),
                'locales' => config('dermovive.locales'),
                'default_locale' => config('dermovive.default_locale'),
            ],
        ]);
    })->name('health');

    Route::get('/home', [HomeController::class, 'index'])->name('home');

    Route::get('/categories', [CategoryController::class, 'index'])->name('categories.index');
    Route::get('/categories/{slug}', [CategoryController::class, 'show'])->name('categories.show');

    Route::get('/products', [ProductController::class, 'index'])->name('products.index');
    Route::get('/products/{slug}', [ProductController::class, 'show'])->name('products.show');

    Route::get('/tags', [TagController::class, 'index'])->name('tags.index');

    Route::get('/pages/{slug}', [PageController::class, 'show'])->name('pages.show');

    Route::get('/settings', [SettingController::class, 'index'])->name('settings.index');
});

// Authenticated user (Sanctum) — kept for future customer/admin token flows.
Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');
