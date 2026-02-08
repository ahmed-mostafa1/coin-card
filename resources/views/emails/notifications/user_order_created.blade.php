@component('emails.layout', ['title' => 'Arab 8bp.in - تأكيد الطلب'])

<p class="intro-text">
    مرحباً {{ $order->user->name }}،
</p>
<p>
    شكراً لاستخدامك خدماتنا. لقد تم استلام طلبك بنجاح وهو الآن قيد المراجعة.
</p>

<table class="details-table">
    <tr>
        <td class="label">رقم الطلب:</td>
        <td class="value highlight-value">#{{ $order->id }}</td>
    </tr>
    <tr>
        <td class="label">الخدمة:</td>
        <td class="value rtl">{{ $order->service->name }}</td>
    </tr>
    <tr>
        <td class="label">المبلغ:</td>
        <td class="value highlight-value">$ {{ number_format($order->amount_held, 2) }}</td>
    </tr>
    @if($order->discount_amount > 0)
    <tr>
        <td class="label">الخصم:</td>
        <td class="value">$ {{ number_format($order->discount_amount, 2) }}</td>
    </tr>
    @endif
    <tr>
        <td class="label">تاريخ الطلب:</td>
        <td class="value">{{ $order->created_at->format('Y-m-d H:i') }}</td>
    </tr>
    <tr>
        <td class="label">حالة الطلب:</td>
        <td class="value">
            <span class="status-badge status-pending">قيد المراجعة</span>
        </td>
    </tr>
</table>

<div class="action-text">
    يمكنك متابعة حالة طلبك في أي وقت من خلال <a href="{{ route('account.orders.show', $order) }}">حسابك</a>.
    سيتم إشعارك فور اكتمال التنفيذ.
</div>

@endcomponent
