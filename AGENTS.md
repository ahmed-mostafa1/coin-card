# Coin Card - معلومات للمساعدين

## نظرة عامة
- Laravel 12 (Blade + TailwindCSS v4)
- قاعدة البيانات: MySQL (معدة في `.env`)
- المصادقة: Breeze (مطبقة يدويًا عبر Blade)
- الصلاحيات: spatie/laravel-permission
- تسجيل دخول Google: Laravel Socialite
- اللغة: العربية فقط مع RTL افتراضيًا

## نقاط أساسية في المشروع
- الإعداد الأساسي في `bootstrap/app.php` يتضمن إعادة توجيه الضيوف للصفحة `login` وربط ميدل وير الصلاحيات.
- جميع الواجهات في `resources/views` وتستخدم التخطيط الأساسي: `resources/views/layouts/app.blade.php`.
- الخط المستخدم: Cairo عبر Google Fonts في `resources/css/app.css`.

## المسارات الأساسية
- `/` الصفحة الرئيسية
- `/dashboard` لوحة التحكم (تتطلب تسجيل دخول)
- `/account` ملخص الحساب (تتطلب تسجيل دخول)
- `/admin` لوحة الأدمن (تتطلب دور admin)
- مسارات المصادقة في `routes/auth.php`

## الملفات المهمة
- `app/Models/User.php` يحتوي على `HasRoles` وحقول Google.
- `config/permission.php` إعدادات Spatie.
- `config/services.php` إعدادات Google OAuth.
- `database/migrations/2024_01_01_000003_create_permission_tables.php` جداول الصلاحيات.
- `database/migrations/2024_01_01_000004_add_google_fields_to_users_table.php` حقول Google.
- `database/seeders/RolesAndPermissionsSeeder.php` و `database/seeders/AdminUserSeeder.php`.

## متغيرات البيئة
- `GOOGLE_CLIENT_ID`, `GOOGLE_CLIENT_SECRET`, `GOOGLE_REDIRECT_URI`
- `ADMIN_NAME`, `ADMIN_EMAIL`, `ADMIN_PASSWORD`
- `APP_LOCALE=ar` (افتراضي داخل `config/app.php`)

## الاختبارات
- PHPUnit عبر `php artisan test`
- اختبارات الوصول في `tests/Feature/AuthAccessTest.php`

## Phase 2
### ما تم تنفيذه
- محفظة لكل مستخدم مع سجل عمليات (Ledger) عبر `wallets` و`wallet_transactions`.
- طلبات شحن يدوية مع إثباتات مرفوعة وجدول مخصص للأدلة.
- إدارة طرق الدفع للأدمن مع رفع الأيقونات.
- صفحات مستخدم لطلبات الشحن وسجل الرصيد وصفحات أدمن للمراجعة والاعتماد/الرفض.

### أوامر التشغيل
- الهجرات: `php artisan migrate`
- البذور: `php artisan db:seed`
- إنشاء رابط التخزين للأيقونات العامة: `php artisan storage:link`
- الاختبارات: `php artisan test`

### متغيرات البيئة
- `MAX_PENDING_DEPOSITS`
- `DEPOSIT_MIN_AMOUNT`
- `DEPOSIT_MAX_AMOUNT`

### قرارات معمارية
- تحديث الرصيد يتم عبر `WalletService` داخل معاملات DB مع `lockForUpdate` لضمان الاتساق.
- إثباتات التحويل تُخزن في قرص `local` (خاص) وتُعرض عبر مسارات أدمن مؤمنة.
- أيقونات طرق الدفع تُخزن على قرص `public` ضمن `payment-methods/icons`.

## Phase 3
### ما تم تنفيذه
- إدارة التصنيفات والخدمات عبر صفحات الأدمن مع رفع الصور.
- نماذج خدمات ديناميكية (حقول نصية وقوائم) مع خيارات قابلة للإدارة.
- واجهة متجر للمستخدم مع تصفح الفئات والخدمات وتفاصيل الخدمة.
- شراء من المحفظة مع خصم الرصيد وتسجيل الطلبات في دفتر الأستاذ.
- صفحات المستخدم للطلبات وتفاصيل الطلب.

### المسارات والجداول
- المسارات: `/categories/{category:slug}`, `/services/{service:slug}`, `/services/{service:slug}/purchase`، صفحات المستخدم `/account/orders` و`/account/orders/{order}`، وإدارة الأدمن `/admin/categories`, `/admin/services`, `/admin/orders`، وحقول الخدمة `/admin/services/{service}/fields`.
- الجداول: `categories`, `services`, `service_form_fields`, `service_form_options`, `orders`.
- حالات الطلب: `new`, `processing`, `done`, `rejected`, `cancelled`.

### أوامر التشغيل
- الهجرات: `php artisan migrate`
- البذور: `php artisan db:seed`
- رابط التخزين: `php artisan storage:link`
- الاختبارات: `php artisan test`

### قرارات معمارية
- تخزين إجابات النموذج في عمود JSON `payload` حسب `name_key`.
- خصم المحفظة يتم ضمن معاملة واحدة مع `lockForUpdate` لضمان الاتساق.
- صور التصنيفات والخدمات تُخزن في قرص `public` ضمن مسارات منفصلة.

## Phase 4
### ما تم تنفيذه
- نظام تسوية مرحلية للمحفظة: الرصيد المتاح مقابل الرصيد المعلّق مع تثبيت مبلغ الطلب عند الشراء.
- إدارة باقات الخدمة (Variants) بأسعار متعددة وربطها بالطلبات.
- تحديث نماذج الخدمة لاختيار الباقة مع التحقق الديناميكي وحفظ البيانات.
- تحديث واجهات المستخدم/الأدمن لعرض الرصيد المتاح والمعلّق وحركات التعليق/التسوية والإرجاع.

### الهجرات والجداول/الحقول
- `database/migrations/2024_01_01_000015_add_held_balance_to_wallets_table.php` إضافة `held_balance` إلى `wallets`.
- `database/migrations/2024_01_01_000016_create_service_variants_table.php` إنشاء جدول `service_variants`.
- `database/migrations/2024_01_01_000017_add_settlement_fields_to_orders_table.php` إضافة `amount_held`, `variant_id`, `settled_at`, `released_at` إلى `orders`.

### حالات الطلب والانتقالات
- الحالات: `new`, `processing`, `done`, `rejected`.
- الانتقالات المسموحة: `new -> processing`, `processing -> done`, `processing -> rejected`, و`new -> rejected` (نهائية).
- بعد `done` أو `rejected` لا يمكن تغيير الحالة.

### أنواع حركات المحفظة والتسوية
- الأنواع: `deposit`, `hold`, `settle`, `release` (مع بقاء `purchase` للسجل القديم فقط).
- عند الشراء: `hold` يحول المبلغ من `balance` إلى `held_balance` ويُخزّن في `orders.amount_held`.
- عند `done`: `settle` يخصم من `held_balance` فقط ويثبّت `settled_at`.
- عند `rejected`: `release` يعيد المبلغ من `held_balance` إلى `balance` ويثبّت `released_at`.
- جميع عمليات الرصيد تتم داخل معاملات DB مع `lockForUpdate` لمنع التكرار.

### أوامر التشغيل
- الهجرات: `php artisan migrate`
- البذور: `php artisan db:seed`
- الاختبارات: `php artisan test`

## Phase 5
### ما تم تنفيذه
- إشعارات داخل التطبيق عبر قاعدة البيانات للمستخدمين والأدمن مع قائمة منبثقة وعدّاد غير المقروء.
- سجل زمني للطلبات يعرض كل تغيّر حالة ومنفّذه للطرفين (أدمن/مستخدم).
- لوحة عمليات للأدمن لعرض طوابير الشحن والطلبات مع فلاتر وإجراءات سريعة.
- تحسينات بسيطة بالواجهات (شارات، عدّادات، روابط مباشرة).

### الجداول الجديدة
- `notifications`
- `order_events`

### أنواع الإشعارات
- `DepositStatusChangedNotification`
- `NewDepositRequestNotification`
- `OrderStatusChangedNotification`
- `NewOrderNotification`

### أنواع أحداث الطلب
- `created`
- `status_changed`

### المسارات/الصفحات الجديدة
- `/account/notifications`
- `/account/notifications/mark-all-read`
- `/account/deposits/{depositRequest}`
- `/admin/ops`

### أوامر التشغيل
- الهجرات: `php artisan migrate`
- البذور: `php artisan db:seed`
- الاختبارات: `php artisan test`

### قرارات معمارية
- تم إنشاء جدول `order_events` مخصص بدل نظام نشاط عام لضمان بساطة السجل وتركيزه على دورة حياة الطلب.
- تم استخدام إشعارات قاعدة البيانات فقط بدون قنوات خارجية حسب نطاق المرحلة.
