@props([
    'subject' => '',
    'title' => null,
    'introLines' => [],
    'outroLines' => [],
    'actionText' => null,
    'actionUrl' => null,
    'helperText' => null,
    'fallbackUrl' => null,
    'signatureName' => 'Arab 8BP',
    'arTitle' => null,
    'enTitle' => null,
    'arIntroLines' => null,
    'enIntroLines' => null,
    'arOutroLines' => null,
    'enOutroLines' => null,
    'arActionText' => null,
    'enActionText' => null,
    'arHelperText' => null,
    'enHelperText' => null,
])

@php
    $arTitle = $arTitle ?? $title;
    $enTitle = $enTitle ?? $title;

    $arIntroLines = is_array($arIntroLines) ? $arIntroLines : $introLines;
    $enIntroLines = is_array($enIntroLines) ? $enIntroLines : $introLines;

    $arOutroLines = is_array($arOutroLines) ? $arOutroLines : $outroLines;
    $enOutroLines = is_array($enOutroLines) ? $enOutroLines : $outroLines;

    $arActionText = $arActionText ?? $actionText;
    $enActionText = $enActionText ?? $actionText;

    $arHelperText = $arHelperText ?? $helperText;
    $enHelperText = $enHelperText ?? $helperText;
@endphp

@component('emails.layout', [
    'title' => $subject,
    'preheader' => $subject,
])
<div class="section rtl">
    <h3 class="lang-title">???????</h3>

    @if($arTitle)
        <p><strong>{{ $arTitle }}</strong></p>
    @endif

    @foreach($arIntroLines as $line)
        <p>{!! $line !!}</p>
    @endforeach

    @if($actionUrl && $arActionText)
        <p class="btn-wrap"><a class="btn" href="{{ $actionUrl }}">{{ $arActionText }}</a></p>
    @endif

    @foreach($arOutroLines as $line)
        <p>{!! $line !!}</p>
    @endforeach

    <p class="signature">?? ???????<br>{{ $signatureName }}</p>

    @if($arHelperText)
        <p class="muted">{{ $arHelperText }}
            @if($fallbackUrl)
                <a href="{{ $fallbackUrl }}">{{ $fallbackUrl }}</a>
            @endif
        </p>
    @endif
</div>

<hr class="separator">

<div class="section ltr">
    <h3 class="lang-title">English</h3>

    @if($enTitle)
        <p><strong>{{ $enTitle }}</strong></p>
    @endif

    @foreach($enIntroLines as $line)
        <p>{!! $line !!}</p>
    @endforeach

    @if($actionUrl && $enActionText)
        <p class="btn-wrap"><a class="btn" href="{{ $actionUrl }}">{{ $enActionText }}</a></p>
    @endif

    @foreach($enOutroLines as $line)
        <p>{!! $line !!}</p>
    @endforeach

    <p class="signature">Regards,<br>{{ $signatureName }}</p>

    @if($enHelperText)
        <p class="muted">{{ $enHelperText }}
            @if($fallbackUrl)
                <a href="{{ $fallbackUrl }}">{{ $fallbackUrl }}</a>
            @endif
        </p>
    @endif
</div>
@endcomponent
