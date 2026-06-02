<?php
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Photo\PhotoController;
use App\Http\Controllers\Photo\LikeController;
use App\Http\Controllers\Photo\SaveController;
use App\Http\Controllers\Photo\CommentController;
use App\Http\Controllers\User\ProfileController;
use App\Http\Controllers\User\SearchController;
use App\Http\Controllers\User\FollowController;
use App\Http\Controllers\EventController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\EngagementController;
use App\Http\Controllers\Admin\EventController as AdminEventController;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// ── PUBLIC ────────────────────────────────────────────────
Route::get('/', [PhotoController::class, 'landing'])->name('landing');
Route::get('/photos/{photo}', [PhotoController::class, 'show'])->name('photos.show');
Route::get('/photos/{photo}/download', [PhotoController::class, 'download'])->name('photos.download');
Route::get('/events', [EventController::class, 'index'])->name('events.index');
Route::get('/events/{event}', [EventController::class, 'show'])->name('events.show');

// ── AUTH ──────────────────────────────────────────────────
Route::middleware('guest')->group(function () {
    Route::get('/login', fn() => redirect()->route('landing'))->name('login');
    Route::get('/register', fn() => redirect()->route('landing'))->name('register');
    Route::post('/login', [AuthController::class, 'login']);
    Route::post('/register', [AuthController::class, 'register']);
});

Route::post('/logout', [AuthController::class, 'logout'])
    ->middleware('auth')->name('logout');

Route::get('/auth/google', [AuthController::class, 'redirectToGoogle'])->name('auth.google');
Route::get('/auth/google/callback', [AuthController::class, 'handleGoogleCallback'])->name('auth.google.callback');

// ── EMAIL VERIFICATION ────────────────────────────────────
Route::middleware('auth')->group(function () {
    Route::get('/email/verify', fn() => view('auth.verify-email'))
        ->name('verification.notice');

    Route::get('/email/verify/{id}/{hash}', function (EmailVerificationRequest $r) {
        $r->fulfill();
        return redirect()->route('dashboard')->with('success', 'Email berhasil diverifikasi!');
    })->middleware('signed')->name('verification.verify');

    Route::post('/email/verification-notification', function (Request $r) {
        $r->user()->sendEmailVerificationNotification();
        return back()->with('message', 'Link verifikasi baru sudah dikirim!');
    })->middleware('throttle:6,1')->name('verification.send');
});

// ── USER (auth + verified) ────────────────────────────────
Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/dashboard', [PhotoController::class, 'dashboard'])->name('dashboard');

    Route::get('/upload', [PhotoController::class, 'showUpload'])->name('upload');
    Route::post('/upload', [PhotoController::class, 'upload']);

    Route::delete('/photos/{photo}', [PhotoController::class, 'destroy'])->name('photos.destroy');

    Route::post('/photos/{photo}/like', [LikeController::class, 'toggle'])->name('photos.like');
    Route::post('/photos/{photo}/save', [SaveController::class, 'toggle'])->name('photos.save');
    Route::post('/photos/{photo}/comment', [CommentController::class, 'store'])->name('photos.comment');

    Route::get('/saved', [SaveController::class, 'index'])->name('saved');

    Route::get('/profile', [ProfileController::class, 'index'])->name('profile');
    Route::get('/profile/password', [ProfileController::class, 'changePasswordPage'])->name('profile.password.page');
    Route::post('/profile/password', [ProfileController::class, 'changePassword'])->name('profile.password');
    Route::post('/profile/avatar', [ProfileController::class, 'updateAvatar'])->name('profile.avatar');

    Route::post('/users/{user}/follow', [FollowController::class, 'toggle'])
        ->name('users.follow');

    Route::get('/search', [SearchController::class, 'index'])->name('search');
});

// ── ADMIN ─────────────────────────────────────────────────
Route::middleware(['auth', 'verified', 'is_admin'])
    ->prefix('admin')->name('admin.')->group(function () {
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
        Route::get('/engagement', [EngagementController::class, 'index'])->name('engagement');
        Route::delete('/photos/{photo}', [PhotoController::class, 'destroy'])->name('photos.destroy');

        Route::get('/events', [AdminEventController::class, 'index'])->name('events.index');
        Route::get('/events/create', [AdminEventController::class, 'create'])->name('events.create');
        Route::post('/events', [AdminEventController::class, 'store'])->name('events.store');
        Route::get('/events/{event}', [AdminEventController::class, 'show'])->name('events.show');
        Route::get('/events/{event}/edit', [AdminEventController::class, 'edit'])->name('events.edit');
        Route::put('/events/{event}', [AdminEventController::class, 'update'])->name('events.update');
        Route::patch('/events/{event}/status', [AdminEventController::class, 'updateStatus'])->name('events.status');
        Route::delete('/events/{event}', [AdminEventController::class, 'destroy'])->name('events.destroy');
    });