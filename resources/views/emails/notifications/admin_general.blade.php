@component('emails.layout', [
    'title' => $titleAr . ' / ' . $titleEn,
    'preheader' => $titleEn
])
<div class="section rtl">
    <h3 class="lang-title">العربية</h3>
    <p><strong>{{ $titleAr }}</strong></p>
    <p style="white-space: pre-wrap;">{!! nl2br(e($contentAr)) !!}</p>
</div>

<hr class="separator">

<div class="section ltr">
    <h3 class="lang-title">English</h3>
    <p><strong>{{ $titleEn }}</strong></p>
    <p style="white-space: pre-wrap;">{!! nl2br(e($contentEn)) !!}</p>
</div>
@endcomponent
