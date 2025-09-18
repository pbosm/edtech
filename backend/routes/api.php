<?php

use App\Models\User;
use Illuminate\Support\Facades\Route;

Route::get("/health", fn() => response()->json(["ok" => true]));

Route::get('/users', function () {
    return User::query()
        ->select('id','name','email','created_at')
        ->orderByDesc('id')
        ->paginate(10);
});
