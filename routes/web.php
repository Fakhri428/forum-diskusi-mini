<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ThreadController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\VoteController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Moderator\DashboardController as ModeratorDashboardController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\ThreadController as AdminThreadController;
use App\Http\Controllers\Admin\CommentController as AdminCommentController;
use App\Http\Controllers\Moderator\ThreadController as ModeratorThreadController;
use App\Http\Controllers\Moderator\CommentController as ModeratorCommentController;
use App\Http\Controllers\Moderator\ReportController;

// Rute publik - HARUS SEBELUM AUTH ROUTES
Route::get('/', function () {
    return view('welcome');
});

// PINDAHKAN ROUTE CREATE KE SINI - SEBELUM ROUTE SHOW
Route::middleware('auth')->group(function () {
    Route::get('/threads/create', [ThreadController::class, 'create'])->name('threads.create');
});

// Public thread routes (tanpa auth)
Route::get('/threads', [ThreadController::class, 'index'])->name('threads.index');
Route::get('/threads/{thread}', [ThreadController::class, 'show'])->name('threads.show');

// Public category routes
Route::get('/categories', [CategoryController::class, 'index'])->name('categories.index');
Route::get('/categories/{category}', [CategoryController::class, 'show'])->name('categories.show');


// Rute autentikasi
Auth::routes();

// Protected routes - PERLU AUTH
Route::middleware('auth')->group(function () {
    Route::get('/home', function() {
        return view('home');
    })->name('home');

    // Thread management - CREATE SUDAH DIPINDAH KE ATAS
    Route::post('/threads', [ThreadController::class, 'store'])->name('threads.store');
    Route::get('/threads/{thread}/edit', [ThreadController::class, 'edit'])->name('threads.edit');
    Route::put('/threads/{thread}', [ThreadController::class, 'update'])->name('threads.update');
    Route::delete('/threads/{thread}', [ThreadController::class, 'destroy'])->name('threads.destroy');

    // Comment management
    Route::post('/threads/{thread}/comments', [CommentController::class, 'store'])->name('comments.store');

    // Comment routes
    Route::get('/comments/{comment}/edit', [CommentController::class, 'edit'])->name('comments.edit');
    Route::put('/comments/{comment}', [CommentController::class, 'update'])->name('comments.update');
    Route::delete('/comments/{comment}', [CommentController::class, 'destroy'])->name('comments.destroy');

    // Vote routes - PERBAIKIAN DI SINI
    Route::post('/comments/{comment}/vote', [VoteController::class, 'voteComment'])->name('vote.comment');
    Route::post('/threads/{thread}/vote', [VoteController::class, 'voteThread'])->name('vote.thread');

    // User personal pages
    Route::get('/diskusi-saya', function() {
        $threads = Auth::user()->threads()->with('category')->latest()->paginate(10);
        return view('my-threads', compact('threads'));
    })->name('my-threads');

    Route::get('/komentar-saya', function() {
        $comments = Auth::user()->comments()->with('thread')->latest()->paginate(10);
        return view('my-comments', compact('comments'));
    })->name('comments.my');

    Route::get('/statistik', function() {
        return view('statistics');
    })->name('statistics');

    Route::get('/notifikasi', function() {
        $user = Auth::user();
        $notifications = collect();
        $recent_activities = collect();

        $comments_on_threads = \App\Models\Comment::whereIn('thread_id', $user->threads()->pluck('id'))
                                                 ->with(['user', 'thread'])
                                                 ->latest()
                                                 ->take(20)
                                                 ->get();

        return view('notifications', compact('notifications', 'recent_activities', 'comments_on_threads'));
    })->name('notifications');
});

// User Profile Routes
Route::middleware(['auth'])->group(function() {
    Route::get('/profile', [ProfileController::class, 'show'])->name('profile');
    Route::get('/profile/edit', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/profile/update', [ProfileController::class, 'update'])->name('profile.update');
    Route::put('/password/update', [ProfileController::class, 'updatePassword'])->name('password.update');
});

// Admin routes
Route::middleware(['auth', \App\Http\Middleware\RoleMiddleware::class . ':admin'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {
        Route::get('/', [AdminDashboardController::class, 'index'])->name('dashboard');

        // Users management
        Route::resource('users', UserController::class);
        Route::put('users/{user}/toggle-ban', [UserController::class, 'toggleBan'])->name('users.toggle-ban');
        Route::put('users/{user}/toggle-role', [UserController::class, 'toggleRole'])->name('users.toggle-role');
        Route::post('users/batch-action', [UserController::class, 'batchAction'])->name('users.batch-action');
        Route::get('users/{user}/verify-email', [UserController::class, 'verifyEmail'])->name('users.verify-email');
        Route::get('users/{user}/ban', [UserController::class, 'ban'])->name('users.ban');
        Route::get('users/{user}/unban', [UserController::class, 'unban'])->name('users.unban');

        // Categories management
        Route::resource('categories', CategoryController::class);
        Route::post('categories/reorder', [CategoryController::class, 'reorder'])->name('categories.reorder');

        // TAMBAHKAN route ini SETELAH resource routes
        Route::put('categories/{category}/toggle-active', [App\Http\Controllers\Admin\CategoryController::class, 'toggleActive'])
             ->name('categories.toggle-active');

        // Thread management
        Route::resource('threads', AdminThreadController::class);
        Route::post('threads/batch-action', [AdminThreadController::class, 'batchAction'])->name('threads.batch-action');
        Route::put('threads/{thread}/toggle-approval', [AdminThreadController::class, 'toggleApproval'])->name('threads.toggle-approval');
        Route::put('threads/{thread}/toggle-pinned', [AdminThreadController::class, 'togglePinned'])->name('threads.toggle-pinned');
        Route::put('threads/{thread}/toggle-locked', [AdminThreadController::class, 'toggleLocked'])->name('threads.toggle-locked');

        // Comment management
        Route::resource('comments', AdminCommentController::class);
        Route::post('comments/batch-action', [AdminCommentController::class, 'batchAction'])->name('comments.batch-action');
        Route::put('comments/{comment}/toggle-approval', [AdminCommentController::class, 'toggleApproval'])->name('comments.toggle-approval');
        Route::get('comments/get-by-thread/{threadId}', [AdminCommentController::class, 'getByThread'])->name('comments.get-by-thread');

        // Admin profile
        Route::get('profile', [App\Http\Controllers\Admin\ProfileController::class, 'index'])->name('profile');
        Route::put('profile', [App\Http\Controllers\Admin\ProfileController::class, 'update'])->name('profile.update');
        Route::put('profile/password', [App\Http\Controllers\Admin\ProfileController::class, 'updatePassword'])->name('profile.password');
    });

// Moderator Routes
Route::middleware(['auth', \App\Http\Middleware\RoleMiddleware::class . ':moderator,admin'])
    ->prefix('moderator')
    ->name('moderator.')
    ->group(function () {
        Route::get('/', [ModeratorDashboardController::class, 'index'])->name('dashboard');

        // Thread management
        Route::resource('threads', ModeratorThreadController::class);
        Route::post('threads/batch-moderate', [ModeratorThreadController::class, 'batchModerate'])->name('threads.batch-moderate');

        // Comment management
        Route::resource('comments', ModeratorCommentController::class);
        Route::post('comments/batch-delete', [ModeratorCommentController::class, 'batchDelete'])->name('comments.batch-delete');

        // Report management
        Route::resource('reports', ReportController::class);
        Route::post('reports/batch-update', [ReportController::class, 'batchUpdate'])->name('reports.batch-update');

        // Moderator profile
        Route::get('profile', [App\Http\Controllers\Moderator\ProfileController::class, 'index'])->name('profile');
        Route::put('profile', [App\Http\Controllers\Moderator\ProfileController::class, 'update'])->name('profile.update');
        Route::put('profile/password', [App\Http\Controllers\Moderator\ProfileController::class, 'updatePassword'])->name('profile.password');
    });

// Pastikan route ini ada di dalam middleware auth
Route::middleware(['auth'])->group(function () {
    // Comment routes - PASTIKAN ROUTE INI ADA
    Route::post('threads/{thread}/comments', [App\Http\Controllers\CommentController::class, 'store'])
         ->name('comments.store');

    Route::get('comments/{comment}/edit', [App\Http\Controllers\CommentController::class, 'edit'])
         ->name('comments.edit');

    Route::put('comments/{comment}', [App\Http\Controllers\CommentController::class, 'update'])
         ->name('comments.update');

    Route::delete('comments/{comment}', [App\Http\Controllers\CommentController::class, 'destroy'])
         ->name('comments.destroy');

    // Vote routes
    Route::post('vote/thread/{thread}', [App\Http\Controllers\VoteController::class, 'voteThread'])
         ->name('vote.thread');

    Route::post('vote/comment/{comment}', [App\Http\Controllers\VoteController::class, 'voteComment'])
         ->name('vote.comment');
});



