<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\FabricController;
use App\Http\Controllers\Api\OrderController;
use App\Http\Controllers\Api\MessageController;
use App\Http\Controllers\Api\MeasurementTemplateController;
use App\Http\Controllers\Api\ClientController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\ItemTypeController;
use App\Http\Controllers\Api\FitController;
use App\Http\Controllers\Api\ModelAnatomyController;
use App\Http\Controllers\Api\MarkerPlanController;
use App\Http\Controllers\Api\ActivityLogController;
use App\Http\Controllers\Api\MediaFileController;

// Public routes
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login'])->name('login');

// Protected routes
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/me', [AuthController::class, 'me']);

    // ── Categories ──
    Route::get('/categories', [CategoryController::class, 'index'])->middleware('permission:categories.view');
    Route::get('/categories/suggest', [CategoryController::class, 'suggest'])->middleware('permission:categories.view');
    Route::get('/categories/{category}', [CategoryController::class, 'show'])->middleware('permission:categories.view');
    Route::post('/categories', [CategoryController::class, 'store'])->middleware('permission:categories.create');
    Route::put('/categories/{category}', [CategoryController::class, 'update'])->middleware('permission:categories.edit');
    Route::delete('/categories/{category}', [CategoryController::class, 'destroy'])->middleware('permission:categories.delete');

    // ── Item Types ──
    Route::get('/item-types', [ItemTypeController::class, 'index'])->middleware('permission:item_types.view');
    Route::post('/item-types', [ItemTypeController::class, 'store'])->middleware('permission:item_types.create');
    Route::get('/item-types/{item_type}', [ItemTypeController::class, 'show'])->middleware('permission:item_types.view');
    Route::put('/item-types/{item_type}', [ItemTypeController::class, 'update'])->middleware('permission:item_types.edit');
    Route::delete('/item-types/{item_type}', [ItemTypeController::class, 'destroy'])->middleware('permission:item_types.delete');

    // ── Fits ──
    Route::get('/fits', [FitController::class, 'index'])->middleware('permission:fit_types.view');
    Route::post('/fits', [FitController::class, 'store'])->middleware('permission:fit_types.create');
    Route::get('/fits/{fit}', [FitController::class, 'show'])->middleware('permission:fit_types.view');
    Route::put('/fits/{fit}', [FitController::class, 'update'])->middleware('permission:fit_types.edit');
    Route::delete('/fits/{fit}', [FitController::class, 'destroy'])->middleware('permission:fit_types.delete');

    // ── Fabrics ──
    Route::get('/fabrics', [FabricController::class, 'index'])->middleware('permission:fabrics.view');
    Route::post('/fabrics', [FabricController::class, 'store'])->middleware('permission:fabrics.create');
    Route::put('/fabrics/{fabric}', [FabricController::class, 'update'])->middleware('permission:fabrics.edit');
    Route::delete('/fabrics/{fabric}', [FabricController::class, 'destroy'])->middleware('permission:fabrics.delete');

    // ── Measurement Templates ──
    Route::get('/measurement-templates', [MeasurementTemplateController::class, 'index'])->middleware('permission:measurements.view');
    Route::post('/measurement-templates', [MeasurementTemplateController::class, 'store'])->middleware('permission:measurements.create');
    Route::get('/measurement-templates/{categoryId}', [MeasurementTemplateController::class, 'show'])->middleware('permission:measurements.view');
    Route::get('/measurement-templates/{id}/export', [MeasurementTemplateController::class, 'export'])->middleware('permission:measurements.export');
    Route::post('/measurement-templates/{id}/import', [MeasurementTemplateController::class, 'import'])->middleware('permission:measurements.create');

    // ── Clients ──
    Route::get('/clients', [ClientController::class, 'index'])->middleware('permission:clients.view');
    Route::get('/clients/stats', [ClientController::class, 'stats'])->middleware('permission:clients.view');
    Route::get('/clients/{user}', [ClientController::class, 'show'])->middleware('permission:clients.view');

    // ── Orders ──
    Route::get('/orders', [OrderController::class, 'index'])->middleware('permission:orders.view');
    Route::get('/orders/stats', [OrderController::class, 'stats'])->middleware('permission:orders.view');
    Route::post('/orders', [OrderController::class, 'store'])->middleware('permission:orders.create');
    Route::get('/orders/export', [OrderController::class, 'export'])->middleware('permission:orders.export');
    Route::get('/orders/{order}', [OrderController::class, 'show'])->middleware('permission:orders.view');
    Route::patch('/orders/{order}', [OrderController::class, 'update'])->middleware('permission:orders.edit');
    Route::get('/orders/{order}/tech-pack', [OrderController::class, 'getTechPack'])->middleware('permission:orders.view');
    Route::post('/orders/{order}/tech-pack', [OrderController::class, 'saveTechPack'])->middleware('permission:orders.edit');
    Route::put('/orders/{order}/tech-pack', [OrderController::class, 'saveTechPack'])->middleware('permission:orders.edit');
    Route::post('/orders/{order}/production', [\App\Http\Controllers\OrderProductionController::class, 'store'])->middleware('permission:orders.edit');
    Route::patch('/orders/{order}/status', [OrderController::class, 'updateStatus'])->middleware('permission:orders.approve');
    Route::delete('/orders/{order}', [OrderController::class, 'destroy'])->middleware('permission:orders.delete');

    // ── Messages ──
    Route::get('/orders/{orderId}/messages', [MessageController::class, 'index'])->middleware('permission:orders.view');
    Route::post('/messages', [MessageController::class, 'store'])->middleware('permission:orders.view');

    // ── Designs ──
    Route::get('/designs', [\App\Http\Controllers\Api\DesignController::class, 'index'])->middleware('permission:designs.view');
    Route::post('/designs', [\App\Http\Controllers\Api\DesignController::class, 'store'])->middleware('permission:designs.create');
    Route::get('/designs/{design}', [\App\Http\Controllers\Api\DesignController::class, 'show'])->middleware('permission:designs.view');
    Route::put('/designs/{design}', [\App\Http\Controllers\Api\DesignController::class, 'update'])->middleware('permission:designs.edit');
    Route::delete('/designs/{design}', [\App\Http\Controllers\Api\DesignController::class, 'destroy'])->middleware('permission:designs.delete');

    // ── Products ──
    Route::get('/products', [\App\Http\Controllers\Api\ProductController::class, 'index'])->middleware('permission:products.view');
    Route::post('/products', [\App\Http\Controllers\Api\ProductController::class, 'store'])->middleware('permission:products.create');
    Route::get('/products/{product}', [\App\Http\Controllers\Api\ProductController::class, 'show'])->middleware('permission:products.view');
    Route::put('/products/{product}', [\App\Http\Controllers\Api\ProductController::class, 'update'])->middleware('permission:products.edit');
    Route::delete('/products/{product}', [\App\Http\Controllers\Api\ProductController::class, 'destroy'])->middleware('permission:products.delete');

    // ── Model Anatomy & Marker Planning ──
    Route::apiResource('marker-plans', MarkerPlanController::class)->middleware('permission:orders.view');
    Route::apiResource('model-anatomies', ModelAnatomyController::class)->middleware('permission:orders.view');
    Route::get('/model-anatomies/suggest', [ModelAnatomyController::class, 'suggest'])->middleware('permission:orders.view');

    // ── Activity Log ──
    Route::get('/activity-logs', [ActivityLogController::class, 'index'])->middleware('permission:settings.view');

    // ── Media Files ──
    Route::apiResource('media-files', MediaFileController::class)->middleware('permission:designs.view');

    // ── Users & Roles (admin module) ──
    Route::get('/users', [UserController::class, 'index'])->middleware('permission:team.view');
    Route::get('/users/counts', [UserController::class, 'countsByRole'])->middleware('permission:team.view');
    Route::post('/users', [UserController::class, 'store'])->middleware('permission:team.create');
    Route::put('/users/{user}', [UserController::class, 'update'])->middleware('permission:team.edit');
    Route::delete('/users/{user}', [UserController::class, 'destroy'])->middleware('permission:team.delete');
    Route::patch('/users/{user}/toggle-status', [UserController::class, 'toggleStatus'])->middleware('permission:team.edit');

    // Profile (own user — no permission needed)
    Route::post('/me/profile', [UserController::class, 'updateProfile']);
    Route::post('/me/change-password', [UserController::class, 'changePassword']);

    // ── Permissions & Roles (settings module) ──
    Route::get('/permissions', [\App\Http\Controllers\Api\PermissionMatrixController::class, 'index'])->middleware('permission:settings.view');
    Route::post('/permissions', [\App\Http\Controllers\Api\PermissionMatrixController::class, 'update'])->middleware('permission:settings.edit');

    Route::get('/roles', [\App\Http\Controllers\Api\RolesController::class, 'index'])->middleware('permission:roles.view');
    Route::post('/roles', [\App\Http\Controllers\Api\RolesController::class, 'store'])->middleware('permission:roles.edit');
    Route::put('/roles/{role}', [\App\Http\Controllers\Api\RolesController::class, 'update'])->middleware('permission:roles.edit');
    Route::delete('/roles/{role}', [\App\Http\Controllers\Api\RolesController::class, 'destroy'])->middleware('permission:roles.delete');
    Route::patch('/roles/{role}/toggle-status', [\App\Http\Controllers\Api\RolesController::class, 'toggleStatus'])->middleware('permission:roles.edit');
});
