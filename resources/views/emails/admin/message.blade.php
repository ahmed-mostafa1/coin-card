@component('emails.layout', ['title' => $subject ?? 'Coin7Card - رسالة من الإدارة'])

<div class="intro-text" style="white-space: pre-wrap;">
    {!! nl2br(e($content)) !!}
</div>

@endcomponent
