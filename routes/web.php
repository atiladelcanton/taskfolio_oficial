<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;

Route::get('/', fn () => redirect('app'))->name('home');
Route::get('/force-download-id/{evidence}', function (App\Models\TaskEvidence $evidence) {
    abort_unless(request()->hasValidSignature(), 403);

    $path = $evidence->file;

    abort_unless(Storage::exists($path), 404);

    return Storage::download($path, basename($path));
})->middleware('auth')->name('force_download');
