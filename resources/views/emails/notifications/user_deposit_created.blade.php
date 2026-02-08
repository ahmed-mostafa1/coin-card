@component('emails.layout', ['title' => 'Arab 8bp.in - تم استلام طلب الشحن'])

<p class="intro-text">
    أهلاً {{ $deposit->user->name }}،
    <br>
    تم استلام طلب شحن الرصيد الخاص بك وهو الآن قيد المراجعة.
</p>

<table class="details-table">
    <tr>
        <td class="label">رقم الطلب:</td>
        <td class="value highlight-value">#{{ $deposit->id }}</td>
    </tr>
    <tr>
        <td class="label">المبلغ:</td>
        <td class="value highlight-value">$ {{ number_format($deposit->user_amount, 2) }}</td>
    </tr>
    <tr>
        <td class="label">طريقة الدفع:</td>
        <td class="value rtl">{{ $deposit->paymentMethod->name ?? 'غير محدد' }}</td>
    </tr>
    <tr>
        <td class="label">تاريخ الطلب:</td>
        <td class="value rtl">{{ $deposit->created_at->format('Y-m-d H:i') }}</td>
    </tr>
    <tr>
        <td class="label">حالة الطلب:</td>
        <td class="value">
            <span class="status-badge status-pending">قيد المراجعة</span>
        </td>
    </tr>
</table>

<div class="action-text">
    سيتم إشعارك فور اكتمال مراجعة الطلب. يمكنك متابعة حالة طلبك من خلال <a href="{{ route('account.deposits.show', $deposit) }}">حسابك</a>.
</div>

@endcomponent
