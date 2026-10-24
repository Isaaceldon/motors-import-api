<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\BrandController;
use App\Http\Controllers\Api\ContactRequestController;
use App\Http\Controllers\Api\FavoriteController;
use App\Http\Controllers\Api\FeatureController;
use App\Http\Controllers\Api\NotificationController;
use App\Http\Controllers\Api\OrderController;
use App\Http\Controllers\Api\ReservationController;
use App\Http\Controllers\Api\SparePartController;
use App\Http\Controllers\Api\VehicleController;
use Illuminate\Support\Facades\Route;

Route::post('auth/register', [AuthController::class, 'register']);
Route::post('auth/login', [AuthController::class, 'login']);
Route::post('auth/forgot-password', [AuthController::class, 'forgotPassword']);
Route::post('auth/reset-password', [AuthController::class, 'resetPassword']);

Route::get('brands', [BrandController::class, 'index']);
Route::get('features', [FeatureController::class, 'index']);

Route::get('vehicles', [VehicleController::class, 'index']);
Route::get('vehicles/{vehicle}', [VehicleController::class, 'show']);
Route::get('vehicles/{vehicle}/similar', [VehicleController::class, 'similar']);

Route::get('spare-parts', [SparePartController::class, 'index']);
Route::get('spare-parts/{sparePart}', [SparePartController::class, 'show']);

Route::post('reservations', [ReservationController::class, 'store']);

Route::post('contact-requests', [ContactRequestController::class, 'store']);

Route::post('orders', [OrderController::class, 'store']);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('auth/logout', [AuthController::class, 'logout']);
    Route::get('auth/me', [AuthController::class, 'me']);
    Route::patch('auth/me', [AuthController::class, 'updateProfile']);
    Route::put('auth/password', [AuthController::class, 'updatePassword']);

    Route::get('reservations', [ReservationController::class, 'index']);
    Route::get('reservations/{reservation}', [ReservationController::class, 'show']);

    Route::get('orders', [OrderController::class, 'index']);

    Route::get('favorites', [FavoriteController::class, 'index']);
    Route::post('favorites/{vehicle}', [FavoriteController::class, 'store']);
    Route::delete('favorites/{vehicle}', [FavoriteController::class, 'destroy']);

    Route::get('notifications', [NotificationController::class, 'index']);
    Route::get('notifications/unread-count', [NotificationController::class, 'unreadCount']);
    Route::post('notifications/{id}/read', [NotificationController::class, 'markAsRead']);
    Route::post('notifications/mark-all-read', [NotificationController::class, 'markAllAsRead']);
});
