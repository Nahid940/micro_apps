<?php

use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;




Route::prefix('services')->group(function () {

    

    Route::post('create', [UserController::class, 'store'])->name('users.create');


    Route::get('/', function () {
        echo "Response sent!";

        phpinfo();
        die;
        
        if (function_exists('fastcgi_finish_request')) {
            fastcgi_finish_request();
        }

        // Simulate heavy task
        sleep(5);
        file_put_contents(storage_path('logs/test.log'), 'Done at ' . now() . PHP_EOL, FILE_APPEND);
    });


    Route::get('/all', function () {
        return response()->json([
            ['id' => 1, 'name' => 'Nahid']
        ]);
    });

});



