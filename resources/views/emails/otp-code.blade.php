@extends('emails.layouts.app')

@section('content')
  <x-emails.layouts.app :subject="'Your Login Code'" :title="'Your Login Code'" :introLines="[
      'Use the code below to complete your login. This code is valid for 10 minutes.',
  ]" :outroLines="[
      'If you did not request this code, you can safely ignore this email.',
  ]">

    {{-- Custom Content Slot for the OTP --}}
    <div style="margin: 24px 0; text-align: center;">
      <p style="display: inline-block; padding: 12px 24px; background-color: #F3F4F6; border-radius: 8px; font-size: 28px; font-weight: 700; letter-spacing: 4px; color: #111827;">
        {{ $otp }}
      </p>
    </div>

  </x-emails.layouts.app>
@endsection