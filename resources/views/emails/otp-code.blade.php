@component('emails.layout', [
    'title' => 'رمز تسجيل الدخول / Login Code',
    'preheader' => 'رمز الدخول الخاص بك / Your login code'
])
<div class="section rtl">
    <h3 class="lang-title">العربية</h3>
    <p>استخدم الرمز التالي لإكمال تسجيل الدخول. صلاحية الرمز 10 دقائق فقط.</p>

    <p class="btn-wrap">
        <span class="btn" style="letter-spacing: 5px; cursor: default;">{{ $otp }}</span>
    </p>

    <p class="muted">إذا لم تطلب هذا الرمز، يمكنك تجاهل الرسالة بأمان.</p>
</div>

<hr class="separator">

<div class="section ltr">
    <h3 class="lang-title">English</h3>
    <p>Use the code below to complete your login. This code is valid for 10 minutes.</p>

    <p class="btn-wrap">
        <span class="btn" style="letter-spacing: 5px; cursor: default;">{{ $otp }}</span>
    </p>

    <p class="muted">If you did not request this code, you can safely ignore this email.</p>
</div>
@endcomponent
