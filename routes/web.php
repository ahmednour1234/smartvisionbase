<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/secure-storage/{path}', function (string $path) {
    abort_unless(request()->hasValidSignature(true), 403);

    abort_unless(Storage::disk('public')->exists($path), 404);

    return Storage::disk('public')->response($path);
})
    ->where('path', '.*')
    ->name('secure.storage');