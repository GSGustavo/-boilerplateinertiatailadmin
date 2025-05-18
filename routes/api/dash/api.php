<?php
use App\Http\Controllers\BackOffice\PlansController;
use App\Http\Controllers\BackOffice\SubscriptionsController;
use App\Http\Controllers\BackOffice\UserController;
use Illuminate\Support\Facades\Route;

Route::controller(UserController::class)->group(function() {
    Route::post("/api/users/get", 'getitems')->name("api.backoffice.admin.users.get");
});

Route::controller(PlansController::class)->group(function () {
    Route::post("/api/plans/get", 'getitems')->name("api.backoffice.admin.plans.get");
});

Route::controller(SubscriptionsController::class)->group(function () {
    Route::post("/api/subscriptions/get", 'getitems')->name("api.backoffice.admin.subscriptions.get");
    Route::post("/api/subscriptions/plans/get", 'getplansitems')->name("api.backoffice.admin.subscriptions.plans.get");
});