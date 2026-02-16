<?php

use App\Http\Controllers\AccountController;
use App\Http\Controllers\AccountDepositController;
use App\Http\Controllers\AccountNotificationController;
use App\Http\Controllers\AccountWalletController;
use App\Http\Controllers\AccountOrderController;
use App\Http\Controllers\AccountVipController;
use App\Http\Controllers\AgencyRequestController;
use App\Http\Controllers\Admin\DepositController as AdminDepositController;
use App\Http\Controllers\Admin\CategoryController as AdminCategoryController;
use App\Http\Controllers\Admin\OrderController as AdminOrderController;
use App\Http\Controllers\Admin\OpsController as AdminOpsController;
use App\Http\Controllers\Admin\OpsOrderController as AdminOpsOrderController;
use App\Http\Controllers\Admin\PageController as AdminPageController;
use App\Http\Controllers\Admin\AgencyRequestController as AdminAgencyRequestController;
use App\Http\Controllers\Admin\AgencyRequestFieldController as AdminAgencyRequestFieldController;
use App\Http\Controllers\Admin\ReportsController as AdminReportsController;
use App\Http\Controllers\Admin\UserController as AdminUserController;
use App\Http\Controllers\Admin\PaymentMethodController as AdminPaymentMethodController;
use App\Http\Controllers\Admin\ServiceController as AdminServiceController;
use App\Http\Controllers\Admin\ServiceFormFieldController as AdminServiceFormFieldController;
use App\Http\Controllers\Admin\ServiceVariantController as AdminServiceVariantController;
use App\Http\Controllers\Admin\BannerController as AdminBannerController;
use App\Http\Controllers\Admin\AppearanceController as AdminAppearanceController;
use App\Http\Controllers\Admin\SiteSettingsController as AdminSiteSettingsController;
use App\Http\Controllers\Admin\PopupController as AdminPopupController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DepositController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ServiceController;
use Illuminate\Support\Facades\Route;

Route::get('/lang/{locale}', function ($locale) {
    if (in_array($locale, ['en', 'ar'])) {
        session(['locale' => $locale]);
    }
    return back();
})->name('lang.switch');

Route::middleware(['auth', 'not_banned'])->group(function () {
    Route::post('/otp/verify', [\App\Http\Controllers\Auth\OtpController::class, 'verify'])->name('otp.verify');
    Route::post('/otp/resend', [\App\Http\Controllers\Auth\OtpController::class, 'resend'])->name('otp.resend');

    Route::get('/', [HomeController::class, 'index'])->name('home');
    Route::get('/categories/{category:slug}', [CategoryController::class, 'show'])->name('categories.show');
    Route::get('/services/{service:slug}', [ServiceController::class, 'show'])->name('services.show');
    Route::get('/privacy-policy', fn() => view('pages.privacy-policy'))->name('privacy-policy');
    Route::get('/about', fn() => view('pages.about'))->name('about');
    Route::get('/agency-request', [AgencyRequestController::class, 'create'])->name('agency-requests.create');
    Route::post('/agency-request', [AgencyRequestController::class, 'store'])->name('agency-requests.store');

    Route::get('/dashboard', [AdminController::class, 'index'])->name('dashboard');
    Route::get('/account', [AccountController::class, 'index'])->name('account');
    Route::post('/account/update', [AccountController::class, 'update'])->name('account.update')->middleware('role:admin');
    Route::get('/account/deposits', [AccountDepositController::class, 'index'])->name('account.deposits');
    Route::get('/account/deposits/{depositRequest}', [AccountDepositController::class, 'show'])->name('account.deposits.show');
    Route::get('/account/wallet', [AccountWalletController::class, 'index'])->name('account.wallet');
    Route::get('/account/orders', [AccountOrderController::class, 'index'])->name('account.orders');
    Route::get('/account/orders/{order}', [AccountOrderController::class, 'show'])->name('account.orders.show');
    Route::get('/account/vip', [AccountVipController::class, 'show'])->name('account.vip');
    Route::get('/account/notifications', [AccountNotificationController::class, 'index'])->name('account.notifications');
    Route::post('/account/notifications/mark-all-read', [AccountNotificationController::class, 'markAllRead'])->name('account.notifications.mark-all-read');
    Route::post('/account/notifications/{id}/mark-read', [AccountNotificationController::class, 'markAsRead'])->name('account.notifications.mark-read');
    Route::get('/account/change-password', [AccountController::class, 'changePassword'])->name('account.password.change');
    Route::post('/account/change-password', [AccountController::class, 'updatePassword'])->name('account.password.update');


    Route::get('/deposit', [DepositController::class, 'index'])->name('deposit.index');
    Route::get('/deposit/{paymentMethod:slug}', [DepositController::class, 'show'])->name('deposit.show');
    Route::post('/deposit/{paymentMethod:slug}', [DepositController::class, 'store'])->name('deposit.store')->middleware(['not_frozen', \App\Http\Middleware\EnsureAccountVerified::class]);

    Route::post('/services/{service:slug}/purchase', [ServiceController::class, 'purchase'])->name('services.purchase')->middleware(['not_frozen', \App\Http\Middleware\EnsureAccountVerified::class]);
    
    // Quick VIP Check
    Route::get('/check-vip', function() {
        $user = auth()->user();
        $user->load('vipStatus.vipTier');
        
        $html = '<style>body{font-family:monospace;padding:20px;background:#1e293b;color:#e2e8f0;}</style>';
        $html .= '<h1 style="color:#10b981;">VIP Discount Debug</h1>';
        $html .= '<p><strong>User:</strong> ' . $user->name . ' (ID: ' . $user->id . ')</p>';
        
        if (!$user->vipStatus) {
            $html .= '<p style="color:#ef4444;">❌ NO VIP STATUS</p>';
            $html .= '<p>Visit <a href="/debug/vip-discount" style="color:#3b82f6;">/debug/vip-discount</a> to assign test VIP status</p>';
            return $html;
        }
        
        $html .= '<p style="color:#10b981;">✅ VIP Status exists</p>';
        
        if (!$user->vipStatus->vipTier) {
            $html .= '<p style="color:#ef4444;">❌ NO VIP TIER</p>';
            return $html;
        }
        
        $tier = $user->vipStatus->vipTier;
        $html .= '<p style="color:#10b981;">✅ VIP Tier: ' . $tier->title_en . '</p>';
        $html .= '<p><strong>Discount:</strong> <span style="color:#10b981;font-size:24px;">' . $tier->discount_percentage . '%</span></p>';
        
        if ($tier->discount_percentage > 0) {
            $html .= '<p style="color:#10b981;">✅ DISCOUNT SHOULD BE SHOWING</p>';
            $html .= '<p>If not showing, clear cache and refresh (Ctrl+Shift+R)</p>';
        } else {
            $html .= '<p style="color:#f59e0b;">⚠️ Discount is 0%</p>';
        }
        
        return $html;
    })->name('check.vip');
    
    // Update VIP Discounts
    Route::get('/update-vip-discounts', function() {
        $updates = [
            1 => 2,   // Rank 1 = 2%
            2 => 4,   // Rank 2 = 4%
            3 => 6,   // Rank 3 = 6%
            4 => 8,   // Rank 4 = 8%
            5 => 10,  // Rank 5 = 10%
            6 => 12,  // Rank 6 = 12%
        ];
        
        $html = '<style>body{font-family:monospace;padding:20px;background:#1e293b;color:#e2e8f0;}</style>';
        $html .= '<h1 style="color:#10b981;">Update VIP Discounts</h1>';
        
        foreach ($updates as $rank => $discount) {
            $tier = \App\Models\VipTier::where('rank', $rank)->first();
            if ($tier) {
                $tier->discount_percentage = $discount;
                $tier->save();
                $html .= '<p style="color:#10b981;">✅ Rank ' . $rank . ' (' . $tier->title_en . '): ' . $discount . '%</p>';
            } else {
                $html .= '<p style="color:#ef4444;">❌ Rank ' . $rank . ': Tier not found</p>';
            }
        }
        
        $html .= '<h2 style="color:#10b981;margin-top:20px;">Updated VIP Tiers</h2>';
        $tiers = \App\Models\VipTier::orderBy('rank')->get();
        foreach ($tiers as $tier) {
            $html .= '<p>Rank ' . $tier->rank . ': ' . $tier->title_en . ' - <strong style="color:#10b981;">' . $tier->discount_percentage . '%</strong></p>';
        }
        
        $html .= '<p style="margin-top:20px;"><a href="/check-vip" style="color:#3b82f6;">Check your VIP status</a></p>';
        
        return $html;
    })->name('update.vip.discounts');
    
    // VIP Debug Routes (remove in production)
    Route::get('/debug/vip-discount', function() {
        return view('debug.vip-discount');
    })->name('debug.vip-discount');
    
    Route::post('/admin/test/assign-vip', function() {
        $user = auth()->user();
        $tier = \App\Models\VipTier::where('rank', 2)->first();
        if ($tier) {
            \App\Models\UserVipStatus::updateOrCreate(
                ['user_id' => $user->id],
                [
                    'vip_tier_id' => $tier->id,
                    'lifetime_spent' => 500,
                    'calculated_at' => now()
                ]
            );
            return redirect()->route('debug.vip-discount')->with('status', 'VIP status assigned successfully!');
        }
        return redirect()->route('debug.vip-discount')->with('error', 'VIP tier not found');
    })->name('admin.test.assign-vip');
});

Route::middleware(['auth', 'not_banned', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/', [AdminController::class, 'index'])->name('index');
    Route::get('/ops', [AdminOpsController::class, 'index'])->name('ops.index');
    Route::post('/ops/orders/{order}/start-processing', [AdminOpsOrderController::class, 'startProcessing'])->name('ops.orders.start-processing');
    Route::post('/ops/orders/{order}/mark-done', [AdminOpsOrderController::class, 'markDone'])->name('ops.orders.mark-done');
    Route::post('/ops/orders/{order}/reject', [AdminOpsOrderController::class, 'reject'])->name('ops.orders.reject');
    Route::get('/reports', [AdminReportsController::class, 'index'])->name('reports.index');
    Route::get('/users', [AdminUserController::class, 'index'])->name('users.index');
    Route::get('/users/{user}', [AdminUserController::class, 'show'])->name('users.show');
    Route::post('/users/{user}/ban', [AdminUserController::class, 'toggleBan'])->name('users.ban');
    Route::post('/users/{user}/freeze', [AdminUserController::class, 'toggleFreeze'])->name('users.freeze');
    Route::post('/users/{user}/credit', [AdminUserController::class, 'credit'])->name('users.credit');
    Route::post('/users/{user}/debit', [AdminUserController::class, 'debit'])->name('users.debit');
    Route::post('/users/{user}/send-email', [AdminUserController::class, 'sendEmail'])->name('users.send-email');
    Route::post('/users/{user}/send-notification', [AdminUserController::class, 'sendNotification'])->name('users.send-notification');
    Route::delete('/users/{user}', [AdminUserController::class, 'destroy'])->name('users.destroy');
    Route::get('/agency-requests', [AdminAgencyRequestController::class, 'index'])->name('agency-requests.index');
    Route::get('/agency-requests/{agencyRequest}', [AdminAgencyRequestController::class, 'show'])->name('agency-requests.show');
    Route::delete('/agency-requests/{agencyRequest}', [AdminAgencyRequestController::class, 'destroy'])->name('agency-requests.destroy');
    
    // Agency Request Fields Management
    Route::get('/agency-request-fields', [AdminAgencyRequestFieldController::class, 'index'])->name('agency-request-fields.index');
    Route::get('/agency-request-fields/create', [AdminAgencyRequestFieldController::class, 'create'])->name('agency-request-fields.create');
    Route::post('/agency-request-fields', [AdminAgencyRequestFieldController::class, 'store'])->name('agency-request-fields.store');
    Route::get('/agency-request-fields/{field}/edit', [AdminAgencyRequestFieldController::class, 'edit'])->name('agency-request-fields.edit');
    Route::put('/agency-request-fields/{field}', [AdminAgencyRequestFieldController::class, 'update'])->name('agency-request-fields.update');
    Route::delete('/agency-request-fields/{field}', [AdminAgencyRequestFieldController::class, 'destroy'])->name('agency-request-fields.destroy');
    
    Route::get('/payment-methods', [AdminPaymentMethodController::class, 'index'])->name('payment-methods.index');
    Route::get('/payment-methods/create', [AdminPaymentMethodController::class, 'create'])->name('payment-methods.create');
    Route::post('/payment-methods', [AdminPaymentMethodController::class, 'store'])->name('payment-methods.store');
    Route::get('/payment-methods/{paymentMethod}/edit', [AdminPaymentMethodController::class, 'edit'])->name('payment-methods.edit');
    Route::put('/payment-methods/{paymentMethod}', [AdminPaymentMethodController::class, 'update'])->name('payment-methods.update');

    Route::get('/deposits', [AdminDepositController::class, 'index'])->name('deposits.index');
    Route::get('/deposits/{depositRequest}', [AdminDepositController::class, 'show'])->name('deposits.show');
    Route::post('/deposits/{depositRequest}/approve', [AdminDepositController::class, 'approve'])->name('deposits.approve');
    Route::post('/deposits/{depositRequest}/reject', [AdminDepositController::class, 'reject'])->name('deposits.reject');
    Route::get('/deposits/{depositRequest}/evidence', [AdminDepositController::class, 'downloadEvidence'])->name('deposits.evidence');

    Route::resource('categories', AdminCategoryController::class)->except(['show']);
    Route::resource('services', AdminServiceController::class)->except(['show']);
    Route::resource('banners', AdminBannerController::class)->except(['show', 'destroy']);
    Route::resource('popups', AdminPopupController::class);
    
    Route::get('/notifications/create', [\App\Http\Controllers\Admin\NotificationController::class, 'create'])->name('notifications.create');
    Route::post('/notifications', [\App\Http\Controllers\Admin\NotificationController::class, 'store'])->name('notifications.store');

    Route::resource('vip-tiers', \App\Http\Controllers\Admin\VipTierController::class)->except(['show']);
    Route::get('/appearance', [AdminAppearanceController::class, 'edit'])->name('appearance.edit');
    Route::post('/appearance', [AdminAppearanceController::class, 'update'])->name('appearance.update');
    // Site Settings Routes
    Route::get('/site-settings', [AdminSiteSettingsController::class, 'edit'])->name('site-settings.edit');
    Route::post('/site-settings/general', [AdminSiteSettingsController::class, 'updateGeneral'])->name('site-settings.update-general');
    Route::post('/site-settings/logo', [AdminSiteSettingsController::class, 'updateLogo'])->name('site-settings.update-logo');
    Route::post('/site-settings/social', [AdminSiteSettingsController::class, 'updateSocial'])->name('site-settings.update-social');
    
    // Pages Management Routes
    Route::get('/pages', [AdminPageController::class, 'edit'])->name('pages.edit');
    Route::put('/pages', [AdminPageController::class, 'update'])->name('pages.update');

    Route::get('services/{service}/variants', [AdminServiceVariantController::class, 'index'])->name('services.variants.index');
    Route::get('services/{service}/variants/create', [AdminServiceVariantController::class, 'create'])->name('services.variants.create');
    Route::post('services/{service}/variants', [AdminServiceVariantController::class, 'store'])->name('services.variants.store');
    Route::get('services/{service}/variants/{variant}/edit', [AdminServiceVariantController::class, 'edit'])->name('services.variants.edit');
    Route::put('services/{service}/variants/{variant}', [AdminServiceVariantController::class, 'update'])->name('services.variants.update');
    Route::delete('services/{service}/variants/{variant}', [AdminServiceVariantController::class, 'destroy'])->name('services.variants.destroy');
    Route::get('services/{service}/fields/create', [AdminServiceFormFieldController::class, 'create'])->name('services.fields.create');
    Route::post('services/{service}/fields', [AdminServiceFormFieldController::class, 'store'])->name('services.fields.store');
    Route::get('services/{service}/fields/{field}/edit', [AdminServiceFormFieldController::class, 'edit'])->name('services.fields.edit');
    Route::put('services/{service}/fields/{field}', [AdminServiceFormFieldController::class, 'update'])->name('services.fields.update');
    Route::delete('services/{service}/fields/{field}', [AdminServiceFormFieldController::class, 'destroy'])->name('services.fields.destroy');
    Route::post('services/{service}/fields/{field}/options', [AdminServiceFormFieldController::class, 'storeOption'])->name('services.fields.options.store');
    Route::delete('services/{service}/fields/{field}/options/{option}', [AdminServiceFormFieldController::class, 'destroyOption'])->name('services.fields.options.destroy');

    Route::get('/orders', [AdminOrderController::class, 'index'])->name('orders.index');
    Route::get('/orders/{order}', [AdminOrderController::class, 'show'])->name('orders.show');
    Route::put('/orders/{order}', [AdminOrderController::class, 'update'])->name('orders.update');
});

Route::get('/debug/test-email', function () {
    $user = auth()->user();
    if (! $user) {
        return 'Please login first.';
    }

    try {
        \Illuminate\Support\Facades\Mail::raw('Test email from Arab 8bp.in debug route.', function ($message) use ($user) {
            $message->to($user->email)
                ->subject('Test Email - Arab 8bp.in');
        });
        return 'Email sent to ' . $user->email;
    } catch (\Exception $e) {
        return 'Failed to send email: ' . $e->getMessage();
    }
});

require __DIR__ . '/auth.php';
