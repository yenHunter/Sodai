<?php
require __DIR__ . '/../routes/admin.php';

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('visitor.pages.index');
});
