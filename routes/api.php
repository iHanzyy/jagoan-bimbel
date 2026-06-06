<?php

declare(strict_types=1);

use App\Http\Controllers\Api\AdminMateriController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\SiswaMateriController;
use App\Models\FileMateri;
use Illuminate\Support\Facades\Route;

Route::post('/login', [AuthController::class, 'login']);

Route::middleware(['auth:sanctum'])->post('/logout', [AuthController::class, 'logout']);

Route::middleware(['auth:sanctum', 'role:admin,siswa'])
    ->get('/list-materi', [SiswaMateriController::class, 'index'])
    ->middleware('can:viewAny,' . FileMateri::class);

Route::middleware(['auth:sanctum', 'role:admin', 'ability:admin'])->group(function (): void {
    Route::get('/show-materi/{fileMateri}', [AdminMateriController::class, 'show'])
        ->middleware('can:view,fileMateri');

    Route::post('/upload-materi', [AdminMateriController::class, 'store'])
        ->middleware('can:create,' . FileMateri::class);

    Route::put('/update-materi/{fileMateri}', [AdminMateriController::class, 'update'])
        ->middleware('can:update,fileMateri');

    Route::delete('/delete-materi/{fileMateri}', [AdminMateriController::class, 'destroy'])
        ->middleware('can:delete,fileMateri');

    Route::get('/materi-download/{fileMateri}', [AdminMateriController::class, 'download'])
        ->middleware('can:download,fileMateri');
});

Route::middleware(['auth:sanctum', 'role:siswa', 'ability:siswa'])->group(function (): void {
    Route::get('/detail-materi/{fileMateri}', [SiswaMateriController::class, 'show'])
        ->middleware('can:view,fileMateri');
});
