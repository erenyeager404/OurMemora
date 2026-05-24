<?php

use App\Http\Controllers\FollowController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\PhotoController;
use App\Http\Controllers\LikeController;
use App\Http\Controllers\SaveController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\EngagementController;
use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\EmailVerificationRequest;

// Landing page
Route::get('/', function () {
    $photos = \App\Models\Photo::where('status', 'public')
        ->with('user')
        ->latest()
        ->take(12)
        ->get();
    return view('landing', compact('photos'));
})->name('landing');

//login 

Route::get('/login', fn() => redirect()->route('landing'))->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::get('/register', fn() => redirect()->route('landing'))->name('register');
Route::post('/register', [AuthController::class, 'register']);

//verifikasi email

Route::get('/email/verify', function () {
    return view('auth.verify-email');
})->middleware('auth')->name('verification.notice');

Route::get('/email/verify/{id}/{hash}', function (EmailVerificationRequest $request) {
    $request->fulfill();
    return redirect()->route('dashboard')
        ->with('success', 'Email berhasil diverifikasi! Selamat datang.');
})->middleware(['auth', 'signed'])->name('verification.verify');

Route::post('/email/verification-notification', function (Request $request) {
    $request->user()->sendEmailVerificationNotification();
    return back()->with('message', 'Link verifikasi baru sudah dikirim ke email kamu!');
})->middleware(['auth', 'throttle:6,1'])->name('verification.send');


Route::middleware(['auth', 'verified'])->group(function () {
    // Dashboard & Photo management
    Route::get('/dashboard', [PhotoController::class, 'dashboard'])->name('dashboard');
    Route::get('/upload', [PhotoController::class, 'showUpload'])->name('upload');
    Route::post('/upload', [PhotoController::class, 'upload']);
    Route::delete('/photos/{photo}', [PhotoController::class, 'destroy'])->name('photos.destroy');

    // Logout
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    // Profile
    Route::get('/profile', [ProfileController::class, 'index'])->name('profile');
    Route::post('/profile/password', [ProfileController::class, 'changePassword'])->name('profile.password');

    // Saved photos
    Route::get('/saved', [SaveController::class, 'index'])->name('saved');

    // Interactions
    Route::post('/photos/{photo}/like', [LikeController::class, 'toggle'])->name('photos.like');
    Route::post('/photos/{photo}/save', [SaveController::class, 'toggle'])->name('photos.save');
    Route::post('/photos/{photo}/comment', [CommentController::class, 'store'])->name('photos.comment');

    //Foto Download
    Route::get('/photos/{photo}/download', [PhotoController::class, 'download'])->name('photos.download');

    Route::post('/users/{user}/follow', [FollowController::class, 'toggle'])->name('users.follow');

});

// Admin routes
Route::middleware(['auth', 'verified', 'is_admin'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
        Route::get('/engagement', [EngagementController::class, 'index'])->name('engagement');
        Route::delete('/photos/{photo}', [PhotoController::class, 'destroy'])->name('photos.destroy');
    });

//Route Sharelink

Route::get('/photo/{photo}', [PhotoController::class, 'show'])->name('photos.show');
