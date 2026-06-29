<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API v1
|--------------------------------------------------------------------------
| Public, read-only endpoints powering the Next.js storefront live under
| /api/v1. Phase 2 fills this with categories, products, home, pages, etc.
| Authenticated/customer routes (future commerce) will get their own group.
*/

Route::prefix('v1')->name('api.v1.')->group(function () {
    Route::get('/health', function () {
        return response()->json([
            'data' => [
                'status' => 'ok',
                'app' => config('app.name'),
                'locales' => config('dermovive.locales'),
                'default_locale' => config('dermovive.default_locale'),
            ],
        ]);
    })->name('health');
});

// Authenticated user (Sanctum) — kept for future customer/admin token flows.
Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');
