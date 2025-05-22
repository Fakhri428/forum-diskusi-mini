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
Route::post('/threads/{thread}/comments', [CommentController::class, 'store'])->name('comments.store');
Route::post('/threads/{thread}/vote', [VoteController::class, 'voteThread'])->name('vote.thread');
Route::post('/comments/{comment}/vote', [VoteController::class, 'voteComment'])->name('vote.comment');
// Menampilkan form edit
Route::get('/threads/{thread}/edit', [ThreadController::class, 'edit'])->name('threads.edit');

// Proses update data
Route::put('/threads/{thread}', [ThreadController::class, 'update'])->name('threads.update');

// Proses hapus data
Route::delete('/threads/{thread}', [ThreadController::class, 'destroy'])->name('threads.destroy');



Route::middleware(['auth'])->group(function () {
    Route::get('/profile', [ProfileController::class, 'index'])->name('profile');
});
//Route::post('/comments/ajax', [CommentController::class, 'storeAjax'])->name('comments.store.ajax');





