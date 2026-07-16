<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\GoogleController;
use App\Http\Controllers\ModuleController;
use Illuminate\Support\Facades\Http;

Route::get('/test-api', function () {
    $apiKey = env('GEMINI_API_KEY');
    $url = "https://generativelanguage.googleapis.com/v1beta/models?key={$apiKey}";

    try {
        // Menggunakan withoutVerifying() untuk melewati pengecekan SSL yang sering bermasalah di Laragon
        $response = Http::withoutVerifying()->get($url);

        if ($response->successful()) {
            return $response->json();
        } else {
            return "Gagal! Kode Status: " . $response->status() . " - Pesan: " . $response->body();
        }
    } catch (\Exception $e) {
        return "Error Fatal: " . $e->getMessage();
    }
});
// Rute untuk Modul
Route::middleware('auth')->group(function () {
    Route::get('/buat-modul', [ModuleController::class, 'create'])->name('modules.create');
    Route::post('/buat-modul', [ModuleController::class, 'store'])->name('modules.store');
    Route::get('/arsip', [ModuleController::class, 'history'])->name('modules.history');
    Route::get('/modul/{module}', [ModuleController::class, 'show'])->name('modules.show');
    Route::post('/modul/{module}/retry', [ModuleController::class, 'retry'])->name('modules.retry');
    Route::post('/modul/{module}/step', [ModuleController::class, 'generateStep'])->name('modules.step');
    Route::post('/modul/{module}/game', [ModuleController::class, 'generateGame'])->name('modules.game');
    Route::get('/modul/{module}/game/download', [ModuleController::class, 'downloadGame'])->name('modules.game.download');
    Route::post('/modul/{module}/image', [ModuleController::class, 'generateImage'])->name('modules.image');
    Route::delete('/modul/{module}/media/{index}', [ModuleController::class, 'deleteMedia'])->name('modules.media.destroy');
    Route::get('/modul/{module}/download', [ModuleController::class, 'download'])->name('modules.download');
    Route::delete('/modul/{module}', [ModuleController::class, 'destroy'])->name('modules.destroy');
});
// Rute untuk Google SSO
Route::get('/auth/google/redirect', [GoogleController::class, 'redirect'])->name('google.redirect');
Route::get('/auth/google/callback', [GoogleController::class, 'callback'])->name('google.callback');

Route::get('/', function () {
    return redirect()->route('login');
});

// Menyajikan foto profil langsung dari storage tanpa bergantung pada symlink
// `storage:link` (supaya tetap berfungsi di server/Laragon Windows yang kadang
// gagal membuat symlink).
Route::get('/avatar/{filename}', function (string $filename) {
    $safeName = basename($filename);
    $path = storage_path('app/public/avatars/'.$safeName);

    abort_unless(file_exists($path), 404);

    return response()->file($path);
})->name('avatar.show');

Route::middleware('auth')->group(function () {
    Route::get('/syarat-ketentuan', fn() => view('pages.terms'))->name('terms');
    Route::get('/bantuan', fn() => view('pages.faq'))->name('faq');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    Route::post('/profile/avatar', [ProfileController::class, 'updateAvatar'])->name('profile.avatar.update');
    Route::delete('/profile/avatar', [ProfileController::class, 'destroyAvatar'])->name('profile.avatar.destroy');
});

require __DIR__.'/auth.php';
