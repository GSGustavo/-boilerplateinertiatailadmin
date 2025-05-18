<?php

use App\Http\Controllers\BackOffice\DashboardIndexController;
use App\Http\Controllers\BackOffice\PlansController;
use App\Http\Controllers\BackOffice\PlansItemsController;
use App\Http\Controllers\BackOffice\SubscriptionRenewalsController;
use App\Http\Controllers\BackOffice\SubscriptionsController;
use App\Http\Controllers\IndexController;
use Illuminate\Support\Facades\Route;

Route::domain(env("APP_URL"))->group(function () {
    Route::controller(IndexController::class)->group(function () {
        Route::get("/", "show")->name("index.show");
    });
});

Route::domain("dash." . env("APP_URL"))->middleware(['auth:sanctum', config('jetstream.auth_session'), 'verified', 'Admin'])->group(function () {


    Route::controller(DashboardIndexController::class)->group(function () {
        Route::get("/", "show")->name("backoffice.admin.index");
    });

     Route::controller(PlansController::class)->group(function () {
        Route::get("/plans", 'show')->name("backoffice.admin.plans.show");
        Route::post("/plans/save", 'save')->name("backoffice.admin.plans.save");
    });

    Route::controller(PlansItemsController::class)->group(function () {
        Route::post('/planitems/delete', "delete")->name('backoffice.admin.planitems.delete');
    });

    Route::controller(SubscriptionsController::class)->group(function () {
        Route::get("/subscriptions", 'show')->name("backoffice.admin.subscriptions.show");
        Route::post("/subscriptions/save", 'save')->name("backoffice.admin.subscriptions.save");
    });
    Route::controller(SubscriptionRenewalsController::class)->group(function () {

        Route::post("/subscriptionsrenewals/save", 'save')->name("backoffice.admin.subscriptionsrenewals.save");
    });


    include("api/dash/api.php");
});