# Project Context

Project name: **S7SH**
Production domain: [s7sh.com](https://s7sh.com?utm_source=chatgpt.com)
Framework: [Laravel](https://laravel.com?utm_source=chatgpt.com) (existing production app)
Architecture:

* Laravel backend
* Blade or Laravel frontend views (detect actual implementation before changing anything)
* Admin dashboard already exists
* Queue/jobs may exist
* External provider integration exists with Daily Card

Critical business rules:

* Never break checkout, orders, wallet, deposits, or provider integrations.
* Preserve backward compatibility.
* All new features must be toggleable if relevant.
* Avoid hardcoding UI strings.
* Use existing architecture patterns, naming conventions, and permissions system.

---

# Mandatory First Phase (Do NOT Skip)

Before changing code:

## Step 1: Audit the project

Scan and document:

### Routes

Check:

* `routes/web.php`
* `routes/api.php`
* admin routes

### Controllers

Find related controllers for:

* Auth/Login
* Orders
* Services
* Deposits
* Payment settings
* Admin settings
* User management

### Models

Find:

* User
* Order
* Service
* Store settings/config model
* Deposit/payment models

### Frontend

Find:

* Global layout
* Shared JS files
* Navigation logic
* Order pages
* Payment pages

### Integrations

Locate all code related to:

* Daily Card API sync
* Webhooks
* Order polling/status updates
* Wallet balance updates

After auditing, create an implementation plan based on actual code—not assumptions.

---

# Required Implementations

---

# FEATURE 1 — Store Maintenance Mode (Admin Controlled)

Goal:
Temporarily block customers from using the store while keeping admin access working.

## Backend

Create settings if not already existing:

Example keys:

* `maintenance_enabled`
* `maintenance_message`
* `maintenance_image`
* `maintenance_button_text`
* `maintenance_button_url`

Use existing settings system if available. Do not create duplicate config systems.

## Middleware

Create a middleware:

Example:
`CheckStoreMaintenance`

Logic:

If:

* maintenance mode = enabled
  AND
* current user is NOT admin

Then:

* block access to store
* show maintenance page

Allow:

* admin routes
* authenticated admins
* API endpoints only if business-critical

Do not block:

* admin dashboard
* login for admins

## Frontend

Create maintenance page with:

* animated message
* optional image
* optional CTA button

Use graceful fallback if image or button is missing.

---

# FEATURE 2 — Global Loading Experience

Goal:
Prevent users from thinking pages are frozen during navigation/actions.

## Audit first

Find:

* global layout
* app.js
* shared frontend scripts

## Implement

Add global loading overlay for:

### Page navigation

Show loading on:

* internal navigation
* form submissions
* checkout actions
* service order submissions

Hide loading on:

* page fully loaded
* request completion
* validation errors

Must prevent:

* duplicate submissions
* multiple order clicks

## Admin control

Add setting:

* `global_loader_enabled`

If disabled:
loader never appears.

---

# FEATURE 3 — Force Homepage Entry After Login/Re-entry

Business reason:
Service prices change frequently.

Users must not resume old service pages with stale prices.

## Implement

Audit auth flow first.

After:

* login
* logout → login
* session restoration

Redirect customers to:
`/`

Do NOT affect admins.

Do NOT break:

* password reset
* email verification
* admin auth

Use role-based redirect logic.

---

# FEATURE 4 — Desktop Order Sharing

Current issue:
Share works on mobile only.

## Implement

Audit existing order sharing implementation.

use same share process as on mobile screens

Must work on:

* Windows browsers
* Chrome
* Edge
* Firefox (with safe fallback)

Must preserve mobile behavior.

---

# FEATURE 5 — Payment Transfer Number Visibility Bug

Bug:
When admin hides transfer number, it still appears on frontend.

## Root Cause Investigation

Audit:

* caching
* blade conditionals
* API serialization
* frontend state caching

## Fix

When hidden in admin:

Number must never render in:

* desktop
* mobile
* cached frontend states

Clear related caches if needed.

Must verify:

* config cache
* view cache
* settings cache

---

# FEATURE 6 — Daily Card Rejected Orders Sync Bug

Bug:
Provider rejects order but local order remains “processing”.

## Audit

Find:

* order sync jobs
* webhook handlers
* provider status mapping

Document all provider statuses.

## Implement status mapping

If provider status = rejected / failed / canceled:

Then:

### Order:

Set status = rejected

### Wallet:

Automatically refund held balance

### Ledger:

Create transaction log

### Safety:

Prevent double refund using:

* DB transaction
* idempotency checks

Use:
`DB::transaction()`

this is daily card API documentation link: https://www.dailycard.net/apiDocumentation

---

# FEATURE 7 — Daily Card Completed Orders Bug

Bug:
Provider marks complete but local order stays processing.

## Fix

When provider sends completed:

Update:

* order status = completed
* delivered_at = timestamp if field exists

Trigger existing:

* notifications
* events
* emails

Do not duplicate notifications.

this is daily card API documentation link: https://www.dailycard.net/apiDocumentation


---

# FEATURE 8 — Block User Deposits

Goal:
Admin can prevent specific users from depositing.

## Database

Add fields to users table:

Examples:

* `deposit_blocked`
* `deposit_block_message`

Use migration safely.

## Admin UI

In user admin page:

Allow:

* toggle deposit block
* custom message

## Frontend Logic

Before deposit:

Check user status.

If blocked:

Reject deposit and show custom message.

Must enforce:

* UI validation
* backend validation
* API validation

Never rely only on frontend.

---

# FEATURE 9 — Suspended Balance Admin Control Missing

Bug:
Suspended balance control disappeared.

## Audit

Check:

* hidden UI conditions
* permissions
* feature flags
* removed columns
* policy checks

## Fix

Restore suspended balance controls in user management.

Ensure:

* only admins can edit

Log all changes.

---

# Quality Requirements

Before finishing:

## Database safety

* Add proper migrations
* Add rollback support

## Logging

Add logs for:

* provider status changes
* refunds
* deposit blocks
* maintenance mode activation

Use Laravel logs only where appropriate.

## Tests

Create/update tests for:

* maintenance access
* login redirects
* refund logic
* deposit blocking
* provider status updates

Use:
[PHPUnit]

# Final Validation

Run:

```bash
php artisan optimize:clear
php artisan migrate
php artisan test
```

Then manually test:

Customer:

* login
* order
* share
* deposit
* payment page

Admin:

* maintenance mode
* user settings
* suspended balance

Provider:

* rejected orders
* completed orders
* duplicate callbacks

---

# Final Output Required From You

When finished, provide:

1. Files changed
2. Database migrations added
3. Bugs fixed
4. Edge cases handled
5. Any breaking change risks
6. Recommended next improvements

Do not make assumptions. Inspect actual codebase first, then implement based on real architecture.

---

# Implementation Plan (Based on Codebase Audit)

## Audit Summary

- **Routes:** 
  - `routes/web.php` contains web routes, admin routes (under `admin/` prefix), and `api/dailycard/webhook` route.
  - `routes/auth.php` handles standard Laravel Breeze authentication.
  - No separate `api.php` or `admin.php` files are currently used for main routing, everything is consolidated in `web.php` and `auth.php`.
- **Controllers:** 
  - Admin settings are managed by controllers like `AdminSiteSettingsController`. 
  - Orders by `OrderController`, `OpsOrderController`.
  - Auth handled by `AuthenticatedSessionController`.
  - Webhooks by `DailyCardWebhookController`.
- **Models:** 
  - Settings are stored in the `SiteSetting` model with `key`/`value` structure. 
  - `Order` manages order data and statuses, including provider tracking.
  - `User` has `balance` and `available_balance` attributes, roles (using spatie/laravel-permission).
  - `Wallet` manages user balances and held amounts.
- **Frontend:**
  - Layout is located in `resources/views/layouts/app.blade.php`. It uses Alpine.js (`x-data`) and Tailwind CSS.
  - Alpine.js is used extensively for UI interactions (dark mode, dropdowns, etc.).
- **Integrations:**
  - Daily Card API logic is mostly inside `app/Services/DailyCardOrderService.php` and `app/Http/Controllers/Api/DailyCardWebhookController.php`.
  - Local statuses and allowed transitions are managed by `app/Services/OrderStatusService.php`.

---

## Feature Implementation Strategy

### FEATURE 1: Store Maintenance Mode
- **Settings:** Add `maintenance_enabled`, `maintenance_message`, `maintenance_image` keys via `SiteSetting` model and `AdminSiteSettingsController`.
- **Middleware:** Create `app/Http/Middleware/CheckStoreMaintenance.php`.
  - Register it in `bootstrap/app.php` (Laravel 11 style) or as a route middleware.
  - Logic: Check `SiteSetting::get('maintenance_enabled')`. Allow access if `auth()->user()?->hasRole('admin')` or `request()->is('admin/*')` or `request()->routeIs('login')`.
- **View:** Create `resources/views/maintenance.blade.php` acting as the fallback UI when triggered.

### FEATURE 2: Global Loading Experience
- **Admin Control:** Add `global_loader_enabled` to `SiteSetting`.
- **Frontend:** Use a lightweight in-app loader, not an external Skeleton.js dependency. Modify `resources/views/layouts/app.blade.php` to show a fixed Alpine.js/native JS overlay on internal navigation, form submit, and active `fetch`/`axios` requests.
- **Skeleton UI:** Use Tailwind-based skeleton placeholders only for async content sections where a full overlay would feel too heavy.
- Ensure submit buttons are disabled during submissions to avoid duplicate actions.

### FEATURE 3: Force Homepage Entry After Login
- **Auth Flow:** Modify `AuthenticatedSessionController::store`.
- **Logic:** After `Auth::login($user, ...)`, check if `$user->hasRole('admin')`.
  - If admin, allow normal redirect (e.g., `redirect()->intended(route('dashboard'))`).
  - If NOT admin, force `redirect()->route('home')` ignoring intended URL.

### FEATURE 4: Desktop Order Sharing
- **Audit:** Locate mobile sharing implementation (likely Web Share API in a blade component).
- **Fix:** Provide a fallback for `navigator.share` on desktop. Add an Alpine.js modal/toast for desktop fallback that copies the URL to the clipboard since `navigator.share` is mostly supported on mobile and specific desktop browsers.

### FEATURE 5: Payment Transfer Number Visibility Bug
- **Audit:** Verify how transfer number is hidden in admin and stored in `PaymentMethod` or similar.
- **Fix:** In the respective component or view (e.g., `deposit.show`), check the visibility rule before rendering the number. Clear view/config caches when the admin setting is toggled via `AdminPaymentMethodController`.

### FEATURE 6: Daily Card Rejected Orders Sync Bug
- **Root Cause:** Provider rejects but status remains 'processing'.
- **Fix:** In `DailyCardWebhookController` and `DailyCardOrderService::mapToLocalStatus`, ensure rejected/failed mappings map to `Order::STATUS_REJECTED`. 
- `OrderStatusService::updateStatus` already safely handles refunding `amount_held` when status moves to `rejected`. Verify this transition is smooth when triggered by the Webhook.

### FEATURE 7: Daily Card Completed Orders Bug
- **Fix:** Ensure `DailyCardWebhookController` maps completed statuses to `Order::STATUS_DONE`.
- `OrderStatusService::updateStatus` handles the wallet settlement and fires `OrderStatusChangedNotification`.

### FEATURE 8: Block User Deposits
- **DB:** Add `is_deposit_blocked` (boolean) and `deposit_block_message` (string) to `users` table via a new migration.
- **Model:** Update `User.php` `$fillable` fields.
- **Admin:** Update `AdminUserController` and related views to allow toggling this field.
- **Frontend/Backend Validation:** Add checks in `DepositController::store` and UI to block deposit creation if `is_deposit_blocked` is true.

### FEATURE 9: Suspended Balance Admin Control
- **Audit:** `held_balance` is stored in the `wallets` table.
- **Fix:** Re-add the UI for modifying or viewing the suspended balance (`held_balance`) in the user administration views (e.g., `resources/views/admin/users/show.blade.php`), ensuring it hits a controller method with proper logging.