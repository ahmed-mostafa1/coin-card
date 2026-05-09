@extends('layouts.app')

@section('title', 'لوحة الأدمن')

@section('content')
    @php
        $adminGroups = [
            [
                'title' => 'العمليات',
                'description' => 'متابعة الطلبات والتقارير اليومية.',
                'icon' => 'fa-solid fa-bolt',
                'links' => [
                    ['label' => 'لوحة العمليات', 'route' => route('admin.ops.index', ['tab' => 'orders_new']), 'icon' => 'fa-solid fa-gauge-high'],
                    ['label' => 'الطلبات', 'route' => route('admin.orders.index'), 'icon' => 'fa-solid fa-basket-shopping'],
                    ['label' => 'التقارير', 'route' => route('admin.reports.index'), 'icon' => 'fa-solid fa-chart-line'],
                ],
            ],
            [
                'title' => 'الشحن والمدفوعات',
                'description' => 'إدارة طلبات الشحن وطرق الدفع والعملات.',
                'icon' => 'fa-solid fa-wallet',
                'links' => [
                    ['label' => 'طلبات الشحن', 'route' => route('admin.deposits.index'), 'icon' => 'fa-solid fa-money-bill-transfer'],
                    ['label' => 'طرق الدفع', 'route' => route('admin.payment-methods.index'), 'icon' => 'fa-solid fa-credit-card'],
                    ['label' => 'العملات', 'route' => route('admin.currencies.index'), 'icon' => 'fa-solid fa-coins'],
                ],
            ],
            [
                'title' => 'الخدمات',
                'description' => 'تنظيم الكتالوج والمزودين والتصنيفات.',
                'icon' => 'fa-solid fa-layer-group',
                'links' => [
                    ['label' => 'الخدمات', 'route' => route('admin.services.index'), 'icon' => 'fa-solid fa-box-open'],
                    ['label' => 'التصنيفات', 'route' => route('admin.categories.index'), 'icon' => 'fa-solid fa-folder-tree'],
                    ['label' => 'مزودو الـ API', 'route' => route('admin.providers.index'), 'icon' => 'fa-solid fa-plug'],
                ],
            ],
            [
                'title' => 'المستخدمون',
                'description' => 'الحسابات، التوثيق، وإعدادات الولاء.',
                'icon' => 'fa-solid fa-users',
                'links' => [
                    ['label' => 'المستخدمون', 'route' => route('admin.users.index'), 'icon' => 'fa-solid fa-user-group'],
                    ['label' => 'طلبات التوثيق', 'route' => route('admin.verification-requests.index'), 'icon' => 'fa-solid fa-user-shield'],
                    ['label' => 'حقول التوثيق', 'route' => route('admin.verification-fields.index'), 'icon' => 'fa-solid fa-list-check'],
                    ['label' => 'إعدادات الولاء', 'route' => route('admin.loyalty-settings.edit'), 'icon' => 'fa-solid fa-award'],
                ],
            ],
            [
                'title' => 'المحتوى والموقع',
                'description' => 'إدارة واجهة الموقع والرسائل والمحتوى العام.',
                'icon' => 'fa-solid fa-window-restore',
                'links' => [
                    ['label' => 'إدارة الموقع', 'route' => route('admin.site-settings.edit'), 'icon' => 'fa-solid fa-sliders'],
                    ['label' => 'الشريط المتحرك', 'route' => route('admin.appearance.edit'), 'icon' => 'fa-solid fa-palette'],
                    ['label' => 'محتوى الصفحات', 'route' => route('admin.pages.edit'), 'icon' => 'fa-solid fa-file-lines'],
                    ['label' => 'النوافذ المنبثقة', 'route' => route('admin.popups.index'), 'icon' => 'fa-solid fa-message'],
                    ['label' => 'البانرات', 'route' => route('admin.banners.index'), 'icon' => 'fa-solid fa-images'],
                    ['label' => 'الإشعارات', 'route' => route('admin.notifications.index'), 'icon' => 'fa-solid fa-bell'],
                ],
            ],
            [
                'title' => 'الوكالات',
                'description' => 'متابعة طلبات الوكالة وإدارة نموذجها.',
                'icon' => 'fa-solid fa-handshake',
                'links' => [
                    ['label' => 'طلبات الوكالة', 'route' => route('admin.agency-requests.index'), 'icon' => 'fa-solid fa-inbox'],
                    ['label' => 'إدارة صفحة طلب الوكالة', 'route' => route('admin.agency-request-fields.index'), 'icon' => 'fa-solid fa-pen-to-square'],
                ],
            ],
        ];
    @endphp

    <section class="admin-dashboard-shell">
        <div class="admin-dashboard-hero">
            <div class="min-w-0">
                <span class="admin-dashboard-eyebrow">لوحة التحكم</span>
                <h1 class="mt-3 text-2xl font-black text-slate-950 dark:text-white sm:text-3xl">لوحة الأدمن</h1>
                <p class="mt-3 max-w-2xl text-sm leading-7 text-slate-900 dark:text-slate-50">
                    إدارة منظمة للطلبات والمدفوعات والخدمات والمستخدمين من مكان واحد، بتجربة مريحة للهاتف أولاً.
                </p>
            </div>

            <a href="{{ route('admin.ops.index', ['tab' => 'orders_new']) }}" class="admin-dashboard-primary-link">
                <i class="fa-solid fa-bolt text-sm"></i>
                <span>فتح لوحة العمليات</span>
            </a>
        </div>

        <div class="admin-dashboard-grid">
            @foreach ($adminGroups as $group)
                <section class="admin-dashboard-card">
                    <div class="flex items-start gap-3">
                        <span class="admin-dashboard-card-icon">
                            <i class="{{ $group['icon'] }}"></i>
                        </span>
                        <div class="min-w-0">
                            <h2 class="text-base font-black text-slate-950 dark:text-white">{{ $group['title'] }}</h2>
                            <p class="mt-1 text-xs leading-6 text-slate-900 dark:text-slate-400">{{ $group['description'] }}</p>
                        </div>
                    </div>

                    <div class="mt-5 grid gap-2">
                        @foreach ($group['links'] as $link)
                            <a href="{{ $link['route'] }}" class="admin-dashboard-link">
                                <span class="admin-dashboard-link-icon">
                                    <i class="{{ $link['icon'] }}"></i>
                                </span>
                                <span class="min-w-0 flex-1 truncate">{{ $link['label'] }}</span>
                                <i class="fa-solid fa-arrow-left text-[11px] text-slate-400 dark:text-slate-900"></i>
                            </a>
                        @endforeach
                    </div>
                </section>
            @endforeach
        </div>
    </section>
@endsection
