<?php

use Illuminate\Http\Request;
use App\Http\Controllers\Api\AgentTaskController;
use App\Http\Controllers\Api\NotificationController;
use App\Http\Controllers\Api\AdminProjectController;
use App\Http\Controllers\Api\AutoDonationController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\AdminAuthController;
use App\Http\Controllers\Api\DonationController;
use App\Http\Controllers\Api\WalletController;
use App\Http\Controllers\Api\ProjectController;
use App\Http\Controllers\Api\ProfileController;
use App\Http\Controllers\Api\LeaderboardController;

// ==========================================
// المسارات العامة (متاحة لأي شخص)
// ==========================================
Route::get('/projects', [ProjectController::class, 'index']);
Route::get('/projects/urgent', [ProjectController::class, 'urgent']); // مسار الحالات العاجلة الجديد
Route::get('/projects/{id}', [ProjectController::class, 'show']); // مسار عرض تفاصيل مشروع
Route::get('/leaderboard', [LeaderboardController::class, 'topDonors']); // مسار لوحة الصدارة

// ------------------ مسارات المستخدمين العاديين ------------------
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

// ------------------ مسارات لوحة تحكم المدراء ------------------
Route::post('/admin/register', [AdminAuthController::class, 'register']);
Route::post('/admin/login', [AdminAuthController::class, 'login']);

// ==========================================
// المسارات المحمية العامة (تحتاج توكن فقط لأي دور)
// ==========================================
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::post('/admin/logout', [AdminAuthController::class, 'logout']);

    // استكمال البروفايل وتعديله وكلمة المرور (متاح للجميع)
    Route::post('/profile/complete', [ProfileController::class, 'completeProfile']);
    Route::put('/profile/info', [ProfileController::class, 'updateInfo']);
    Route::put('/profile/password', [ProfileController::class, 'updatePassword']);

    // مسار التبرع وعرض السجل وشحن المحفظة
    Route::post('/donations', [DonationController::class, 'store']);
    Route::get('/donations', [DonationController::class, 'index']);
    Route::post('/wallet/top-up', [WalletController::class, 'topUp']);

    // مسارات الإشعارات (جديد)
    Route::get('/notifications', [NotificationController::class, 'index']);
    Route::put('/notifications/{id}/read', [NotificationController::class, 'markAsRead']);
    Route::put('/notifications/read-all', [NotificationController::class, 'markAllAsRead']);
});

// ==========================================
// مسارات الجمعية (مسجل دخول + دور جمعية فقط)
// ==========================================
Route::middleware(['auth:sanctum', 'role:organization'])->group(function () {
    Route::get('/organization/dashboard', function () {
        return response()->json(['message' => 'مرحباً بك في لوحة تحكم الجمعية']);
    });

    // إضافة مشروع جديد (خاص بالجمعيات فقط)
    Route::post('/projects', [ProjectController::class, 'store']);
});

// ==========================================
// مسارات المتبرع (مسجل دخول + دور متبرع فقط)
// ==========================================
Route::middleware(['auth:sanctum', 'role:donor'])->group(function () {
    Route::get('/donor/dashboard', function () {
        return response()->json(['message' => 'مرحباً بك في واجهة المتبرع']);
    });

    // مسارات التبرع التلقائي
    Route::get('/auto-donations', [AutoDonationController::class, 'index']);
    Route::post('/auto-donations', [AutoDonationController::class, 'store']);
    Route::put('/auto-donations/status', [AutoDonationController::class, 'toggleStatus']);
});

// ==========================================
// مسارات الإدارة (مسجل دخول + دور أدمن فقط)
// ==========================================
Route::middleware(['auth:sanctum', 'role:admin'])->group(function () {
    // عرض المشاريع المعلقة
    Route::get('/admin/projects/pending', [AdminProjectController::class, 'pendingProjects']);

    // تغيير حالة المشروع (قبول أو رفض)
    Route::put('/admin/projects/{id}/status', [AdminProjectController::class, 'updateStatus']);
});
// ==========================================
// مسارات المندوب (مسجل دخول + دور مندوب فقط)
// ==========================================
Route::middleware(['auth:sanctum', 'role:agent'])->group(function () {
    // عرض مهام المندوب
    Route::get('/agent/tasks', [AgentTaskController::class, 'index']);

    // مسح الـ QR لتأكيد المهمة
    Route::post('/agent/tasks/scan', [AgentTaskController::class, 'scanQrCode']);
});
