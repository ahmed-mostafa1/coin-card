@component('emails.layout', [
    'title' => $subjectAr . ' / ' . $subjectEn,
    'preheader' => $subjectEn
])
<div class="section rtl">
    <h3 class="lang-title">العربية</h3>
    <p>مرحبًا {{ $notifiable->name ?? 'عميلنا' }}،</p>
    <p>{{ $descriptionAr }}</p>
    <table class="details-table">
        <tr><td class="label">النوع</td><td class="value">{{ $isDebit ? 'خصم' : 'إضافة' }}</td></tr>
        <tr><td class="label">المبلغ</td><td class="value highlight-value">{{ $amountText }}</td></tr>
        <tr><td class="label">الرصيد الحالي</td><td class="value">{{ $balanceText }}</td></tr>
        @if(!empty($noteText))
        <tr><td class="label">ملاحظة</td><td class="value">{{ $noteText }}</td></tr>
        @endif
    </table>
    <p class="btn-wrap"><a class="btn" href="{{ $walletUrl }}">عرض المحفظة</a></p>
</div>

<hr class="separator">

<div class="section ltr">
    <h3 class="lang-title">English</h3>
    <p>Hello {{ $notifiable->name ?? 'Customer' }},</p>
    <p>{{ $descriptionEn }}</p>
    <table class="details-table">
        <tr><td class="label">Type</td><td class="value">{{ $isDebit ? 'Debit' : 'Credit' }}</td></tr>
        <tr><td class="label">Amount</td><td class="value highlight-value">{{ $amountText }}</td></tr>
        <tr><td class="label">Current Balance</td><td class="value">{{ $balanceText }}</td></tr>
        @if(!empty($noteText))
        <tr><td class="label">Note</td><td class="value">{{ $noteText }}</td></tr>
        @endif
    </table>
    <p class="btn-wrap"><a class="btn" href="{{ $walletUrl }}">View Wallet</a></p>
</div>
@endcomponent
