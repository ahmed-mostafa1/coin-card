{{--
    RESPONSIVE TABLE COMPONENT - USAGE GUIDE
    ========================================
    
    This component automatically transforms tables into mobile-friendly cards on screens < 640px
    while maintaining normal table layout on desktop/tablet (>= 640px).
    
    FEATURES:
    - ✅ Desktop/tablet: Normal table display
    - ✅ Mobile: Card layout with label + value
    - ✅ Full RTL support
    - ✅ Preserves action buttons, badges, links, icons
    - ✅ Safe text wrapping
    
    BASIC USAGE:
    ------------
--}}

<x-responsive-table>
    <thead class="bg-slate-50 dark:bg-slate-700/50 text-xs text-slate-500 dark:text-slate-400">
        <tr>
            <th class="py-2">Order ID</th>
            <th class="py-2">Customer</th>
            <th class="py-2">Amount</th>
            <th class="py-2">Status</th>
            <th class="py-2">Actions</th>
        </tr>
    </thead>
    <tbody class="divide-y divide-slate-100 dark:divide-slate-700">
        <tr class="transition hover:bg-slate-50 dark:hover:bg-slate-700/50">
            <td class="py-3 text-slate-500 dark:text-slate-400" data-label="Order ID">#12345</td>
            <td class="py-3 text-slate-700 dark:text-slate-300" data-label="Customer">
                Ahmed Mostafa
                <div class="text-xs text-slate-500 dark:text-slate-400">ahmed@example.com</div>
            </td>
            <td class="py-3 text-slate-700 dark:text-slate-300" data-label="Amount">$150.00</td>
            <td class="py-3" data-label="Status">
                <x-badge type="approved">Approved</x-badge>
            </td>
            <td class="py-3" data-label="Actions">
                <div class="flex flex-wrap gap-2 text-xs">
                    <a href="#" class="text-emerald-700 dark:text-emerald-400 hover:text-emerald-900">View</a>
                    <a href="#" class="text-blue-700 dark:text-blue-400 hover:text-blue-900">Edit</a>
                </div>
            </td>
        </tr>
    </tbody>
</x-responsive-table>

{{--
    ARABIC/RTL EXAMPLE:
    -------------------
--}}

<x-responsive-table>
    <thead class="bg-slate-50 dark:bg-slate-700/50 text-xs text-slate-500 dark:text-slate-400">
        <tr>
            <th class="py-2">رقم الطلب</th>
            <th class="py-2">المستخدم</th>
            <th class="py-2">النوع</th>
            <th class="py-2">المبلغ</th>
            <th class="py-2">الحالة</th>
            <th class="py-2">التاريخ</th>
            <th class="py-2">عرض</th>
        </tr>
    </thead>
    <tbody class="divide-y divide-slate-100 dark:divide-slate-700">
        <tr class="transition hover:bg-slate-50 dark:hover:bg-slate-700/50">
            <td class="py-3 text-slate-500 dark:text-slate-400" data-label="رقم الطلب">#123</td>
            <td class="py-3 text-slate-700 dark:text-slate-300" data-label="المستخدم">
                أحمد مصطفى
                <div class="text-xs text-slate-500 dark:text-slate-400">ahmed@example.com</div>
            </td>
            <td class="py-3 text-slate-700 dark:text-slate-300" data-label="النوع">USD</td>
            <td class="py-3 text-slate-700 dark:text-slate-300" data-label="المبلغ">150.00 USD</td>
            <td class="py-3" data-label="الحالة">
                <x-badge type="pending">قيد المراجعة</x-badge>
            </td>
            <td class="py-3 text-slate-500 dark:text-slate-400" data-label="التاريخ">2026-01-30 19:45</td>
            <td class="py-3" data-label="عرض">
                <a href="#" class="text-emerald-700 dark:text-emerald-400 hover:text-emerald-900">عرض</a>
            </td>
        </tr>
    </tbody>
</x-responsive-table>

{{--
    IMPORTANT RULES:
    ----------------
    
    1. ALWAYS add data-label="..." to EVERY <td>
       - The label should match the column header text
       - Include Arabic labels for RTL tables
       - Example: <td data-label="Customer Name">John Doe</td>
    
    2. Keep existing desktop table classes:
       - thead: bg-slate-50 dark:bg-slate-700/50 text-xs
       - tbody: divide-y divide-slate-100 dark:divide-slate-700
       - tr: transition hover:bg-slate-50 dark:hover:bg-slate-700/50
       - td: py-3 (and color classes)
    
    3. Action buttons/links in cells:
       - Wrap multiple actions in: <div class="flex flex-wrap gap-2 text-xs">
       - This ensures proper mobile display
    
    4. Badges and status indicators:
       - Use existing <x-badge> component
       - They will display properly in mobile cards
    
    5. Long text handling:
       - Text automatically wraps safely
       - Numbers remain readable
       - No need for truncation classes
    
    6. Empty states:
       - Use colspan for full-width empty messages
       - Example: <td colspan="7" class="py-6 text-center">No data</td>
    
    MIGRATION CHECKLIST:
    --------------------
    
    [ ] Replace <x-table> or <table> wrapper with <x-responsive-table>
    [ ] Add data-label="..." to every <td> (copy from <th> text)
    [ ] Verify action buttons are wrapped in <div class="flex flex-wrap gap-2">
    [ ] Test on mobile (< 640px) to verify card layout
    [ ] Test on desktop (>= 640px) to verify normal table
    [ ] Test RTL if applicable
    [ ] Verify badges, links, icons display correctly
--}}
