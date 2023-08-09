<?php

declare(strict_types=1);

use CybrixSolutions\EasyPost\Http\Controllers\WebhooksController;
use Illuminate\Support\Facades\Route;

Route::prefix(config('easypost.route_prefix'))->group(function () {
    Route::view('/legal/ups-license', 'easypost::pages.legal.ups-license-agreement')
        ->name('easypost::legal.ups_license');
});

Route::post(config('easypost.webhook_url'), WebhooksController::class)->name('easypost::webhooks');
