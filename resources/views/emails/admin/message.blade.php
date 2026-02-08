@component('emails.layout', ['title' => $subject ?? 'Arab 8bp.in - رسالة من الإدارة'])

<div class="intro-text" style="white-space: pre-wrap;">
    {!! nl2br(e($content)) !!}
</div>

@endcomponent
