@component('emails.layout', ['title' => $subject ?? 'رسالة من الإدارة'])
<div style="text-align: right; direction: rtl;">
    {!! nl2br(e($content)) !!}
</div>
@endcomponent
