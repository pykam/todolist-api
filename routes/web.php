<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function() {
    abort(405, 'Method not allowed');
});
