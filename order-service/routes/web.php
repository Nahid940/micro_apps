<?php

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});


Route::get('/test-call', function () {
    return Http::get('http://user-service:8000/api/users')->json();
});