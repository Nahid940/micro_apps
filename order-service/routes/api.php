<?php

use Illuminate\Support\Facades\Route;

Route::prefix('orders')->group(function () {
    Route::get('/test-call', function () {
        echo "from order";
        // phpinfo();
        echo "done";
    });
});

