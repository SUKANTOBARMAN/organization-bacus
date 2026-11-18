<?php

use App\Http\Controllers\Api\Admin;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\PublicController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function () {

    /* ---------- পাবলিক ---------- */
    Route::get('settings', [PublicController::class, 'settings']);
    Route::get('sliders', [PublicController::class, 'sliders']);
    Route::get('videos', [PublicController::class, 'videos']);
    Route::get('home', [PublicController::class, 'home']);
    Route::get('pages/{slug}', [PublicController::class, 'page']);
    Route::get('committee', [PublicController::class, 'committee']);
    Route::get('committees', [PublicController::class, 'committees']);
    Route::get('committees/{id}', [PublicController::class, 'committeeShow']);
    Route::get('commission', [PublicController::class, 'commission']);
    Route::get('elections/{id}', [PublicController::class, 'electionShow']);
    Route::get('notices', [PublicController::class, 'notices']);
    Route::get('notices/{id}', [PublicController::class, 'noticeShow']);
    Route::get('posts', [PublicController::class, 'posts']);
    Route::get('posts/{id}', [PublicController::class, 'postShow']);
    Route::get('albums', [PublicController::class, 'albums']);
    Route::get('albums/{id}', [PublicController::class, 'albumShow']);
    Route::get('documents', [PublicController::class, 'documents']);
    Route::get('members', [PublicController::class, 'members']);
    Route::post('contact', [PublicController::class, 'contact'])->middleware('throttle:5,1');
    Route::post('membership-applications', [PublicController::class, 'membershipApply'])->middleware('throttle:3,10');

    Route::post('auth/login', [AuthController::class, 'login'])->middleware('throttle:10,1');

    /* ---------- অ্যাডমিন (টোকেন লাগবে) ---------- */
    Route::prefix('admin')->middleware(['auth:sanctum', 'active'])->group(function () {
        Route::get('me', [AuthController::class, 'me']);
        Route::post('logout', [AuthController::class, 'logout']);
        Route::put('password', [AuthController::class, 'changePassword']);
        Route::get('dashboard', [Admin\DashboardController::class, 'index']);

        // এডিটর, অ্যাডমিন, সুপার অ্যাডমিন — সবাই
        Route::apiResource('notices', Admin\NoticeController::class);
        Route::apiResource('posts', Admin\PostController::class);
        Route::post('posts/{id}/images', [Admin\PostController::class, 'upload']);
        Route::apiResource('post-images', Admin\PostImageController::class)->only(['index', 'update', 'destroy']);
        Route::apiResource('sliders', Admin\SliderController::class);
        Route::apiResource('videos', Admin\VideoController::class);
        Route::apiResource('documents', Admin\DocumentController::class);
        Route::apiResource('pages', Admin\PageController::class);
        Route::apiResource('albums', Admin\AlbumController::class);
        Route::post('albums/{id}/photos', [Admin\AlbumController::class, 'upload']);
        Route::apiResource('photos', Admin\PhotoController::class)->except(['show']);

        // অ্যাডমিন ও সুপার অ্যাডমিন
        Route::middleware('role:super_admin,admin')->group(function () {
            Route::get('members/export', [Admin\MemberController::class, 'export']);
            Route::apiResource('members', Admin\MemberController::class);
            Route::apiResource('designations', Admin\DesignationController::class);
            Route::get('payments/summary', [Admin\PaymentController::class, 'summary']);
            Route::apiResource('payments', Admin\PaymentController::class);
            Route::get('membership-applications', [Admin\MembershipApplicationController::class, 'index']);
            Route::get('membership-applications/{id}', [Admin\MembershipApplicationController::class, 'show']);
            Route::post('membership-applications/{id}/approve', [Admin\MembershipApplicationController::class, 'approve']);
            Route::post('membership-applications/{id}/reject', [Admin\MembershipApplicationController::class, 'reject']);
            Route::delete('membership-applications/{id}', [Admin\MembershipApplicationController::class, 'destroy']);
            Route::apiResource('election-candidates', Admin\ElectionCandidateController::class);
            Route::get('activity-logs', [Admin\ActivityLogController::class, 'index']);
            Route::post('settings/logo', [Admin\SettingController::class, 'logo']);
            Route::apiResource('committees', Admin\CommitteeController::class);
            Route::apiResource('committee-members', Admin\CommitteeMemberController::class);
            Route::apiResource('commission-members', Admin\CommissionMemberController::class);
            Route::apiResource('elections', Admin\ElectionController::class);
            Route::get('settings', [Admin\SettingController::class, 'index']);
            Route::put('settings', [Admin\SettingController::class, 'update']);
            Route::apiResource('contact-messages', Admin\ContactMessageController::class)->only(['index', 'show', 'destroy']);
        });

        // শুধু সুপার অ্যাডমিন
        Route::middleware('role:super_admin')->group(function () {
            Route::apiResource('users', Admin\UserController::class);
        });
    });
});
