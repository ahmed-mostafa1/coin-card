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

## Phase 6
### ما تم تنفيذه
- لوحة تقارير للإدارة مع فلاتر زمنية وملخصات رقمية لنشاط الشحن والطلبات.
- صفحة "User 360" للأدمن لعرض نظرة شاملة على المستخدم (الملف، المحفظة، الحركات، الشحنات، الطلبات).

### المسارات الجديدة
- `/admin/reports`
- `/admin/users/{user}`

### الفلاتر والتعريفات
- فلاتر التقارير: `from` و`to` مع إعدادات سريعة (`today`, `7`, `30`).
- KPIs الشحن: عدد الطلبات المنشأة + التفصيل حسب الحالة + مجموع `approved_amount` للطلبات المعتمدة.
- KPIs الطلبات: عدد الطلبات المنشأة + التفصيل حسب الحالة + "إيراد محصل" من `orders.amount_held` للطلبات المنفذة.
- نطاق "الإيراد المحصل" يعتمد على `settled_at`، مع fallback إلى `updated_at` للطلبات القديمة بدون `settled_at`.
- لقطة المحفظة: مجموع `held_balance` ومجموع `balance` الحاليين عبر جميع المحافظ.

### قرارات الأداء/الفهارس
- تمت إضافة فهارس للتقارير والفلاتر على: `deposit_requests.status`, `deposit_requests.created_at`, `deposit_requests.user_id`.
- تمت إضافة فهارس على: `orders.status`, `orders.created_at`, `orders.user_id`, `orders.service_id`, `orders.settled_at`, `orders.released_at`.
- تمت إضافة فهارس على: `wallet_transactions.wallet_id`, `wallet_transactions.created_at`, `wallet_transactions.type`.
- تمت إضافة فهرس على: `wallets.held_balance`.

### أوامر التشغيل
- الهجرات: `php artisan migrate`
- الاختبارات: `php artisan test`

## Phase 8
### ما تم تنفيذه
- تحسين صفحة العمليات للأدمن مع تبويبات مخصصة للمدفوعات والطلبات وإجراءات سريعة آمنة.
- تحسين صفحة الحساب لإظهار الرصيد المتاح والمعلّق بوضوح مع جداول مختصرة لآخر الطلبات والشحنات.
- توضيح الشراء في صفحة الخدمة مع عرض الرصيد الحالي ورسالة تعليق المبلغ وتحديث السعر.
- تحسين عرض تفاصيل الطلب للمستخدم بإظهار تواريخ التسوية/الإرجاع.

### المسارات/الصفحات المضافة أو المعدلة
- `/admin/ops` (تبويبات: deposits, orders_new, orders_processing)
- إجراءات الطلبات السريعة: `/admin/ops/orders/{order}/start-processing`, `/admin/ops/orders/{order}/mark-done`, `/admin/ops/orders/{order}/reject`
- `/account` (ملخص + آخر الطلبات/الشحنات)
- `/services/{service:slug}` (وضوح الرصيد والسعر والرسالة)
- `/account/orders` و`/account/orders/{order}` (عرض بيانات أوضح)

### قرارات تصميم أساسية
- استخدام خدمة `OrderStatusService` لتوحيد منطق تغيير حالات الطلب مع الحفاظ على تسوية الرصيد مرة واحدة.
- الاعتماد على تأكيدات واجهة بسيطة (`confirm`) للإجراءات الحساسة ضمن لوحة العمليات.
- تحديث السعر في صفحة الخدمة بجافاسكريبت خفيف بدون مكتبات إضافية.

### أوامر التشغيل
- الهجرات: `php artisan migrate`
- الاختبارات: `php artisan test`

### متغيرات/إعدادات
- لا يوجد.

## UI Fixes - Dropdown + Contrast
### ما تم إصلاحه
- معالجة تجاوز القوائم المنسدلة لحواف الشاشة على الجوال والسطح مع تموضع ثابت على الموبايل وتموضع مطلق على الشاشات الكبيرة.
- إضافة إغلاق عند النقر خارج القائمة عبر طبقة تغطية وخطاف جافاسكربت بسيط.
- تثبيت الخلفية المحايدة وإبراز البطاقات بحدود وظلال واضحة.

### الملفات المعدلة
- `resources/views/layouts/app.blade.php`

### أوامر التشغيل
- لم يتم تشغيل أوامر.

## Auth Layout Fix
### السبب
- صفحات الحساب والمصادقة كانت تستخدم بطاقة صغيرة في المنتصف مع فراغ كبير بسبب اعتماد نفس تخطيط الصفحات العامة وبطاقات ثابتة العرض.

### الحل
- إنشاء تخطيط مخصص للمصادقة بدون تمركز مبالغ فيه، مع حاوية ثابتة ومسافات مناسبة.
- تحديث صفحات تسجيل الدخول/التسجيل/الاستعادة/إعادة التعيين لاستخدام التخطيط الجديد.

### الملفات المعدلة
- `resources/views/layouts/auth.blade.php`
- `resources/views/auth/login.blade.php`
- `resources/views/auth/register.blade.php`
- `resources/views/auth/forgot-password.blade.php`
- `resources/views/auth/reset-password.blade.php`
- `AGENTS.md`

### أوامر التشغيل
- `npm run build`
- `php artisan test`

## Phase 9
### ما تم تنفيذه
- إعادة تصميم كاملة لواجهة المستخدم بطابع احترافي موحّد مع تحسينات دقيقة للحركة والتفاعل.
- توحيد المكونات البصرية وإعادة تنسيق الصفحات العامة والحساب ولوحات الأدمن.

### التخطيطات/المكونات التي تم تعديلها أو إضافتها
- تم تحديث التخطيط الأساسي: `resources/views/layouts/app.blade.php`.
- مكونات جديدة: `resources/views/components/card.blade.php`, `resources/views/components/button.blade.php`, `resources/views/components/select.blade.php`, `resources/views/components/badge.blade.php`, `resources/views/components/table.blade.php`, `resources/views/components/empty-state.blade.php`, `resources/views/components/page-header.blade.php`.
- تحديث مكونات الإدخال والأزرار: `resources/views/components/text-input.blade.php`, `resources/views/components/primary-button.blade.php`, `resources/views/components/input-error.blade.php`.

### الصفحات المحدثة
- الصفحة الرئيسية، صفحة الفئة، صفحة الخدمة، صفحات الحساب وسجل الرصيد والطلبات.
- لوحات الأدمن: العمليات، طلبات الشحن، الطلبات.
- الصفحات الثابتة: سياسة الخصوصية، من نحن.

### قرارات تصميم أساسية
- اعتماد نظام ألوان موحّد (Emerald) وخلفية محايدة مع زوايا وظلال موحّدة.
- اعتماد مكونات مشتركة لتقليل تكرار الأنماط وضمان اتساق الواجهات.

### الاعتمادات الجديدة
- لا يوجد.

### أوامر التشغيل
- بناء الواجهة: `npm run build`
- الاختبارات: `php artisan test`

## Phase 7
### ما تم تنفيذه
- زر واتساب عائم يظهر في جميع الصفحات ويفتح المحادثة مباشرة.
- صفحتان ثابتتان: سياسة الخصوصية ومن نحن مع روابط في التذييل.
- صفحة طلب وكالة عامة مع حفظ الطلبات وإدارة عرض/حذف للأدمن فقط.

### المسارات الجديدة
- `/privacy-policy`
- `/about`
- `/agency-request`
- `/admin/agency-requests`
- `/admin/agency-requests/{agencyRequest}`

### الجداول الجديدة
- `agency_requests`

### متغيرات/إعدادات
- لا يوجد.

### أوامر التشغيل
- الهجرات: `php artisan migrate`
- الاختبارات: `php artisan test`
