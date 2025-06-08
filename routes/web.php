<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ThreadController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\VoteController;
use App\Http\Controllers\ProfileController;

Route::get('/', function () {
    return view('welcome');
});

Auth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
Route::get('/admin', fn() => 'Admin Page')->middleware(['auth', 'role:admin']);
Route::get('/moderator', fn() => 'Moderator Page')->middleware(['auth', 'role:moderator']);
Route::get('/member', fn() => 'Member Page')->middleware(['auth', 'role:member']);
Route::resource('threads', ThreadController::class)->middleware('auth');

// Comment routes
Route::post('/threads/{thread}/comments', [CommentController::class, 'store'])->name('comments.store');
Route::put('/comments/{comment}', [CommentController::class, 'update'])->name('comments.update');
Route::delete('/comments/{comment}', [CommentController::class, 'destroy'])->name('comments.destroy');

// Vote routes
Route::post('/threads/{thread}/vote', [VoteController::class, 'voteThread'])->name('vote.thread');
Route::post('/comments/{comment}/vote', [VoteController::class, 'voteComment'])->name('vote.comment');

// Thread management
Route::get('/threads/{thread}/edit', [ThreadController::class, 'edit'])->name('threads.edit');
Route::put('/threads/{thread}', [ThreadController::class, 'update'])->name('threads.update');
Route::delete('/threads/{thread}', [ThreadController::class, 'destroy'])->name('threads.destroy');

Route::middleware(['auth'])->group(function () {
    Route::get('/profile', [ProfileController::class, 'index'])->name('profile');

    // New sidebar menu routes
    Route::get('/statistik', function() {
        return view('statistics');
    })->name('statistics');

    Route::get('/diskusi-saya', function() {
        return view('my-threads');
    })->name('my-threads');

    Route::get('/komentar-saya', function() {
        return view('my-comments');
    })->name('my-comments');

    Route::get('/notifikasi', function() {
        return view('notifications');
    })->name('notifications');
});
