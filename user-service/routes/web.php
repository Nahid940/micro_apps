<?php

use Illuminate\Support\Facades\Route;


Route::prefix('users')->group(function () {
    Route::get('/', function () {
        echo "User Service";
    });
});
