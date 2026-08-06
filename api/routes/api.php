<?php

use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\StoreSettingController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\Admin\ProductController as AdminProductController;
use App\Http\Controllers\Api\Admin\CategoryController as AdminCategoryController;
use App\Http\Controllers\Api\Admin\StoreSettingController as AdminStoreSettingController;
use App\Http\Controllers\Api\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Api\BrandController;
use App\Http\Controllers\Api\Admin\BrandController as AdminBrandController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\BannerController;
use App\Http\Controllers\Api\Admin\BannerController as AdminBannerController;
use App\Http\Controllers\Api\FaqController;
use App\Http\Controllers\Api\Admin\FaqController as AdminFaqController;


Route::post('/v1/admin/login', [AuthController::class, 'login'])
    ->middleware('throttle:5,1');

Route::middleware('auth:sanctum')->prefix('v1/admin')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/me', [AuthController::class, 'me']);

    Route::apiResource('products', AdminProductController::class)->except(['show']);
    Route::patch('/products/{product}/toggle-active', [AdminProductController::class, 'toggleActive']);
    Route::delete('/products/{product}/images/{mediaId}', [AdminProductController::class, 'deleteImage']);

    Route::apiResource('categories', AdminCategoryController::class)->except(['show']);

    Route::apiResource('brands', AdminBrandController::class)->except(['show']);

    Route::apiResource('banners', AdminBannerController::class)->except(['show']);

    Route::apiResource('faqs', AdminFaqController::class)->except(['show']);
    Route::delete('/settings/about-images/{mediaId}', [AdminStoreSettingController::class, 'deleteAboutImage']);

    Route::get('/settings', [AdminStoreSettingController::class, 'show']);
    Route::put('/settings', [AdminStoreSettingController::class, 'update']);
    Route::get('/dashboard/stats', [AdminDashboardController::class, 'stats']);
});

Route::prefix('v1')->group(function () {
    Route::get('/settings', [StoreSettingController::class, 'show']);
    Route::get('/categories', [CategoryController::class, 'index']);
    Route::get('/products', [ProductController::class, 'index']);
    Route::get('/products/{slug}', [ProductController::class, 'show']);
    Route::get('/brands', [BrandController::class, 'index']);
    Route::get('/faqs', [FaqController::class, 'index']);
    Route::get('/banners', [BannerController::class, 'index']);
});
