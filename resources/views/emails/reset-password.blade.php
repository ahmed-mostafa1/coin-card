<x-emails.app
  :subject="__('Reset Your Password')"
  :title="__('Reset Your Password')"
  :introLines="[
      __('You are receiving this email because we received a password reset request for your account.'),
      __('This password reset link will expire in 60 minutes.'),
      __('If you did not request a password reset, no further action is required.'),
  ]"
  :actionText="__('Reset Password')"
  :actionUrl="$resetUrl"
  :helperText="__('If you\'re having trouble clicking the button, copy and paste the URL below into your web browser:')"
  :fallbackUrl="$resetUrl"
/>
