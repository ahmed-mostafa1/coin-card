@props(['user'])

@if ($user)
    <img src="{{ asset($user->is_verified ? 'img/s7sh-verified-badge.png' : 'img/s7sh-unverified-badge.png') }}"
         alt="{{ $user->is_verified ? 'Verified' : 'Unverified' }}"
         title="{{ $user->is_verified ? 'مستخدم موثق' : 'مستخدم غير موثق' }}"
         {{ $attributes->merge(['class' => 'inline-block h-5 w-5 align-middle']) }}>
@endif
