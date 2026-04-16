<?php

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Route;


Route::get('/test-call', function () {
    return Http::get('http://user-service:8000/api/all')->json();
});