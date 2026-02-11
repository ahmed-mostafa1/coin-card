@component('emails.layout', [
    'title' => $subject ?? '????? ?? ??????? / Admin Message',
    'preheader' => $subject ?? 'Admin message'
])
<div class="section rtl">
    <h3 class="lang-title">???????</h3>
    <p style="white-space: pre-wrap;">{!! nl2br(e($content)) !!}</p>
</div>

<hr class="separator">

<div class="section ltr">
    <h3 class="lang-title">English</h3>
    <p style="white-space: pre-wrap;">{!! nl2br(e($content)) !!}</p>
</div>
@endcomponent
