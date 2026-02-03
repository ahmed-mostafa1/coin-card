<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" dir="{{ app()->getLocale() == 'ar' ? 'rtl' : 'ltr' }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Arab 8BP.in')</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <script>
        // Initialize theme before page renders
        if (localStorage.theme === 'dark' || (!('theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
            document.documentElement.classList.add('dark');
        } else {
            document.documentElement.classList.remove('dark');
        }
    </script>

    <style>
        main {
            position: relative;
            z-index: 1;
        }
        
        /* Gradient background */
        body::before {
            content: '';
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: linear-gradient(135deg, #f0fdf4 0%, #ecfdf5 50%, #f0fdfa 100%);
            z-index: 0;
        }
        
        .dark body::before {
            background: linear-gradient(135deg, #0f172a 0%, #1e293b 50%, #0f172a 100%);
        }
    </style>
</head>

<body class="min-h-screen bg-transparent transition-colors duration-200" 
      x-data="{ darkMode: localStorage.theme === 'dark' || (!('theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches) }"
      x-init="$watch('darkMode', val => {
          localStorage.theme = val ? 'dark' : 'light';
          if(val) document.documentElement.classList.add('dark');
          else document.documentElement.classList.remove('dark');
      })">
    
    <main class="mx-auto flex min-h-screen w-full max-w-md flex-col justify-start px-4 py-10 md:py-16">
        {{-- Logo & Theme Toggle --}}
        <div class="mb-8 flex items-center justify-center relative">
            <a href="{{ route('home') }}" class="flex items-center justify-center">
                @if($sharedLogoType === 'image' && $sharedLogoImage)
                    <img src="{{ asset('storage/' . $sharedLogoImage) }}" alt="Logo" class="h-12 object-contain">
                @else
                    <span class="text-2xl font-bold text-emerald-700 dark:text-emerald-400 transition hover:text-emerald-800 dark:hover:text-emerald-300">{{ $sharedLogoText }}</span>
                @endif
            </a>
            
            {{-- Theme Toggle (Absolute positioned to the right) --}}
            <button type="button" @click="darkMode = !darkMode" 
                    class="absolute right-0 rounded-lg p-2 text-slate-500 hover:bg-white/50 dark:hover:bg-slate-800/50 hover:text-slate-700 dark:text-slate-400 dark:hover:text-slate-200 transition">
                <svg x-show="!darkMode" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z" />
                </svg>
                <svg x-show="darkMode" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" x-cloak>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z" />
                </svg>
            </button>
        </div>

        {{-- Auth Card --}}
        <div class="w-full rounded-3xl border border-slate-200/60 dark:border-slate-700/60 bg-white/80 dark:bg-slate-800/80 backdrop-blur-xl p-8 shadow-2xl shadow-slate-900/5 dark:shadow-slate-900/30">
            @yield('content')
        </div>

        {{-- Language Switcher --}}
        <div class="mt-6 text-center">
            @if(app()->getLocale() == 'ar')
                <a href="{{ route('lang.switch', 'en') }}" 
                   class="inline-flex items-center gap-2 text-sm font-semibold text-slate-600 dark:text-slate-400 transition hover:text-emerald-600 dark:hover:text-emerald-400">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5h12M9 3v2m1.048 9.5A18.022 18.022 0 016.412 9m6.088 9h7M11 21l5-10 5 10M12.751 5C11.783 10.77 8.07 15.61 3 18.129" />
                    </svg>
                    English
                </a>
            @else
                <a href="{{ route('lang.switch', 'ar') }}" 
                   class="inline-flex items-center gap-2 text-sm font-semibold text-slate-600 dark:text-slate-400 transition hover:text-emerald-600 dark:hover:text-emerald-400">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5h12M9 3v2m1.048 9.5A18.022 18.022 0 016.412 9m6.088 9h7M11 21l5-10 5 10M12.751 5C11.783 10.77 8.07 15.61 3 18.129" />
                    </svg>
                    العربية
                </a>
            @endif
        </div>
    </main>

    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.13.3/dist/cdn.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            document.querySelectorAll('body *').forEach((el) => {
                const rect = el.getBoundingClientRect();
                const style = getComputedStyle(el);
                const isFullScreen = rect.width >= window.innerWidth && rect.height >= window.innerHeight;
                const isFixed = style.position === 'fixed';
                const isOverlayCandidate = isFullScreen && isFixed && !['HTML', 'BODY', 'MAIN'].includes(el.tagName);

                if (isOverlayCandidate) {
                    el.style.pointerEvents = 'none';
                    el.style.display = 'none';
                }
            });
        });
    </script>
</body>

</html>