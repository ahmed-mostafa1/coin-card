You are a **senior Laravel backend engineer + payment systems engineer** working on a live production platform.

Your task is to investigate and fix **critical wallet/accounting/order-sync bugs** in production. These bugs directly affect user balances, admin financial operations, and digital product fulfillment.

Project name: **S7SH**
Production domain: [s7sh.com](https://s7sh.com?utm_source=chatgpt.com)
Framework: [Laravel](https://laravel.com?utm_source=chatgpt.com)
External provider: Daily Card

This is a **money-related system**, so you must work with accounting-grade safety.

---

# Critical Rules Before Touching Code

Before implementing anything:

## Step 1 — Audit Actual Codebase

Do NOT guess.

Inspect and document all related code:

### Database

Find tables related to:

* users
* wallets
* balances
* held balances
* orders
* transactions
* gift cards
* provider responses

Inspect:

* migrations
* current schema
* nullable fields
* numeric field types (`decimal`, `float`, etc.)

Pay special attention to:

* precision/scale issues
* signed/unsigned inconsistencies
* default values

---

### Models

Find actual models such as:

* User
* Order
* Wallet
* WalletTransaction
* ProviderOrder
* GiftCard / GiftCardOrder

Document:

* casts
* accessors
* mutators
* computed attributes

Especially check if user profile uses an accessor while admin dashboard uses direct DB values.

---

### Business Logic

Find all logic for:

#### Wallet operations

Search for:

* `held_balance`
* `pending_balance`
* `available_balance`
* `wallet_balance`
* `balance`
* `refund`
* `reserve`
* `release`

#### Provider sync

Search for:

* Daily Card API
* webhook handlers
* polling jobs
* status mapping
* gift card fulfillment logic

---

### Search Entire Project For

Search for all occurrences of:

```php
held_balance
available_balance
balance
processing
completed
gift_card
daily
refund
wallet
transaction
```

Build dependency map before changing code.

---

# BUG 1 — Held Money Always Stored as Negative

## Problem

Held money is being stored/displayed like:

* `-23.6`
* `-5.1`

instead of positive values.

This causes admin financial tools and balance actions to behave incorrectly.

---

## Investigation Required

Audit:

### Database values

Check whether values are actually stored negative in DB or only displayed negative.

Run investigation on:

* wallet tables
* transaction tables
* balance snapshots

---

### Business logic

Find where held money is created:

Examples:

* order placement
* pending orders
* provider reservations
* refunds
* cancellations

Look for code like:

```php
$held -= $amount
```

or

```php
$held = -$amount
```

or sign inversions in:

* accessors
* transformers
* API resources

---

## Required Fix

Held money must always be treated as:

### Positive reserved money

Meaning:

Correct:

* `23.60`

Wrong:

* `-23.60`

---

## Implementation Rules

### Storage

Normalize storage logic.

Held balance must be saved as:

```php
abs($amount)
```

only where business logic confirms reserve funds.

---

### Existing Bad Data

Create safe repair migration or command.

Fix existing negative records:

Convert:

```sql
-23.60 → 23.60
```

But ONLY for actual held balance fields.

Do NOT mass-update without field verification.

---

### Safety

Wrap repair in transaction:

```php
DB::transaction()
```

Create backup log before repair.

---

# BUG 2 — User Profile Available Balance Is Wrong

## Problem

Users see wrong available balance on their profile page.

But admin dashboard shows correct value.

This is a severe accounting inconsistency.

---

## Investigation Required

Find both balance calculation paths:

### User-facing balance

Trace:

* controller
* API resource
* accessor
* frontend state

### Admin balance

Trace:

* admin controller
* queries
* dashboard calculations

Compare line-by-line.

---

## Likely Root Causes to Audit

Check for:

### Different formulas

Example:

User:

```php
wallet - held
```

Admin:

```php
wallet - pending - held
```

---

### Cached values

Check:

* Redis
* Laravel cache
* session cache
* frontend local storage

---

### Different DB fields

Example:

User reading:

```php
users.balance
```

Admin reading:

```php
wallets.available_balance
```

---

### Accessor bugs

Example:

```php
getAvailableBalanceAttribute()
```

May be calculating incorrectly.

---

## Required Fix

There must be ONE source of truth.

Create centralized balance service:

Example:

`App\Services\WalletBalanceService`

Method example:

```php
calculateAvailableBalance(User $user)
```

Use same method everywhere:

### User profile

### Admin dashboard

### API responses

### Order validations

Remove duplicated formulas.

---

## Validation Required

Test with users having:

### No orders

### Pending orders

### Held money

### Refunds

### Completed orders

### Failed orders

All balance views must match exactly.

---

# BUG 3 — Daily Card Gift Card Completion Sync Broken

## Problem

When a user orders a gift card:

### At Daily Card:

Order becomes completed.

### In our store:

Order stays:

`processing`

Also:

User does NOT receive gift card details.

This breaks product delivery.

---

## Investigation Required

Audit:

### Provider integration

Find:
this is dailycard api documentation :"https://www.dailycard.net/apiDocumentation
* webhook controllers
* polling jobs
* status mappers
* provider response handlers

---

### Status mapping

Document all Daily Card statuses.

Examples:

* pending
* processing
* completed
* rejected
* failed

Check current mapping.

---

### Gift card delivery flow

Find where gift card data is saved:

Examples:

* code
* PIN
* serial
* redemption URL

Search:

```php
gift_card
code
pin
voucher
serial
delivery
```

---

# Required Fix

When Daily Card returns:

### Completed

System must:

---

## 1. Update order

Change:

```php
processing → completed
```

Set:

```php
completed_at
```

if field exists.

---

## 2. Save gift card data

Extract and store:

If provider sends:

* code
* pin
* serial
* instructions

Save all securely.

Use encrypted storage if project already encrypts sensitive data.

---

## 3. Deliver to user

User must be able to see gift card details in:

### Orders page

or

### Gift card details page

Use existing UI if available.

If missing, wire existing fields into frontend.

---

## 4. Prevent duplicate processing

Webhook/job may fire multiple times.

Implement idempotency:

Do not process completed order twice.

Examples:

If already completed:

Skip.

---

## 5. Trigger existing events

If project has:

* notifications
* emails
* in-app alerts

Trigger once only.

---

# Financial Safety Requirements

Use:

```php
DB::transaction()
```

for:

* balance updates
* gift card saves
* status updates

Never partially update.

---

# Logging

Add structured logs for:

### Held balance repair

### Balance calculation mismatch

### Daily Card callbacks

### Gift card fulfillment

Include:

* user_id
* order_id
* provider_status
* old_value
* new_value

Do not log sensitive gift card codes.

---

# Tests Required

Create/update [PHPUnit](https://phpunit.de?utm_source=chatgpt.com) tests for:

---

## Held balance

Test:

* reserve funds
* refund
* release

Held balance must never become negative.

---

## Balance consistency

Test:

Same user balance across:

* user profile
* admin dashboard
* API

Must always match.

---

## Daily Card fulfillment

Test:

Provider sends completed.

Assert:

* order completed
* gift card saved
* user can access card
* duplicate callbacks ignored

---

# Final Verification

Run:

```bash
php artisan optimize:clear
php artisan migrate
php artisan test
```

Then manually verify with real sandbox/provider data.

---

# Required Final Output

When finished, provide:

## Root Causes Found

Explain exact root cause for each bug.

## Files Changed

List every changed file.

## Database Changes

List migrations/repair scripts.

## Data Repairs

Explain if old bad records were fixed.

## Edge Cases Handled

List duplicate callbacks, partial updates, stale cache, etc.

Do NOT guess. Audit first, then implement using the actual architecture.

---
