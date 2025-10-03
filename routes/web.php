<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\EventController;
use App\Http\Controllers\ParticipantController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ParticipantPDFController;

// الصفحة الرئيسية توجيه إلى Events
Route::get('/', function () {
    return redirect()->route('events.index');
});

Route::get('/dashboard', function () {
    return redirect()->route('events.index');
})->middleware(['auth'])->name('dashboard');

// جميع الـ routes الخاصة بالمستخدمين مسموح بها بعد تسجيل الدخول
Route::middleware(['auth'])->group(function () {

    // Events CRUD
    Route::get('/events', [EventController::class, 'index'])->name('events.index');
    Route::get('/events/create', [EventController::class, 'create'])->name('events.create');
    Route::post('/events', [EventController::class, 'store'])->name('events.store');
    Route::get('/events/{event}/edit', [EventController::class, 'edit'])->name('events.edit');
    Route::put('/events/{event}', [EventController::class, 'update'])->name('events.update');
    Route::delete('/events/{event}', [EventController::class, 'destroy'])->name('events.destroy');

    // Routes الخاصة بالمشاركين داخل كل حدث
    Route::prefix('events/{event}/participants')->group(function () {

        // إضافة مشارك يدويًا
        Route::post('/', [ParticipantController::class, 'store'])->name('participants.store');

        // حذف جميع المشاركين
        Route::delete('/delete-all', [ParticipantController::class, 'destroyAll'])->name('participants.destroyAll');

        // حذف مشارك واحد
        Route::delete('/{participant}', [ParticipantController::class, 'destroy'])->name('participants.destroy');

        // رفع Excel وحفظ مؤقت في session
        Route::post('/upload', [ParticipantController::class, 'uploadExcel'])->name('participants.uploadExcel');

        // إضافة المشاركين من الملف المرفوع إلى DB
        Route::post('/add-uploaded', [ParticipantController::class, 'addUploadedParticipants'])->name('participants.addUploaded');

        // استيراد Excel مباشر (اختياري)
        Route::post('/import-excel', [ParticipantController::class, 'importExcel'])->name('participants.importExcel');


    });

    // تصدير Excel
    Route::get('/events/{event}/participants/export', [ParticipantController::class, 'exportExcel'])
        ->name('participants.export');

    // تصدير PDF
    Route::get('/events/{event}/pdf', [ParticipantPDFController::class, 'exportPdf'])->name('events.pdf');

    // Profile routes
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// Breeze auth routes
require __DIR__ . '/auth.php';
