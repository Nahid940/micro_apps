<?php

use Illuminate\Support\Facades\Route;


Route::get('/', function () {
    echo "from u server";
});


Route::prefix('users')->group(function () {
    Route::get('/all', function () {
        return response()->json([
            ['id' => 1, 'name' => 'Nahid']
        ]);
    });
});

