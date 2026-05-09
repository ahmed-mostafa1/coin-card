<!--
╔══════════════════════════════════════════════════════════════════════════════╗
║                     RESPONSIVE TABLE - QUICK REFERENCE                       ║
╚══════════════════════════════════════════════════════════════════════════════╝

📱 MOBILE (<640px): Cards with label + value
🖥️  DESKTOP (≥640px): Normal table
🌍 RTL: Fully supported

┌──────────────────────────────────────────────────────────────────────────────┐
│ STEP 1: Wrap table in component                                             │
└──────────────────────────────────────────────────────────────────────────────┘
-->
<x-table class="mt-6">
    <!-- or -->
<x-responsive-table>

<!--
┌──────────────────────────────────────────────────────────────────────────────┐
│ STEP 2: Add data-label to EVERY <td>                                        │
└──────────────────────────────────────────────────────────────────────────────┘

❌ WRONG:
-->
<td class="py-3">#12345</td>

<!--
✅ CORRECT:
-->
<td class="py-3" data-label="Order ID">#12345</td>
<td class="py-3" data-label="رقم الطلب">#12345</td>  <!-- Arabic -->

<!--
┌──────────────────────────────────────────────────────────────────────────────┐
│ COMMON PATTERNS                                                              │
└──────────────────────────────────────────────────────────────────────────────┘

📝 Simple cell:
-->
<td class="py-3 text-slate-700 dark:text-slate-50" data-label="Name">
    {{ $user->name }}
</td>

<!--
👤 Cell with sub-text:
-->
<td class="py-3 text-slate-700 dark:text-slate-50" data-label="User">
    {{ $user->name }}
    <div class="text-xs text-slate-9000 dark:text-slate-50">{{ $user->email }}</div>
</td>

<!--
🏷️  Badge/Status:
-->
<td class="py-3" data-label="Status">
    <x-badge type="approved">Approved</x-badge>
</td>

<!--
🔗 Single action:
-->
<td class="py-3" data-label="Actions">
    <a href="#" class="text-emerald-900 dark:text-emerald-400">View</a>
</td>

<!--
⚡ Multiple actions (IMPORTANT - wrap in flex div):
-->
<td class="py-3" data-label="Actions">
    <div class="flex flex-wrap gap-2 text-xs">
        <a href="#" class="text-emerald-900 dark:text-emerald-400">View</a>
        <a href="#" class="text-blue-700 dark:text-blue-400">Edit</a>
        <form method="POST" action="#" class="inline">
            @csrf
            <button type="submit" class="text-rose-700 dark:text-rose-400">Delete</button>
        </form>
    </div>
</td>

<!--
┌──────────────────────────────────────────────────────────────────────────────┐
│ STANDARD CLASSES (Copy-Paste Ready)                                         │
└──────────────────────────────────────────────────────────────────────────────┘
-->

<!-- Table wrapper -->
<x-table class="mt-6">

<!-- Table header -->
<thead class="bg-slate-50 dark:bg-slate-700/50 text-xs text-slate-9000 dark:text-slate-50">
    <tr>
        <th class="py-2">Column Name</th>
    </tr>
</thead>

<!-- Table body -->
<tbody class="divide-y divide-slate-50 dark:divide-slate-700">
    <tr class="transition hover:bg-slate-50 dark:hover:bg-slate-700/50">
        <!-- Regular text cell -->
        <td class="py-3 text-slate-700 dark:text-slate-50" data-label="Label">Content</td>
        
        <!-- Muted text cell -->
        <td class="py-3 text-slate-900 dark:text-slate-400" data-label="Label">Content</td>
        
        <!-- Component cell (badge, etc) -->
        <td class="py-3" data-label="Label">
            <x-badge type="pending">Pending</x-badge>
        </td>
    </tr>
    
    <!-- Empty state -->
    <tr>
        <td colspan="7" class="py-6 text-center text-slate-900 dark:text-slate-400">
            No data available
        </td>
    </tr>
</tbody>

</x-table>

<!--
┌──────────────────────────────────────────────────────────────────────────────┐
│ MIGRATION CHECKLIST                                                          │
└──────────────────────────────────────────────────────────────────────────────┘

□ Table wrapped in <x-table> or <x-responsive-table>
□ data-label added to EVERY <td> (copy from <th> text)
□ Multiple actions wrapped in <div class="flex flex-wrap gap-2 text-xs">
□ Tested on mobile (<640px)
□ Tested on desktop (≥640px)
□ Tested RTL if applicable

┌──────────────────────────────────────────────────────────────────────────────┐
│ TROUBLESHOOTING                                                              │
└──────────────────────────────────────────────────────────────────────────────┘

❓ Tables still squeezing on mobile?
   → Check data-label on EVERY <td>
   → Run: npm run dev (rebuild CSS)
   → Clear browser cache

❓ Labels not showing on mobile?
   → Verify data-label="..." exists
   → Check label text is not empty

❓ Action buttons misaligned?
   → Wrap in <div class="flex flex-wrap gap-2 text-xs">

❓ RTL not working?
   → Check <html dir="rtl"> or container dir="rtl"
   → Use Arabic labels in data-label

┌──────────────────────────────────────────────────────────────────────────────┐
│ EXAMPLES IN CODEBASE                                                         │
└──────────────────────────────────────────────────────────────────────────────┘

📁 resources/views/admin/ops/index.blade.php
   → Deposits table (lines 64-106)
   → Orders table (lines 123-189)

📁 resources/views/components/responsive-table-example.blade.php
   → Comprehensive examples with English & Arabic

📁 RESPONSIVE_TABLES.md
   → Full documentation

╔══════════════════════════════════════════════════════════════════════════════╗
║  💡 TIP: The data-label value becomes the mobile label - make it clear!     ║
╚══════════════════════════════════════════════════════════════════════════════╝
-->
