@props([
    'image' => null,
    'alt' => '',
    'height' => 'h-48 sm:h-[260px]',
    'banners' => collect(),
])

@php
    $bannerItems = collect($banners ?? [])->filter();
    if ($image && $bannerItems->isEmpty()) {
        $bannerItems = collect([['image_path' => $image, 'title' => $alt]]);
    }
@endphp

<div class="relative overflow-hidden rounded-2xl border border-slate-300 bg-gradient-to-tr from-slate-800 via-slate-700 to-slate-600 shadow-md {{ $height }}"
    data-hero-slider>
    <div class="flex h-full w-full transition-transform duration-700 ease-in-out" data-hero-track>
        @foreach ($bannerItems as $banner)
            @php
                $rawPath = is_array($banner) ? ($banner['image_path'] ?? '') : ($banner->image_path ?? '');
                $isAbsolute =  preg_match('/^https?:\/\//', $rawPath);
                $src = $isAbsolute ? $rawPath : asset('storage/' . ltrim($rawPath, '/'));
            @endphp
            <div class="min-w-full h-full shrink-0">
                <img src="{{ $src }}"
                    alt="{{ is_array($banner) ? ($banner['title'] ?? '') : ($banner->localized_title ?? $banner->title ?? '') }}"
                    class="h-full w-full object-contain md:object-cover">
            </div>
        @endforeach
    </div>
    @if ($bannerItems->count() > 1)
        <div class="absolute inset-x-0 bottom-2 flex justify-center gap-2">
            @foreach ($bannerItems as $idx => $banner)
                <span class="h-2 w-2 rounded-full bg-white/70" data-hero-dot="{{ $idx }}"></span>
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
                    const dots = slider.querySelectorAll('[data-hero-dot]');
                    const slides = track ? Array.from(track.children) : [];
                    if (!track || slides.length <= 1) return;

                    let index = 0;
                    const isRtl = getComputedStyle(slider).direction === 'rtl';

                    const update = () => {
                        const offset = index * 100;
                        track.style.transform = `translateX(${isRtl ? offset : -offset}%)`;
                        dots.forEach((dot, i) => {
                            dot.style.opacity = i === index ? '1' : '0.4';
                        });
                    };

                    const next = () => {
                        index = (index + 1) % slides.length;
                        update();
                    };

                    update();
                    let timer = setInterval(next, 5000);

                    slider.addEventListener('mouseenter', () => clearInterval(timer));
                    slider.addEventListener('mouseleave', () => {
                        timer = setInterval(next, 5000);
                    });

                    window.addEventListener('resize', update);
                });
            });
        </script>
    @endpush
@endonce
