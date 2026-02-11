@extends('emails.layouts.app')

@section('content')
  <x-emails.layouts.app :subject="'Verify Your Email Address'" :title="'Verify Your Email Address'" :introLines="[
      'Please click the button below to verify your email address.',
      'This link will expire in 60 minutes.',
  ]" :actionText="'Verify Email Address'" :actionUrl="$verificationUrl" :helperText="'If you\'re having trouble clicking the button, copy and paste the URL below into your web browser:'" :fallbackUrl="$verificationUrl" />
@endsection

{{-- Note: In a real mailable, you would pass the variables directly to the view, not use @section --}}
{{-- This is just for demonstration of the blade file itself. See the Mailable class for correct implementation. --}}