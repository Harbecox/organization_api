<?php

use App\Http\Controllers\ActibityController;
use App\Http\Controllers\ActivityController;
use App\Http\Controllers\BuildingController;
use App\Http\Controllers\OrganizationController;
use App\Http\Middleware\StaticTokenMiddleware;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::middleware([StaticTokenMiddleware::class])->group(function () {
    Route::prefix('organizations')->group(function () {
        Route::get('/', [OrganizationController::class, 'index']);
        Route::get('/radius', [OrganizationController::class, 'findInRadius']);
        Route::get('/rectangle', [OrganizationController::class, 'findInRectangle']);
        Route::get('/name/{name}', [OrganizationController::class, 'findByName']);
        Route::get('/{organizationId}', [OrganizationController::class, 'show']);
    });
    Route::prefix('buildings')->group(function (){
        Route::get('/', [BuildingController::class, 'index']);
        Route::get('/{buildingId}/organizations', [BuildingController::class, 'getOrganizations']);
    });
    Route::prefix('activities')->group(function (){
        Route::get('/', [ActivityController::class, 'index']);
        Route::get('/{activityId}/organizations', [ActivityController::class, 'getOrganizations']);
    });
});
