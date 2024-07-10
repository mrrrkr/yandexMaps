<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\MarkController;
use App\Http\Controllers\AdminController;
use App\Http\Middleware\IsAdmin;

Route::get('/', function () 
{
    return view('welcome');
});

Route::middleware(
[
  'auth:sanctum',
  config('jetstream.auth_session'),
  'verified',
])->group(function () 
{
    Route::get('/marks.index', function () 
    {
        return view('marks');
    })->name('marks');
});

Route::resource('marks', MarkController::class);

Route::middleware(IsAdmin::class)->group(function () 
{
    Route::get('/admin', [AdminController::class, 'index'])->name('admin');
});

Route::get('/locations/{userId}', [AdminController::class, 'getLocations'])->name('locations');