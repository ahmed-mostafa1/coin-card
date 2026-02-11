@extends('emails.layouts.app')
<x-emails.app :subject="__('Reset Your Password')" :title="__('Reset Your Password')" :introLines="[
    __('You are receiving this email because we received a password reset request for your account.'),
    __('This password reset link will expire in 60 minutes.'),
    __('If you did not request a password reset, no further action is required.'),
]" :actionText="__('Reset Password')" :actionUrl="$resetUrl" :helperText="__('If you\'re having trouble clicking the button, copy and paste the URL below into your web browser:')" :fallbackUrl="$resetUrl" />

@section('content')
  <x-emails.layouts.app :subject="'Reset Your Password'" :title="'Reset Your Password'" :introLines="[
      'You are receiving this email because we received a password reset request for your account.',
      'This password reset link will expire in 60 minutes.',
      'If you did not request a password reset, no further action is required.',
  ]" :actionText="'Reset Password'" :actionUrl="$resetUrl" :helperText="'If you\'re having trouble clicking the button, copy and paste the URL below into your web browser:'" :fallbackUrl="$resetUrl" />
@endsection

{{-- Note: In a real mailable, you would pass the variables directly to the view, not use @section --}}
{{-- The Mailable should call this view directly. --}}