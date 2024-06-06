<?php

use App\Http\Controllers\Admin\ArticleController;
use App\Http\Controllers\Admin\User\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::apiResource('user', UserController::class);
Route::apiResource('article', ArticleController::class)->only(['index']);
