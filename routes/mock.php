<?php

use App\Http\Controllers\mock\MockProviderController;
use Illuminate\Support\Facades\Route;

Route::get('provider-a', [MockProviderController::class, 'ProviderA']);
Route::get('provider-b', [MockProviderController::class, 'ProviderB']);
Route::get('provider-c', [MockProviderController::class, 'ProviderC']);
