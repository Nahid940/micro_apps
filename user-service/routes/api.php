<?php

use Illuminate\Support\Facades\Route;


Route::get('/all', function () {
    return response()->json([
        ['id' => 1, 'name' => 'Nahid']
    ]);
});
