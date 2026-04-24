<?php

use Illuminate\Support\Facades\Route;


Route::prefix('orders')->group(function () {
    Route::get('/', function () {
        echo "Order Service";
    });
});


// Route::get('/test-call', function () {
//     return Http::get('http://user-service:8000/api/users')->json();
// });