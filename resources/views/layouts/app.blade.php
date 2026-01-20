<!doctype html>
<html lang="ar" dir="rtl">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>@yield('title', 'كوين كارد')</title>
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="min-h-screen">
        <div class="bg-gradient-to-b from-emerald-50 via-slate-50 to-white">
            <nav class="border-b border-emerald-100 bg-white/80 backdrop-blur">
                <div class="mx-auto flex max-w-6xl flex-row-reverse items-center justify-between px-4 py-4">
                    <a href="{{ route('home') }}" class="text-lg font-semibold text-emerald-700">
                        كوين كارد
                    </a>
                    <div class="flex items-center gap-4 text-sm text-slate-700">
                        <a href="{{ route('home') }}" class="transition hover:text-emerald-700">الرئيسية</a>
                        <a href="{{ route('agency-requests.create') }}" class="transition hover:text-emerald-700">طلب وكالة</a>
                        @auth
                            <a href="{{ route('dashboard') }}" class="transition hover:text-emerald-700">لوحة التحكم</a>
                            <a href="{{ route('account') }}" class="transition hover:text-emerald-700">حسابي</a>
                            @role('admin')
                                <a href="{{ route('admin.index') }}" class="rounded-full bg-emerald-100 px-3 py-1 text-emerald-700 transition hover:bg-emerald-200">لوحة الأدمن</a>
                            @endrole
                        @endauth
                    </div>
                    <div class="flex items-center gap-3 text-sm">
                        @auth
                            <details class="relative">
                                <summary class="relative cursor-pointer rounded-full border border-slate-200 px-3 py-1 text-slate-700 transition hover:border-emerald-200">
                                    الإشعارات
                                    @if (! empty($navUnreadCount ?? 0))
                                        <span class="absolute -left-1 -top-1 rounded-full bg-rose-500 px-2 text-xs text-white">{{ $navUnreadCount }}</span>
                                    @endif
                                </summary>
                                <div class="absolute left-0 mt-2 w-80 rounded-2xl border border-emerald-100 bg-white p-4 shadow-lg">
                                    <div class="flex items-center justify-between">
                                        <p class="text-sm font-semibold text-emerald-700">آخر الإشعارات</p>
                                        <a href="{{ route('account.notifications') }}" class="text-xs text-emerald-700 hover:text-emerald-900">عرض الكل</a>
                                    </div>
                                    <div class="mt-3 space-y-3 text-sm">
                                        @forelse ($navNotifications ?? [] as $notification)
                                            <a href="{{ $notification->data['url'] ?? route('account.notifications') }}" class="block rounded-xl border border-slate-100 p-3 transition hover:border-emerald-200">
                                                <div class="flex items-start justify-between gap-2">
                                                    <p class="font-semibold text-slate-700">{{ $notification->data['title'] ?? 'إشعار جديد' }}</p>
                                                    @if ($notification->read_at === null)
                                                        <span class="mt-1 h-2 w-2 rounded-full bg-emerald-500"></span>
                                                    @endif
                                                </div>
                                                <p class="mt-2 text-xs text-slate-500">{{ $notification->data['description'] ?? '' }}</p>
                                                <p class="mt-2 text-xs text-slate-400">{{ $notification->created_at->diffForHumans() }}</p>
                                            </a>
                                        @empty
                                            <p class="text-xs text-slate-500">لا توجد إشعارات حتى الآن.</p>
                                        @endforelse
                                    </div>
                                    <form method="POST" action="{{ route('account.notifications.mark-all-read') }}" class="mt-4">
                                        @csrf
                                        <button type="submit" class="w-full rounded-full border border-emerald-200 px-3 py-2 text-xs font-semibold text-emerald-700 transition hover:bg-emerald-50">
                                            تعليم الكل كمقروء
                                        </button>
                                    </form>
                                </div>
                            </details>
                            <details class="relative">
                                <summary class="cursor-pointer rounded-full border border-emerald-200 px-3 py-1 text-emerald-700">
                                    {{ auth()->user()->name }}
                                </summary>
                                <div class="absolute right-0 mt-2 w-44 rounded-xl border border-emerald-100 bg-white p-2 shadow-lg">
                                    <form method="POST" action="{{ route('logout') }}">
                                        @csrf
                                        <button type="submit" class="w-full rounded-lg px-3 py-2 text-right text-sm text-slate-700 transition hover:bg-emerald-50">
                                            تسجيل الخروج
                                        </button>
                                    </form>
                                </div>
                            </details>
                        @else
                            <a href="{{ route('login') }}" class="rounded-full border border-emerald-200 px-4 py-1 text-emerald-700 transition hover:bg-emerald-50">تسجيل الدخول</a>
                            <a href="{{ route('register') }}" class="rounded-full bg-emerald-600 px-4 py-1 text-white transition hover:bg-emerald-700">إنشاء حساب</a>
                        @endauth
                    </div>
                </div>
            </nav>

            <main class="mx-auto max-w-6xl px-4 py-10">
                @yield('content')
            </main>

            <footer class="border-t border-emerald-100 bg-white/80 py-6">
                <div class="mx-auto flex max-w-6xl flex-wrap items-center justify-between gap-3 px-4 text-sm text-slate-600">
                    <p>جميع الحقوق محفوظة.</p>
                    <div class="flex items-center gap-4">
                        <a href="{{ route('privacy-policy') }}" class="transition hover:text-emerald-700">سياسة الخصوصية</a>
                        <a href="{{ route('about') }}" class="transition hover:text-emerald-700">من نحن</a>
                    </div>
                </div>
            </footer>
        </div>

        <a href="https://wa.me/963991195136" target="_blank" rel="noopener noreferrer" class="fixed bottom-4 left-4 z-50 inline-flex items-center gap-2 rounded-full bg-emerald-600 px-4 py-3 text-sm font-semibold text-white shadow-lg transition hover:bg-emerald-700">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 32 32" class="h-5 w-5 fill-current" aria-hidden="true">
                <path d="M16 3.2a12.8 12.8 0 0 0-11.1 19.2L3.2 28.8l6.6-1.7A12.8 12.8 0 1 0 16 3.2Zm7.5 17.7c-.3.8-1.5 1.5-2.3 1.6-.6.1-1.4.2-4.7-1-4.1-1.6-6.7-5.8-6.9-6.1-.2-.3-1.7-2.2-1.7-4.2s1-3 1.3-3.4c.3-.3.6-.4.9-.4h.7c.2 0 .5 0 .8.6.3.6 1 2.4 1.1 2.6.1.2.1.5 0 .7-.1.2-.2.4-.4.6-.2.2-.4.4-.6.6-.2.2-.4.4-.2.7.2.3.9 1.5 2 2.4 1.4 1.2 2.5 1.6 2.9 1.8.4.2.6.2.8 0 .2-.2 1-1.1 1.2-1.5.2-.4.5-.3.8-.2.3.1 2.1 1 2.4 1.2.3.2.5.3.6.5.1.2.1.9-.2 1.7Z"/>
            </svg>
            تواصل واتساب
        </a>
    </body>
</html>
