<?php

declare(strict_types=1);

use App\Http\Controllers\Api\AdminMateriController;
use App\Http\Controllers\Api\SiswaMateriController;
use App\Models\FileMateri;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth:sanctum', 'role:admin,siswa'])
    ->get('/list-materi', [SiswaMateriController::class, 'index'])
    ->middleware('can:viewAny,' . FileMateri::class);

Route::middleware(['auth:sanctum', 'role:admin'])->group(function (): void {
    Route::get('/show-materi/{fileMateri}', [AdminMateriController::class, 'show'])
        ->middleware('can:view,fileMateri');

    Route::post('/upload-materi', [AdminMateriController::class, 'store'])
        ->middleware('can:create,' . FileMateri::class);

    Route::get('/materi-download/{fileMateri}', [AdminMateriController::class, 'download'])
        ->middleware('can:download,fileMateri');
});

Route::middleware(['auth:sanctum', 'role:siswa'])->group(function (): void {
    Route::get('/detail-materi/{fileMateri}', [SiswaMateriController::class, 'show'])
        ->middleware('can:view,fileMateri');
});
