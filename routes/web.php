<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return response([
        'service' => 'iBox Flight Aggregator API',
        'version' => 'v1',
        'status'  => 'running',
    ]);
});
