@extends('emails.layouts.app')

@section('content')
  <x-emails.layouts.app :subject="'Reset Your Password'" :title="'Reset Your Password'" :introLines="[
      'You are receiving this email because we received a password reset request for your account.',
      'This password reset link will expire in 60 minutes.',
      'If you did not request a password reset, no further action is required.',
  ]" :actionText="'Reset Password'" :actionUrl="$resetUrl" :helperText="'If you\'re having trouble clicking the button, copy and paste the URL below into your web browser:'" :fallbackUrl="$resetUrl" />
@endsection

{{-- Note: In a real mailable, you would pass the variables directly to the view, not use @section --}}