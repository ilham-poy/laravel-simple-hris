<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});
// Tambahkan rute ini agar route('login') terdefinisi
Route::get('/login', function () {
    return 'Halaman Login - HandToHand'; // Nanti ganti dengan view/controller login kamu
})->name('login');
