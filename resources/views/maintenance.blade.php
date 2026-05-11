<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" dir="{{ app()->getLocale() == 'ar' ? 'rtl' : 'ltr' }}" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>المتجر تحت الصيانة | {{ \App\Models\SiteSetting::get('logo_text', 'S7SH.com') }}</title>
    
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@400;500;700;800;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Vite -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-tajawal text-slate-900 bg-slate-50 dark:bg-slate-900 dark:text-slate-50 min-h-screen flex items-center justify-center p-4">
    @php
        $maintenanceMessage = \App\Models\SiteSetting::get('maintenance_message', 'المتجر تحت الصيانة حالياً. سنعود قريباً!');
        $maintenanceImage = \App\Models\SiteSetting::get('maintenance_image');
        $maintenanceButtonText = \App\Models\SiteSetting::get('maintenance_button_text');
        $maintenanceButtonUrl = \App\Models\SiteSetting::get('maintenance_button_url');
    @endphp

    <div class="max-w-2xl w-full text-center space-y-8 animate-fade-in-up">
        
        <!-- Logo / Image Area -->
        <div class="flex justify-center mb-8 relative">
            @if($maintenanceImage)
                <img src="{{ asset('storage/' . $maintenanceImage) }}" alt="Maintenance" class="max-h-64 object-contain animate-float drop-shadow-2xl rounded-3xl" />
            @else
                <div class="relative">
                    <!-- Glow effect -->
                    <div class="absolute -inset-4 bg-emerald-500/20 rounded-full blur-2xl animate-pulse"></div>
                    <!-- Icon -->
                    <div class="relative bg-white dark:bg-slate-800 p-8 rounded-full shadow-2xl border border-slate-100 dark:border-slate-700">
                        <i class="fa-solid fa-person-digging text-6xl text-emerald-500 drop-shadow-md"></i>
                        <!-- Tool cog -->
                        <div class="absolute -top-2 -right-2 bg-rose-500 text-white p-3 rounded-full shadow-lg animate-spin-slow">
                            <i class="fa-solid fa-gear text-xl"></i>
                        </div>
                    </div>
                </div>
            @endif
        </div>

        <!-- Content Area -->
        <div class="bg-white dark:bg-slate-800 rounded-3xl shadow-xl border border-slate-100 dark:border-slate-700 p-8 md:p-12 backdrop-blur-sm bg-white/90 dark:bg-slate-800/90 relative overflow-hidden">
            
            <!-- Decorative Elements -->
            <div class="absolute top-0 left-0 w-full h-1 bg-gradient-to-r from-emerald-400 via-teal-500 to-emerald-600"></div>
            
            <h1 class="text-3xl md:text-4xl font-bold mb-6 text-slate-800 dark:text-slate-100 leading-tight">
                عدنا لنبني شيئاً أفضل <br/>
                <span class="text-emerald-600 dark:text-emerald-400">من أجلك</span>
            </h1>
            
            <p class="text-lg md:text-xl text-slate-600 dark:text-slate-300 leading-relaxed mb-8 max-w-lg mx-auto">
                {{ $maintenanceMessage }}
            </p>
            
            @if($maintenanceButtonText && $maintenanceButtonUrl)
                <a href="{{ $maintenanceButtonUrl }}" target="_blank" rel="noopener noreferrer" 
                   class="inline-flex items-center gap-3 px-8 py-4 bg-emerald-600 hover:bg-emerald-700 text-white rounded-2xl font-bold text-lg transition-all duration-300 transform hover:-translate-y-1 hover:shadow-xl hover:shadow-emerald-500/30 group">
                    <span>{{ $maintenanceButtonText }}</span>
                    <i class="fa-solid fa-arrow-left group-hover:-translate-x-1 transition-transform rtl:rotate-180 rtl:group-hover:translate-x-1"></i>
                </a>
            @endif
            
            <!-- Small Login Link for Admins -->
            <div class="mt-8 pt-8 border-t border-slate-100 dark:border-slate-700/50">
                <p class="text-sm text-slate-500 dark:text-slate-400">
                    هل أنت مدير؟ 
                    <a href="{{ route('login') }}" class="text-emerald-600 hover:text-emerald-700 dark:text-emerald-400 dark:hover:text-emerald-300 font-semibold underline underline-offset-4 decoration-2 decoration-emerald-500/30 hover:decoration-emerald-500 transition-all">
                        تسجيل الدخول
                    </a>
                </p>
            </div>
        </div>
        
    </div>

    <style>
        @keyframes fade-in-up {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }
        @keyframes float {
            0% { transform: translateY(0px); }
            50% { transform: translateY(-15px); }
            100% { transform: translateY(0px); }
        }
        @keyframes spin-slow {
            from { transform: rotate(0deg); }
            to { transform: rotate(360deg); }
        }
        .animate-fade-in-up {
            animation: fade-in-up 0.8s ease-out forwards;
        }
        .animate-float {
            animation: float 6s ease-in-out infinite;
        }
        .animate-spin-slow {
            animation: spin-slow 8s linear infinite;
        }
    </style>
</body>
</html>
