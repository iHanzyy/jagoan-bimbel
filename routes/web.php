<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;

Route::redirect('/', '/login');

Route::view('/login', 'auth.login')->name('login');

Route::view('/admin/materi', 'admin.materi.index')->name('admin.materi.index');

Route::view('/admin/materi/create', 'admin.materi.create')->name('admin.materi.create');

Route::get('/admin/materi/{id}/edit', static function (int $id) {
    return view('admin.materi.edit', ['materiId' => $id]);
})->name('admin.materi.edit');
