<?php

use App\Http\Controllers\AccountController;
use App\Http\Controllers\AccountDepositController;
use App\Http\Controllers\AccountNotificationController;
use App\Http\Controllers\AccountWalletController;
use App\Http\Controllers\AccountOrderController;
use App\Http\Controllers\AccountSecurityController;
use App\Http\Controllers\AccountVerificationController;
use App\Http\Controllers\AgencyRequestController;
use App\Http\Controllers\Admin\DepositController as AdminDepositController;
use App\Http\Controllers\Admin\CategoryController as AdminCategoryController;
use App\Http\Controllers\Admin\OrderController as AdminOrderController;
use App\Http\Controllers\Admin\OpsController as AdminOpsController;
use App\Http\Controllers\Admin\OpsOrderController as AdminOpsOrderController;
use App\Http\Controllers\Admin\PageController as AdminPageController;
use App\Http\Controllers\Admin\DailyCardCatalogController as AdminDailyCardCatalogController;
use App\Http\Controllers\Admin\ApiProviderController as AdminApiProviderController;
use App\Http\Controllers\Admin\ApiProviderCatalogController as AdminApiProviderCatalogController;
use App\Http\Controllers\Admin\ApiProviderOrderSyncController as AdminApiProviderOrderSyncController;
use App\Http\Controllers\Admin\AgencyRequestController as AdminAgencyRequestController;
use App\Http\Controllers\Admin\AgencyRequestFieldController as AdminAgencyRequestFieldController;
use App\Http\Controllers\Admin\ReportsController as AdminReportsController;
use App\Http\Controllers\Admin\UserController as AdminUserController;
use App\Http\Controllers\Admin\PaymentMethodController as AdminPaymentMethodController;
use App\Http\Controllers\Admin\CurrencyController as AdminCurrencyController;
use App\Http\Controllers\Admin\VerificationFieldController as AdminVerificationFieldController;
use App\Http\Controllers\Admin\VerificationRequestController as AdminVerificationRequestController;
use App\Http\Controllers\Admin\LoyaltySettingController as AdminLoyaltySettingController;
use App\Http\Controllers\Admin\ServiceController as AdminServiceController;
use App\Http\Controllers\Admin\ServiceFormFieldController as AdminServiceFormFieldController;
use App\Http\Controllers\Admin\ServiceVariantController as AdminServiceVariantController;
use App\Http\Controllers\Admin\ServiceButtonController as AdminServiceButtonController;
use App\Http\Controllers\Admin\PaymentMethodButtonController as AdminPaymentMethodButtonController;
use App\Http\Controllers\Admin\BannerController as AdminBannerController;
use App\Http\Controllers\Admin\AppearanceController as AdminAppearanceController;
use App\Http\Controllers\Admin\SiteSettingsController as AdminSiteSettingsController;
use App\Http\Controllers\Admin\PopupController as AdminPopupController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\ContactController;
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

Route::get('/terms-of-use', fn() => view('pages.terms-of-use'))->name('terms-of-use');
Route::redirect('/terms-and-conditions', '/terms-of-use', 301)->name('terms-and-conditions');
Route::get('/contact-us', [ContactController::class, 'show'])->name('contact-us.show');
Route::post('/contact-us', [ContactController::class, 'store'])->middleware('throttle:5,1')->name('contact-us.send');
Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/categories/{category:slug}', [CategoryController::class, 'show'])->name('categories.show');
Route::get('/services/{service:slug}', [ServiceController::class, 'show'])->name('services.show');
Route::get('/privacy-policy', fn() => view('pages.privacy-policy'))->name('privacy-policy');
Route::get('/about', fn() => view('pages.about'))->name('about');

Route::middleware(['auth', 'not_banned'])->group(function () {
    Route::post('/otp/verify', [\App\Http\Controllers\Auth\OtpController::class, 'verify'])->name('otp.verify');
    Route::post('/otp/resend', [\App\Http\Controllers\Auth\OtpController::class, 'resend'])->name('otp.resend');
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
    Route::get('/account/vip', fn () => redirect()->route('account.verification.show'))->name('account.vip');
    Route::get('/account/verification', [AccountVerificationController::class, 'show'])->name('account.verification.show');
    Route::post('/account/verification', [AccountVerificationController::class, 'store'])->name('account.verification.store');
    Route::get('/account/notifications', [AccountNotificationController::class, 'index'])->name('account.notifications');
    Route::post('/account/notifications/mark-all-read', [AccountNotificationController::class, 'markAllRead'])->name('account.notifications.mark-all-read');
    Route::post('/account/notifications/{id}/mark-read', [AccountNotificationController::class, 'markAsRead'])->name('account.notifications.mark-read');
    Route::get('/account/change-password', [AccountController::class, 'changePassword'])->name('account.password.change');
    Route::post('/account/change-password', [AccountController::class, 'updatePassword'])->name('account.password.update');
    Route::get('/account/security', [AccountSecurityController::class, 'show'])->name('account.security');
    Route::post('/account/security/2fa/enable', [AccountSecurityController::class, 'enable'])->name('account.security.2fa.enable');
    Route::post('/account/security/2fa/disable', [AccountSecurityController::class, 'disable'])->name('account.security.2fa.disable');


    Route::get('/deposit', [DepositController::class, 'index'])->name('deposit.index');
    Route::get('/deposit/{paymentMethod:slug}', [DepositController::class, 'show'])->name('deposit.show');
    Route::post('/deposit/{paymentMethod:slug}', [DepositController::class, 'store'])->name('deposit.store')->middleware(['not_frozen', \App\Http\Middleware\EnsureAccountVerified::class]);

    Route::post('/services/{service:slug}/purchase', [ServiceController::class, 'purchase'])->name('services.purchase')->middleware(['not_frozen', \App\Http\Middleware\EnsureAccountVerified::class]);
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
    Route::get('/users/{user}/security', [AdminUserController::class, 'security'])->name('users.security');
    Route::post('/users/{user}/password', [AdminUserController::class, 'changePassword'])->name('users.password');
    Route::post('/users/{user}/ban', [AdminUserController::class, 'toggleBan'])->name('users.ban');
    Route::post('/users/{user}/freeze', [AdminUserController::class, 'toggleFreeze'])->name('users.freeze');
    Route::post('/users/{user}/deposit-block', [AdminUserController::class, 'toggleDepositBlock'])->name('users.deposit-block');
    Route::post('/users/{user}/credit', [AdminUserController::class, 'credit'])->name('users.credit');
    Route::post('/users/{user}/debit', [AdminUserController::class, 'debit'])->name('users.debit');
    Route::post('/users/{user}/hold-balance', [AdminUserController::class, 'holdBalance'])->name('users.hold-balance');
    Route::post('/users/{user}/refund-held', [AdminUserController::class, 'refundHeld'])->name('users.refund-held');
    Route::post('/users/{user}/settle-held', [AdminUserController::class, 'settleHeld'])->name('users.settle-held');
    Route::post('/users/{user}/verification-discount', [AdminUserController::class, 'updateVerificationDiscount'])->name('users.verification-discount');
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
    Route::resource('currencies', AdminCurrencyController::class)->except(['show', 'destroy']);
    Route::resource('verification-fields', AdminVerificationFieldController::class)->except(['show']);
    Route::get('/verification-requests', [AdminVerificationRequestController::class, 'index'])->name('verification-requests.index');
    Route::get('/verification-requests/{verificationRequest}', [AdminVerificationRequestController::class, 'show'])->name('verification-requests.show');
    Route::post('/verification-requests/{verificationRequest}/approve', [AdminVerificationRequestController::class, 'approve'])->name('verification-requests.approve');
    Route::post('/verification-requests/{verificationRequest}/status', [AdminVerificationRequestController::class, 'updateStatus'])->name('verification-requests.status');
    Route::get('/verification-requests/{verificationRequest}/files/{file}', [AdminVerificationRequestController::class, 'downloadFile'])->name('verification-requests.files.show');
    Route::get('/loyalty-settings', [AdminLoyaltySettingController::class, 'edit'])->name('loyalty-settings.edit');
    Route::post('/loyalty-settings', [AdminLoyaltySettingController::class, 'update'])->name('loyalty-settings.update');

    // Payment Method Buttons
    Route::get('payment-methods/{paymentMethod}/buttons/create', [AdminPaymentMethodButtonController::class, 'create'])->name('payment-methods.buttons.create');
    Route::post('payment-methods/{paymentMethod}/buttons', [AdminPaymentMethodButtonController::class, 'store'])->name('payment-methods.buttons.store');
    Route::get('payment-methods/{paymentMethod}/buttons/{button}/edit', [AdminPaymentMethodButtonController::class, 'edit'])->name('payment-methods.buttons.edit');
    Route::put('payment-methods/{paymentMethod}/buttons/{button}', [AdminPaymentMethodButtonController::class, 'update'])->name('payment-methods.buttons.update');
    Route::delete('payment-methods/{paymentMethod}/buttons/{button}', [AdminPaymentMethodButtonController::class, 'destroy'])->name('payment-methods.buttons.destroy');

    Route::get('/deposits', [AdminDepositController::class, 'index'])->name('deposits.index');
    Route::get('/deposits/{depositRequest}', [AdminDepositController::class, 'show'])->name('deposits.show');
    Route::post('/deposits/{depositRequest}/approve', [AdminDepositController::class, 'approve'])->name('deposits.approve');
    Route::post('/deposits/{depositRequest}/reject', [AdminDepositController::class, 'reject'])->name('deposits.reject');
    Route::get('/deposits/{depositRequest}/evidence', [AdminDepositController::class, 'downloadEvidence'])->name('deposits.evidence');

    Route::resource('categories', AdminCategoryController::class)->except(['show']);
    Route::post('/services/description-images', [AdminServiceController::class, 'uploadDescriptionImage'])->name('services.description-images.store');
    Route::resource('services', AdminServiceController::class)->except(['show']);
    Route::resource('banners', AdminBannerController::class)->except(['show', 'destroy']);
    Route::resource('popups', AdminPopupController::class);
    
    Route::get('/notifications', [\App\Http\Controllers\Admin\NotificationController::class, 'index'])->name('notifications.index');
    Route::get('/notifications/create', [\App\Http\Controllers\Admin\NotificationController::class, 'create'])->name('notifications.create');
    Route::post('/notifications', [\App\Http\Controllers\Admin\NotificationController::class, 'store'])->name('notifications.store');

    Route::get('/vip-tiers', fn () => redirect()->route('admin.verification-requests.index'))->name('vip-tiers.index');
    Route::get('/vip-tiers/create', fn () => redirect()->route('admin.verification-requests.index'))->name('vip-tiers.create');
    Route::get('/vip-tiers/{vipTier}/edit', fn () => redirect()->route('admin.verification-requests.index'))->name('vip-tiers.edit');
    Route::post('/vip-tiers', fn () => redirect()->route('admin.verification-requests.index'))->name('vip-tiers.store');
    Route::put('/vip-tiers/{vipTier}', fn () => redirect()->route('admin.verification-requests.index'))->name('vip-tiers.update');
    Route::delete('/vip-tiers/{vipTier}', fn () => redirect()->route('admin.verification-requests.index'))->name('vip-tiers.destroy');
    Route::get('/appearance', [AdminAppearanceController::class, 'edit'])->name('appearance.edit');
    Route::post('/appearance', [AdminAppearanceController::class, 'update'])->name('appearance.update');
    // Site Settings Routes
    Route::get('/site-settings', [AdminSiteSettingsController::class, 'edit'])->name('site-settings.edit');
    Route::post('/site-settings/general', [AdminSiteSettingsController::class, 'updateGeneral'])->name('site-settings.update-general');
    Route::post('/site-settings/logo', [AdminSiteSettingsController::class, 'updateLogo'])->name('site-settings.update-logo');
    Route::post('/site-settings/social', [AdminSiteSettingsController::class, 'updateSocial'])->name('site-settings.update-social');
    Route::post('/site-settings/seo', [AdminSiteSettingsController::class, 'updateSeo'])->name('site-settings.update-seo');
    Route::post('/site-settings/maintenance', [AdminSiteSettingsController::class, 'updateMaintenance'])->name('site-settings.update-maintenance');
    
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

    // Service Buttons
    Route::get('services/{service}/buttons/create', [AdminServiceButtonController::class, 'create'])->name('services.buttons.create');
    Route::post('services/{service}/buttons', [AdminServiceButtonController::class, 'store'])->name('services.buttons.store');
    Route::get('services/{service}/buttons/{button}/edit', [AdminServiceButtonController::class, 'edit'])->name('services.buttons.edit');
    Route::put('services/{service}/buttons/{button}', [AdminServiceButtonController::class, 'update'])->name('services.buttons.update');
    Route::delete('services/{service}/buttons/{button}', [AdminServiceButtonController::class, 'destroy'])->name('services.buttons.destroy');

    Route::get('/orders', [AdminOrderController::class, 'index'])->name('orders.index');
    Route::get('/orders/{order}', [AdminOrderController::class, 'show'])->name('orders.show');
    Route::put('/orders/{order}', [AdminOrderController::class, 'update'])->name('orders.update');
    Route::post('/orders/sync-all-providers', [AdminApiProviderOrderSyncController::class, 'syncAllProcessingStatus'])->name('orders.sync-all-providers');
    Route::post('/orders/{order}/sync-provider', [AdminApiProviderOrderSyncController::class, 'syncStatus'])->name('orders.sync-provider');

    // API Providers (generic multi-provider system)
    Route::post('providers/sync-statuses', [AdminApiProviderController::class, 'syncStatuses'])->name('providers.sync-statuses');
    Route::resource('providers', AdminApiProviderController::class)->except(['show']);
    Route::post('providers/{provider}/test', [AdminApiProviderController::class, 'testConnection'])->name('providers.test');
    Route::post('providers/{provider}/sync-statuses', [AdminApiProviderController::class, 'syncProviderStatuses'])->name('providers.sync-provider-statuses');
    Route::get('providers/{provider}/catalog', [AdminApiProviderCatalogController::class, 'index'])->name('providers.catalog.index');
    Route::post('providers/{provider}/catalog/import', [AdminApiProviderCatalogController::class, 'import'])->name('providers.catalog.import');

    // Legacy DailyCard redirect → generic catalog
    Route::get('/dailycard', function () {
        $provider = \App\Models\ApiProvider::where('slug', 'dailycard')->first();
        if ($provider) {
            return redirect()->route('admin.providers.catalog.index', $provider);
        }
        return redirect()->route('admin.providers.index');
    })->name('dailycard.index');
    Route::post('/dailycard/import', [AdminDailyCardCatalogController::class, 'import'])->name('dailycard.import');
});

Route::get('/debug/test-email', function () {
    $user = auth()->user();
    if (! $user) {
        return 'Please login first.';
    }

    try {
        \Illuminate\Support\Facades\Mail::raw('Test email from S7SH.com|شحنك شات.in debug route.', function ($message) use ($user) {
            $message->to($user->email)
                ->subject('Test Email - S7SH.com|شحنك شات.in');
        });
        return 'Email sent to ' . $user->email;
    } catch (\Exception $e) {
        return 'Failed to send email: ' . $e->getMessage();
    }
});
Route::match(['get', 'post'], '/api/dailycard/webhook', [\App\Http\Controllers\Api\DailyCardWebhookController::class, 'handle'])->name('api.dailycard.webhook');

require __DIR__ . '/auth.php';
