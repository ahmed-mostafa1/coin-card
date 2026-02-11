@component('emails.layout', [
    'title' => 'رمز التفعيل / Activation Code',
    'preheader' => 'OTP code'
])
<div class="section rtl">
    <h3 class="lang-title">العربية</h3>
    <p>شكرًا لتسجيلك. استخدم رمز التفعيل التالي لتأكيد حسابك:</p>
    <p class="btn-wrap"><span class="btn" style="letter-spacing: 6px; cursor: default;">{{ $code }}</span></p>
    <p class="muted">صلاحية الرمز 10 دقائق فقط.</p>
</div>

<hr class="separator">

<div class="section ltr">
    <h3 class="lang-title">English</h3>
    <p>Thank you for registering. Use the activation code below to verify your account:</p>
    <p class="btn-wrap"><span class="btn" style="letter-spacing: 6px; cursor: default;">{{ $code }}</span></p>
    <p class="muted">This code is valid for 10 minutes only.</p>
</div>
@endcomponent
