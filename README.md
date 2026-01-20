# كوين كارد - المرحلة الأولى

مشروع Laravel 12 بواجهة عربية كاملة RTL مع تسجيل دخول، صلاحيات أساسية، وتكامل Google OAuth.

## المتطلبات

- PHP 8.2+
- Composer
- Node.js + npm
- قاعدة بيانات MySQL مهيأة في `.env`

## خطوات الإعداد

1) تثبيت الحزم

```bash
composer install
npm install
```

2) إعداد البيئة

```bash
cp .env.example .env
php artisan key:generate
```

3) تشغيل الهجرات والبذور

```bash
php artisan migrate
php artisan db:seed
```

4) تشغيل الأصول

```bash
npm run dev
```

## إعداد Google OAuth

أضف القيم التالية في `.env`:

```
GOOGLE_CLIENT_ID=
GOOGLE_CLIENT_SECRET=
GOOGLE_REDIRECT_URI=https://your-domain.test/auth/google/callback
```

## إعداد مستخدم الأدمن

أضف القيم التالية في `.env` (وسيتم إنشاؤه عبر `db:seed` إذا لم يكن موجودًا):

```
ADMIN_NAME=المشرف الرئيسي
ADMIN_EMAIL=admin@example.com
ADMIN_PASSWORD=YourStrongPassword
```

## ملاحظات تنفيذية

- تم تخزين بيانات Google داخل جدول `users` عبر الحقول `google_id` و `avatar`.
- الأدوار الافتراضية: `admin` و `customer`.
- أي تسجيل جديد يحصل تلقائيًا على دور `customer`.
- صفحة الأدمن محمية بواسطة صلاحية `admin`.
- ملفات إثبات التحويل تُخزّن في المسار `storage/app/private` ويتم الوصول إليها عبر مسارات الأدمن المؤمنة.
- أيقونات طرق الدفع تُخزن في `storage/app/public` وتحتاج `php artisan storage:link` لعرضها.
- صور التصنيفات والخدمات تُخزن في `storage/app/public` ضمن `categories` و`services`.

## إعدادات الشحن اليدوي

يمكن ضبط الحدود عبر `.env`:

```
MAX_PENDING_DEPOSITS=3
DEPOSIT_MIN_AMOUNT=1
DEPOSIT_MAX_AMOUNT=100000
```

## اختبارات

```bash
php artisan test
```
