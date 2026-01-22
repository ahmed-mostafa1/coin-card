<!doctype html>
<html lang="ar" dir="rtl">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>@yield('title', 'Arab 8BP')</title>
        @vite(['resources/css/app.css', 'resources/js/app.js'])

        <style>
            main {
                position: relative;
                z-index: 1;
            }
        </style>
    </head>
    <body class="min-h-screen bg-slate-50">
        <main class="mx-auto flex min-h-screen w-full max-w-md flex-col justify-start px-4 py-10 md:py-14">
            <a href="{{ route('home') }}" class="mb-6 text-lg font-bold text-emerald-700">Arab 8BP</a>
            <div class="w-full rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
                @yield('content')
            </div>
        </main>

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
