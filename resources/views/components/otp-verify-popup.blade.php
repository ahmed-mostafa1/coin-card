@props(['open' => false])

<div x-data="{ 
    open: {{ $open ? 'true' : 'false' }},
    otp: '',
    message: '',
    loading: false,
    resendLoading: false,
    timer: 0,
    init() {
        if (this.open) {
            this.startTimer();
        }
    },
    verify() {
        this.loading = true;
        this.message = '';
        axios.post('{{ route('otp.verify') }}', { otp: this.otp })
            .then(response => {
                this.message = response.data.message;
                setTimeout(() => {
                    this.open = false;
                    window.location.href = response.data.redirect;
                }, 1000);
            })
            .catch(error => {
                this.message = error.response.data.message || 'Error verifying OTP';
            })
            .finally(() => {
                this.loading = false;
            });
    },
    resend() {
        this.resendLoading = true;
        this.message = '';
        axios.post('{{ route('otp.resend') }}')
            .then(response => {
                this.message = response.data.message;
                this.timer = 600; // 10 minutes reset
                this.startTimer();
            })
            .catch(error => {
                this.message = error.response.data.message || 'Error resending OTP';
            })
            .finally(() => {
                this.resendLoading = false;
            });
    },
    startTimer() {
        // Simple timer logic can be added here if needed
    }
}"
x-show="open"
x-cloak
@open-otp-popup.window="open = true"
class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 backdrop-blur-sm"
>
    <div class="w-full max-w-md rounded-2xl bg-white p-6 shadow-xl dark:bg-slate-800" @click.outside="open = false">
        <div class="text-center">
            <h2 class="text-xl font-bold text-slate-900 dark:text-white">{{ __('messages.account_activation') }}</h2>
            <p class="mt-2 text-sm text-slate-600 dark:text-slate-300">
                {{ __('messages.otp_sent_email_instruction') }}
            </p>
        </div>

        <div class="mt-6">
            <input type="text" x-model="otp" placeholder="Enter OTP Code" class="w-full rounded-lg border border-slate-300 px-4 py-2 text-center text-lg tracking-widest outline-none focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 dark:border-slate-600 dark:bg-slate-700 dark:text-white" maxlength="6">
            
            <p x-show="message" x-text="message" class="mt-2 text-center text-sm font-medium text-red-500"></p>

            <button @click="verify" :disabled="loading || otp.length < 6" class="mt-4 w-full rounded-lg bg-indigo-600 px-4 py-2 text-white hover:bg-indigo-700 disabled:opacity-50">
                <span x-show="!loading">{{ __('messages.verify') }}</span>
                <span x-show="loading">{{ __('messages.processing') }}...</span>
            </button>

            <button @click="resend" :disabled="resendLoading" class="mt-4 w-full text-sm text-indigo-600 hover:text-indigo-800 dark:text-indigo-400 dark:hover:text-indigo-300">
                <span x-show="!resendLoading">{{ __('messages.resend_otp') }}</span>
                <span x-show="resendLoading">{{ __('messages.sending') }}...</span>
            </button>
        </div>
    </div>
</div>
