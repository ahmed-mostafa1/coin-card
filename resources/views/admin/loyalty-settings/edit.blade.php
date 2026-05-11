@extends('layouts.app')

@section('title', 'إعدادات الولاء')

@section('content')
    <x-card :hover="false" class="p-8">
        <x-page-header title="إعدادات الولاء" subtitle="تحكم بالنقاط والخصومات التدريجية للمستوى الثالث." />
        @if (session('status'))<div class="mt-6 rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-900 dark:text-emerald-50 dark:bg-emerald-900 dark:border-emerald-700">{{ session('status') }}</div>@endif
        <form method="POST" action="{{ route('admin.loyalty-settings.update') }}" class="mt-6 space-y-4">
            @csrf
            <label class="flex items-center gap-2 text-sm text-slate-900"><input type="checkbox" name="is_enabled" value="1" class="rounded border-slate-50 text-emerald-600" @checked($settings->is_enabled)> تفعيل نظام الولاء</label>
            <div class="grid gap-4 md:grid-cols-2">
                <div><x-input-label for="points_per_usd" value="نقاط لكل 1 USD" /><x-text-input id="points_per_usd" name="points_per_usd" type="number" step="0.0001" min="0" :value="old('points_per_usd', $settings->points_per_usd)" required /><p class="mt-1 text-xs text-slate-900 dark:text-slate-50">عدد النقاط المكتسبة عند كل دولار من الطلبات المكتملة.</p></div>
                <div><x-input-label for="level_3_points" value="نقاط الوصول للمستوى 3" /><x-text-input id="level_3_points" name="level_3_points" type="number" min="1" :value="old('level_3_points', $settings->level_3_points)" required /><p class="mt-1 text-xs text-slate-900 dark:text-slate-50">عند بلوغ هذا الرقم ينتقل المستخدم إلى المستوى 3.</p></div>
                <div><x-input-label for="level_2_discount_percentage" value="خصم المستوى 2 (%)" /><x-text-input id="level_2_discount_percentage" name="level_2_discount_percentage" type="number" step="0.01" min="0" max="100" :value="old('level_2_discount_percentage', $settings->level_2_discount_percentage)" required /><p class="mt-1 text-xs text-slate-900 dark:text-slate-50">الخصم الأساسي للمستخدم في المستوى 2.</p></div>
                <div><x-input-label for="level_3_discount_percentage" value="خصم المستوى 3 الأساسي (%)" /><x-text-input id="level_3_discount_percentage" name="level_3_discount_percentage" type="number" step="0.01" min="0" max="100" :value="old('level_3_discount_percentage', $settings->level_3_discount_percentage)" required /><p class="mt-1 text-xs text-slate-900 dark:text-slate-50">الخصم الأساسي للمستخدم في المستوى 3 قبل أي زيادات.</p></div>
                <div><x-input-label for="points_step" value="كل كم نقطة تزيد الخصم" /><x-text-input id="points_step" name="points_step" type="number" min="1" :value="old('points_step', $settings->points_step)" required /><p class="mt-1 text-xs text-slate-900 dark:text-slate-50">كلما جمع المستخدم هذا العدد من النقاط، تضاف له زيادة خصم.</p></div>
                <div><x-input-label for="discount_step_percentage" value="زيادة الخصم لكل خطوة (%)" /><x-text-input id="discount_step_percentage" name="discount_step_percentage" type="number" step="0.01" min="0" max="100" :value="old('discount_step_percentage', $settings->discount_step_percentage)" required /><p class="mt-1 text-xs text-slate-900 dark:text-slate-50">نسبة الزيادة لكل خطوة نقاط بعد المستوى الأساسي.</p></div>
                <div><x-input-label for="max_discount_percentage" value="أقصى خصم (%)" /><x-text-input id="max_discount_percentage" name="max_discount_percentage" type="number" step="0.01" min="0" max="100" :value="old('max_discount_percentage', $settings->max_discount_percentage)" required /><p class="mt-1 text-xs text-slate-900 dark:text-slate-50">حد أقصى للخصم لا يمكن تجاوزه مهما زادت النقاط.</p></div>
            </div>
            <x-primary-button>حفظ الإعدادات</x-primary-button>
        </form>
    </x-card>
@endsection
