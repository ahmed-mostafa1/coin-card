# Responsive Table Implementation Guide

## Overview
This project uses a global responsive table pattern that automatically transforms tables into mobile-friendly cards on screens < 640px while maintaining normal table layout on desktop/tablet (>= 640px).

## Features
- ✅ **Desktop/tablet (≥640px)**: Normal table display
- ✅ **Mobile (<640px)**: Card layout with label + value pairs
- ✅ **Full RTL support**: Works with both Arabic and English
- ✅ **Preserves components**: Action buttons, badges, links, and icons display correctly
- ✅ **Safe text handling**: Long text wraps safely, numbers remain readable
- ✅ **Zero JavaScript**: Pure CSS solution

## Implementation

### 1. Global CSS (Already Added)
The responsive table styles are in `resources/css/app.css` under the `.rt-table` class in the `@layer components` section.

### 2. Blade Components

#### Option A: Use `<x-table>` (Recommended for existing code)
The existing `<x-table>` component has been updated to use the responsive pattern:

```blade
<x-table class="mt-6">
    <thead class="bg-slate-50 dark:bg-slate-700/50 text-xs text-slate-500 dark:text-slate-400">
        <!-- headers -->
    </thead>
    <tbody class="divide-y divide-slate-100 dark:divide-slate-700">
        <!-- rows -->
    </tbody>
</x-table>
```

#### Option B: Use `<x-responsive-table>` (New component)
For new code or explicit responsive tables:

```blade
<x-responsive-table>
    <!-- same structure as above -->
</x-responsive-table>
```

### 3. Required Markup Changes

#### CRITICAL: Add `data-label` to Every `<td>`

For each table cell, add a `data-label` attribute that matches the column header text:

**English Example:**
```blade
<thead>
    <tr>
        <th class="py-2">Order ID</th>
        <th class="py-2">Customer</th>
        <th class="py-2">Amount</th>
    </tr>
</thead>
<tbody>
    <tr>
        <td class="py-3" data-label="Order ID">#12345</td>
        <td class="py-3" data-label="Customer">John Doe</td>
        <td class="py-3" data-label="Amount">$150.00</td>
    </tr>
</tbody>
```

**Arabic Example:**
```blade
<thead>
    <tr>
        <th class="py-2">رقم الطلب</th>
        <th class="py-2">المستخدم</th>
        <th class="py-2">المبلغ</th>
    </tr>
</thead>
<tbody>
    <tr>
        <td class="py-3" data-label="رقم الطلب">#12345</td>
        <td class="py-3" data-label="المستخدم">أحمد مصطفى</td>
        <td class="py-3" data-label="المبلغ">150.00 USD</td>
    </tr>
</tbody>
```

## Migration Checklist

When updating an existing table:

- [ ] Ensure table is wrapped in `<x-table>` or `<x-responsive-table>`
- [ ] Add `data-label="..."` to **every** `<td>` element
- [ ] Copy the exact header text from `<th>` to the corresponding `data-label`
- [ ] For action columns with multiple buttons, wrap in `<div class="flex flex-wrap gap-2 text-xs">`
- [ ] Test on mobile (resize browser to < 640px width)
- [ ] Test on desktop (>= 640px width)
- [ ] If RTL, test with `dir="rtl"` on the HTML element

## Common Patterns

### Pattern 1: Simple Data Cell
```blade
<td class="py-3 text-slate-700 dark:text-slate-300" data-label="Service Name">
    {{ $order->service->name }}
</td>
```

### Pattern 2: Cell with Sub-text
```blade
<td class="py-3 text-slate-700 dark:text-slate-300" data-label="User">
    {{ $user->name }}
    <div class="text-xs text-slate-500 dark:text-slate-400">{{ $user->email }}</div>
</td>
```

### Pattern 3: Badge/Status Cell
```blade
<td class="py-3" data-label="Status">
    <x-badge type="approved">Approved</x-badge>
</td>
```

### Pattern 4: Action Buttons (Single)
```blade
<td class="py-3" data-label="Actions">
    <a href="{{ route('admin.orders.show', $order) }}" 
       class="text-emerald-700 dark:text-emerald-400 hover:text-emerald-900">
        View
    </a>
</td>
```

### Pattern 5: Action Buttons (Multiple)
```blade
<td class="py-3" data-label="Actions">
    <div class="flex flex-wrap gap-2 text-xs">
        <a href="#" class="text-emerald-700 dark:text-emerald-400">View</a>
        <a href="#" class="text-blue-700 dark:text-blue-400">Edit</a>
        <form method="POST" action="#" class="inline">
            @csrf
            <button type="submit" class="text-rose-700 dark:text-rose-400">Delete</button>
        </form>
    </div>
</td>
```

### Pattern 6: Empty State
```blade
<tr>
    <td colspan="7" class="py-6 text-center text-slate-500 dark:text-slate-400">
        No data available
    </td>
</tr>
```

## Standard Table Classes

### Table Header (`<thead>`)
```blade
<thead class="bg-slate-50 dark:bg-slate-700/50 text-xs text-slate-500 dark:text-slate-400">
```

### Table Body (`<tbody>`)
```blade
<tbody class="divide-y divide-slate-100 dark:divide-slate-700">
```

### Table Row (`<tr>`)
```blade
<tr class="transition hover:bg-slate-50 dark:hover:bg-slate-700/50">
```

### Table Cells (`<td>`)
- **Regular text**: `class="py-3 text-slate-700 dark:text-slate-300"`
- **Muted text**: `class="py-3 text-slate-500 dark:text-slate-400"`
- **With badge/component**: `class="py-3"`

## How It Works

### Desktop (≥640px)
- Table displays normally with all standard table elements
- Headers are visible
- Rows and cells use standard table layout
- `data-label` attributes are ignored (hidden via CSS)

### Mobile (<640px)
- Table header is hidden
- Each `<tr>` becomes a card with rounded borders, padding, and shadow
- Each `<td>` becomes a 2-column grid:
  - **Left column**: Label from `data-label` attribute (gray text)
  - **Right column**: Cell content (normal text)
- Action buttons and badges display properly
- Cards stack vertically with spacing

### RTL Support
- Text direction automatically adjusts based on `dir="rtl"` or `dir="ltr"`
- Labels and values align correctly in both directions
- Works seamlessly with Arabic and English content

## Examples in Codebase

See these files for working examples:
- `resources/views/admin/ops/index.blade.php` - Deposits and Orders tables
- `resources/views/components/responsive-table-example.blade.php` - Comprehensive examples

## Troubleshooting

### Tables still squeezing on mobile
- ✅ Ensure you've added `data-label` to **every** `<td>`
- ✅ Check that Tailwind CSS is compiling (run `npm run dev`)
- ✅ Clear browser cache

### Labels not showing on mobile
- ✅ Verify `data-label="..."` attribute exists on each `<td>`
- ✅ Check that the label text is not empty
- ✅ Inspect element to ensure CSS is loaded

### Action buttons not aligning properly
- ✅ Wrap multiple actions in `<div class="flex flex-wrap gap-2 text-xs">`
- ✅ Ensure buttons/links have proper spacing classes

### RTL not working
- ✅ Verify `<html dir="rtl">` or container has `dir="rtl"`
- ✅ Check that Arabic labels are in the `data-label` attributes

## Notes

- The CSS linter warnings about `@apply` are expected and safe to ignore - they're part of Tailwind CSS
- Desktop table behavior is completely unchanged
- No JavaScript required
- Works with all existing Blade components (badges, buttons, forms, etc.)
- Fully compatible with dark mode
