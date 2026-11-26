<?php

use Illuminate\Support\Facades\Route;

Route::redirect('/', '/admin');
Route::redirect('/admin/login', '/admin');
Route::redirect('/portal/login', '/portal');
