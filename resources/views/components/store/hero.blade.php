@props([
    'image' => null,
    'alt' => '',
    'height' => 'h-40 sm:h-48 md:h-[260px]',
    'banners' => collect(),
])

@php
    $bannerItems = collect($banners ?? [])->filter();
    if ($image && $bannerItems->isEmpty()) {
        $bannerItems = collect([['image_path' => $image, 'title' => $alt]]);
    }
@endphp

<div class="relative w-full overflow-hidden rounded-xl border border-slate-300 bg-gradient-to-tr from-slate-800 via-slate-700 to-slate-600 shadow-md {{ $height }} sm:w-4/5 sm:mx-auto"
    dir="ltr" data-hero-slider>
    @if($bannerItems->isNotEmpty())
        <div class="flex h-full w-full transition-transform duration-700 ease-in-out" data-hero-track>
            @foreach ($bannerItems as $banner)
                @php
                    $rawPath = is_array($banner) ? ($banner['image_path'] ?? '') : ($banner->image_path ?? '');
                    $isAbsolute = preg_match('/^https?:\/\//', $rawPath);
                    $src = $isAbsolute ? $rawPath : asset('storage/' . ltrim($rawPath, '/'));
                    $fallback = asset('img/placeholder-banner.jpg');
                @endphp
                <div class="min-w-full h-full shrink-0 flex items-center justify-center">
                    <img src="{{ $src }}"
                        alt="{{ is_array($banner) ? ($banner['title'] ?? '') : ($banner->localized_title ?? $banner->title ?? '') }}"
                        onerror="this.onerror=null;this.src='{{ $fallback }}';"
                        class="h-full w-full object-cover">
                </div>
            @endforeach
        </div>
    @else
        <!-- Fallback when no banners -->
        <div class="flex h-full w-full items-center justify-center">
            <div class="text-center text-slate-400">
                <i class="fa-solid fa-image text-4xl mb-2"></i>
                <p class="text-sm">{{ __('messages.no_banner') ?? 'No banner available' }}</p>
            </div>
        </div>
    @endif
    
    @if ($bannerItems->count() > 1)
        <div class="absolute inset-x-0 bottom-2 flex justify-center gap-2" data-hero-dots>
            @foreach ($bannerItems as $index => $banner)
                <button type="button" class="h-2 w-2 rounded-full bg-white transition-opacity duration-300 shadow-sm"
                    aria-label="Slide {{ $index + 1 }}"></button>
            @endforeach
        </div>
    @endif
</div>

@once
    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', () => {
                document.querySelectorAll('[data-hero-slider]').forEach((slider) => {
                    const track = slider.querySelector('[data-hero-track]');
                    const dotsContainer = slider.querySelector('[data-hero-dots]');
                    const dots = dotsContainer ? Array.from(dotsContainer.children) : [];
                    const slides = track ? Array.from(track.children) : [];

                    if (!track || slides.length <= 1) return;

                    let index = 0;
                    
                    const update = () => {
                        const offset = index * 100;
                        const translateValue = -offset;
                        
                        track.style.transform = `translateX(${translateValue}%)`;
                        
                        dots.forEach((dot, i) => {
                            dot.style.opacity = i === index ? '1' : '0.4';
                        });
                    };

                    const next = () => {
                        index = (index + 1) % slides.length;
                        update();
                    };

                    // Add click handlers for dots
                    dots.forEach((dot, i) => {
                        dot.addEventListener('click', () => {
                            index = i;
                            update();
                            // Reset timer on manual interaction
                            clearInterval(timer);
                            timer = setInterval(next, 5000);
                        });
                    });

                    update();
                    let timer = setInterval(next, 5000);

                    slider.addEventListener('mouseenter', () => clearInterval(timer));
                    slider.addEventListener('mouseleave', () => {
                        clearInterval(timer); // ensure clear before restart
                        timer = setInterval(next, 5000);
                    });

                    window.addEventListener('resize', update);
                });
            });
        </script>
    @endpush
@endonce
