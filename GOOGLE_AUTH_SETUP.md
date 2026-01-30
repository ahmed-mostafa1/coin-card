# Google Sign-In/Sign-Up Setup Guide

## ✅ Implementation Complete!

Your application now has **full Google OAuth authentication** with automatic account linking.

---

## 🎯 What's Been Implemented

### 1. **Environment Configuration**
- ✅ Google Client ID and Secret added to `.env`
- ✅ Redirect URI configured for local development
- ✅ Socialite service provider configured

### 2. **Authentication Flow**
- ✅ **Sign Up with Google**: New users can create accounts using Google
- ✅ **Sign In with Google**: Existing users can log in using Google
- ✅ **Automatic Account Linking**: If a user signs up with email/password and later uses Google with the same email, accounts are automatically linked

### 3. **User Interface**
- ✅ Google Sign-In button on login page with official Google logo
- ✅ Google Sign-Up button on register page with official Google logo
- ✅ Visual dividers ("or") for better UX
- ✅ Hover effects and smooth transitions
- ✅ Full Arabic and English translation support

### 4. **Security Features**
- ✅ Banned user check (prevents banned users from logging in via Google)
- ✅ Random password generation for Google-only accounts
- ✅ Automatic role assignment (customer role)
- ✅ Avatar synchronization from Google profile

---

## 🚀 For Production Deployment

When you deploy to production, follow these steps:

### Step 1: Update Google Cloud Console

1. Go to [Google Cloud Console](https://console.cloud.google.com/)
2. Select your project
3. Navigate to **APIs & Services** → **Credentials**
4. Click on your OAuth 2.0 Client ID
5. Add your **production domain** to "Authorized redirect URIs":
   ```
   https://yourdomain.com/auth/google/callback
   ```
6. Click **Save**

### Step 2: Update Production `.env`

Update your production `.env` file with:

```env
APP_URL=https://yourdomain.com

GOOGLE_CLIENT_ID=1041031292395-osvicg5c4mscdu9ag6qpjun2dggka349.apps.googleusercontent.com
GOOGLE_CLIENT_SECRET=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
GOOGLE_REDIRECT_URI=${APP_URL}/auth/google/callback
```

### Step 3: Clear Cache

After updating `.env` on production, run:

```bash
php artisan config:clear
php artisan cache:clear
```

---

## 🧪 Testing Locally

### Test Sign-Up Flow:
1. Go to `http://localhost:8000/register`
2. Click "Sign up with Google"
3. Select a Google account
4. Verify you're redirected to the dashboard
5. Check that your name, email, and avatar are saved

### Test Sign-In Flow:
1. Logout
2. Go to `http://localhost:8000/login`
3. Click "Login with Google"
4. Select the same Google account
5. Verify you're logged in successfully

### Test Account Linking:
1. Create an account with email/password
2. Logout
3. Try to sign in with Google using the **same email**
4. Verify that your Google ID and avatar are now linked to your existing account

---

## 📋 How It Works

### New User Flow (Sign-Up):
```
User clicks "Sign up with Google"
    ↓
Redirected to Google OAuth consent screen
    ↓
User authorizes the app
    ↓
Google redirects back with user data
    ↓
System checks if user exists by Google ID or email
    ↓
If NOT exists: Create new user with Google data
    ↓
Assign 'customer' role
    ↓
Log user in and redirect to dashboard
```

### Existing User Flow (Account Linking):
```
User clicks "Login with Google"
    ↓
Redirected to Google OAuth consent screen
    ↓
User authorizes the app
    ↓
Google redirects back with user data
    ↓
System finds existing user by email
    ↓
Updates user record with Google ID and avatar
    ↓
Log user in and redirect to dashboard
```

---

## 🔒 Security Notes

1. **Banned Users**: Users who are banned cannot log in via Google (they'll be redirected to login with an error message)

2. **Password**: Users who sign up via Google get a random 32-character password. They can set a custom password later via "Change Password" if they want to use email/password login.

3. **Email Verification**: Google accounts are considered verified since Google has already verified the email.

4. **Avatar Storage**: Avatars are stored as URLs pointing to Google's servers. If you want to download and store them locally, you can modify the `GoogleController`.

---

## 🎨 UI Components

### Login Page
- Email/password form
- "or" divider
- Google Sign-In button with logo

### Register Page
- Registration form (name, email, password)
- "or" divider
- Google Sign-Up button with logo

---

## 🌐 Routes

| Route | Method | Description |
|-------|--------|-------------|
| `/auth/google` | GET | Redirects to Google OAuth |
| `/auth/google/callback` | GET | Handles Google callback |

---

## 📝 Translation Keys

### English (`lang/en/messages.php`):
- `login_google`: "Login with Google"
- `signup_google`: "Sign up with Google"
- `or`: "or"

### Arabic (`lang/ar/messages.php`):
- `login_google`: "تسجيل الدخول عبر Google"
- `signup_google`: "إنشاء حساب عبر Google"
- `or`: "أو"

---

## 🐛 Troubleshooting

### Issue: "Error 400: redirect_uri_mismatch"
**Solution**: Make sure the redirect URI in Google Cloud Console exactly matches:
- Local: `http://localhost:8000/auth/google/callback`
- Production: `https://yourdomain.com/auth/google/callback`

### Issue: "This app isn't verified"
**Solution**: This is normal during development. Click "Advanced" → "Go to [App Name] (unsafe)" to proceed. For production, you can verify your app with Google.

### Issue: User gets logged in but avatar doesn't show
**Solution**: The avatar URL from Google might be blocked by CORS. Consider downloading and storing avatars locally.

---

## 📞 Support

If you encounter any issues:
1. Check the Laravel logs: `storage/logs/laravel.log`
2. Verify your `.env` credentials are correct
3. Ensure Google Cloud Console redirect URIs are properly configured
4. Clear config cache: `php artisan config:clear`

---

## ✨ Features Summary

✅ Google Sign-In  
✅ Google Sign-Up  
✅ Automatic account linking  
✅ Beautiful UI with Google logo  
✅ Full RTL support (Arabic)  
✅ Banned user protection  
✅ Role-based access control  
✅ Avatar synchronization  

**Everything is ready to go! Just test it locally and deploy to production when ready.** 🚀
