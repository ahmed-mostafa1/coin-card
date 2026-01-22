@extends('layouts.app')

@section('title', 'المستخدمون')
@section('mainWidth', 'max-w-none w-full')

@section('content')
    <div class="space-y-6">
        <div class="rounded-3xl border border-emerald-100 bg-white p-8 shadow-sm">
            <div class="flex flex-wrap items-center justify-between gap-4">
                <div>
                    <h1 class="text-2xl font-semibold text-emerald-700">إدارة المستخدمين</h1>
                    <p class="mt-2 text-sm text-slate-600">البحث بالاسم أو البريد وإدارة الحالات.</p>
                </div>
                <form method="GET" action="{{ route('admin.users.index') }}" class="flex items-center gap-2">
                    <x-text-input name="q" type="text" placeholder="بحث بالاسم أو البريد" :value="$search" />
                    <x-primary-button>بحث</x-primary-button>
                </form>
            </div>
        </div>

        <div class="rounded-3xl border border-emerald-100 bg-white p-6 shadow-sm">
            <div class="overflow-x-auto">
                <table class="w-full text-right text-sm">
                    <thead class="border-b border-slate-200 text-xs text-slate-500">
                        <tr>
                            <th class="py-2">المستخدم</th>
                            <th class="py-2">الحالة</th>
                            <th class="py-2">تاريخ الإنشاء</th>
                            <th class="py-2">عرض</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse ($users as $user)
                            <tr>
                                <td class="py-3 text-slate-700">
                                    {{ $user->name }}
                                    <div class="text-xs text-slate-500">{{ $user->email }}</div>
                                </td>
                                <td class="py-3">
                                    @if ($user->is_banned)
                                        <span class="rounded-full bg-rose-100 px-3 py-1 text-xs text-rose-700">محظور</span>
                                    @endif
                                    @if ($user->is_frozen)
                                        <span class="rounded-full bg-amber-100 px-3 py-1 text-xs text-amber-700">مجمّد</span>
                                    @endif
                                    @if (! $user->is_banned && ! $user->is_frozen)
                                        <span class="rounded-full bg-emerald-100 px-3 py-1 text-xs text-emerald-700">نشط</span>
                                    @endif
                                </td>
                                <td class="py-3 text-slate-500">{{ $user->created_at->format('Y-m-d') }}</td>
                                <td class="py-3">
                                    <a href="{{ route('admin.users.show', $user) }}" class="text-emerald-700 hover:text-emerald-900">عرض</a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="py-4 text-center text-slate-500">لا يوجد مستخدمون.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-6">{{ $users->links() }}</div>
        </div>
    </div>
@endsection
