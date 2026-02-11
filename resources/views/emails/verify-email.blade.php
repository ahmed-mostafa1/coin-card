<x-emails.app
  :subject="__('Verify Your Email Address')"
  :title="__('Verify Your Email Address')"
  :introLines="[
      __('Please click the button below to verify your email address.'),
      __('This link will expire in 60 minutes.'),
  ]"
  :actionText="__('Verify Email Address')"
  :actionUrl="$verificationUrl"
  :helperText="__('If you\'re having trouble clicking the button, copy and paste the URL below into your web browser:')"
  :fallbackUrl="$verificationUrl"
/>
