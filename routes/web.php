<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;

Route::prefix(config('easypost.route_prefix'))->group(function () {
    Route::view('/legal/ups-license', 'easypost::pages.legal.ups-license-agreement')
        ->name('easypost::legal.ups_license');
});
