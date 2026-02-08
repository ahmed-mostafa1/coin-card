@component('emails.layout', ['title' => 'Arab 8bp.in - إشعار طلب جديد'])

<p class="intro-text">
    تم إنشاء طلب شحن رصيد جديد على الموقع، وهذه تفاصيله:
</p>

<table class="details-table">
    <tr>
        <td class="label">رقم طلب الشحن:</td>
        <td class="value highlight-value">#{{ $order->id }}</td>
    </tr>
    <tr>
        <td class="label">اسم المستخدم:</td>
        <td class="value rtl">{{ $order->user->name }}</td>
    </tr>
    <tr>
        <td class="label">إيميل المستخدم:</td>
        <td class="value"><a href="mailto:{{ $order->user->email }}">{{ $order->user->email }}</a></td>
    </tr>
    <tr>
        <td class="label">الخدمة:</td>
        <td class="value rtl">{{ $order->service->name }}</td>
    </tr>
    <tr>
        <td class="label">قيمة الشحن:</td>
        <td class="value highlight-value">$ {{ number_format($order->amount_held, 2) }}</td>
    </tr>
    @if($order->discount_amount > 0)
    <tr>
        <td class="label">الخصم المطبق:</td>
        <td class="value highlight-value">$ {{ number_format($order->discount_amount, 2) }}</td>
    </tr>
    @endif
    <tr>
        <td class="label">طريقة الدفع:</td>
        <td class="value rtl">{{ $order->payment_method ?? 'رصيد الحساب' }}</td>
    </tr>
    <tr>
        <td class="label">حالة الطلب الحالية:</td>
        <td class="value">
            <span class="status-badge status-pending">قيد المراجعة</span>
        </td>
    </tr>
</table>

<div class="action-text">
    لمراجعة طلب الشحن وتغيير حالته، يمكنك الدخول مباشرة إلى <a href="{{ route('admin.orders.show', $order) }}">لوحة التحكم</a>.
</div>

@endcomponent
