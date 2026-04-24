<?php

use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;


Route::get('/', function () {
    echo "Response sent!";
    
    if (function_exists('fastcgi_finish_request')) {
        fastcgi_finish_request();
    }

    // Simulate heavy task
    sleep(5);
    file_put_contents(storage_path('logs/test.log'), 'Done at ' . now() . PHP_EOL, FILE_APPEND);
});


Route::prefix('users')->group(function () {
//     Route::get('/all', function () {
//         return response()->json([
//             ['id' => 1, 'name' => 'Nahid']
//         ]);
//     });

    Route::post('create', [UserController::class, 'store'])->name('users.create');
});



