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
