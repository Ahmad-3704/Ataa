<?php

use App\Http\Controllers\Api\ProfileController; // ضفها فوق مع الـ use

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\AdminAuthController;

// ------------------ مسارات المستخدمين العاديين ------------------
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

// ------------------ مسارات لوحة تحكم المدراء ------------------
Route::post('/admin/register', [AdminAuthController::class, 'register']);
Route::post('/admin/login', [AdminAuthController::class, 'login']);

// ------------------ المسارات المحمية (تحتاج توكن) ------------------
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::post('/admin/logout', [AdminAuthController::class, 'logout']);
});
// مسارات الجمعية (يجب أن يكون مسجل دخول + دوره جمعية)
Route::middleware(['auth:sanctum', 'role:organization'])->group(function () {
    Route::get('/organization/dashboard', function () {
        return response()->json(['message' => 'مرحباً بك في لوحة تحكم الجمعية']);
    });
});

// مسارات المتبرع (يجب أن يكون مسجل دخول + دوره متبرع)
Route::middleware(['auth:sanctum', 'role:donor'])->group(function () {
    Route::get('/donor/dashboard', function () {
        return response()->json(['message' => 'مرحباً بك في واجهة المتبرع']);
    });
});

// ضف هذا السطر داخل مجموعة auth:sanctum
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::post('/admin/logout', [AdminAuthController::class, 'logout']);

    // --- المسار الجديد لاستكمال البروفايل ---
    Route::post('/profile/complete', [ProfileController::class, 'completeProfile']);
});
// ==========================================
    // مسارات المشاريع الخيرية (Projects)
    // ==========================================

    // مسار عرض كل المشاريع (متاح لأي شخص مسجل الدخول)
    Route::get('/projects', [App\Http\Controllers\Api\ProjectController::class, 'index']);

    // مسار إضافة مشروع (محمي بصلاحية "الجمعية" فقط)
   Route::middleware(['auth:sanctum', 'role:organization'])->group(function () {
    Route::post('/projects', [App\Http\Controllers\Api\ProjectController::class, 'store']);
});
