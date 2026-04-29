@props([
    'image'   => null,
    'alt'     => '',
    'height'  => '',
    'banners' => collect(),
])

@php
    $bannerItems = collect($banners ?? [])->filter();
    if ($image && $bannerItems->isEmpty()) {
        $bannerItems = collect([['image_path' => $image, 'title' => $alt]]);
    }
@endphp

<div class="relative w-full overflow-hidden rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-900 shadow-md {{ $height }}"
    style="aspect-ratio: var(--store-hero-ratio, 16 / 8);"
    dir="ltr" data-hero-slider>

    @if($bannerItems->isNotEmpty())
        <div class="flex h-full w-full transition-transform duration-700 ease-in-out" data-hero-track dir="ltr">
            @foreach ($bannerItems as $banner)
                @php
                    $rawPath   = is_array($banner) ? ($banner['image_path'] ?? '') : ($banner->image_path ?? '');
                    $isAbs     = preg_match('/^https?:\/\//', $rawPath);
                    $src       = $isAbs ? $rawPath : asset('storage/' . ltrim($rawPath, '/'));
                    $fallback  = asset('img/placeholder-banner.jpg');
                @endphp
                <div class="relative flex h-full w-full shrink-0 flex-[0_0_100%] items-center justify-center bg-slate-100 dark:bg-slate-900">
                    <img src="{{ $src }}"
                        alt="{{ is_array($banner) ? ($banner['title'] ?? '') : ($banner->localized_title ?? $banner->title ?? '') }}"
                        width="1600"
                        height="800"
                        loading="{{ $loop->first ? 'eager' : 'lazy' }}"
                        decoding="async"
                        onerror="this.onerror=null;this.src='{{ $fallback }}';"
                        class="h-full w-full object-contain">
                    {{-- Bottom gradient scrim for readability --}}
                    <div class="absolute inset-x-0 bottom-0 h-16 bg-gradient-to-t from-black/50 to-transparent pointer-events-none"></div>
                </div>
            @endforeach
        </div>
    @else
        <div class="flex h-full w-full items-center justify-center text-slate-500">
            <i class="fa-solid fa-image text-4xl"></i>
        </div>
    @endif

    {{-- Prev / Next arrows --}}
    @if ($bannerItems->count() > 1)
        <button type="button" data-hero-prev
            class="absolute start-2 top-1/2 -translate-y-1/2 flex h-8 w-8 items-center justify-center rounded-full bg-black/40 text-white backdrop-blur-sm transition hover:bg-black/65 opacity-0 group-hover:opacity-100 focus:opacity-100"
            aria-label="Previous slide">
            <i class="fa-solid fa-chevron-left text-sm"></i>
        </button>
        <button type="button" data-hero-next
            class="absolute end-2 top-1/2 -translate-y-1/2 flex h-8 w-8 items-center justify-center rounded-full bg-black/40 text-white backdrop-blur-sm transition hover:bg-black/65 opacity-0 group-hover:opacity-100 focus:opacity-100"
            aria-label="Next slide">
            <i class="fa-solid fa-chevron-right text-sm"></i>
        </button>
    @endif

    {{-- Numbered pill pagination --}}
    @if ($bannerItems->count() > 1)
        <div class="absolute bottom-2.5 end-3 flex gap-1.5 items-center" data-hero-dots>
            @foreach ($bannerItems as $index => $banner)
                <button type="button"
                    class="rounded-full text-[10px] font-bold px-2 py-0.5 bg-white/25 text-white backdrop-blur-sm transition-all duration-300 shadow-sm"
                    aria-label="Slide {{ $index + 1 }}">
                    {{ str_pad($index + 1, 2, '0', STR_PAD_LEFT) }}
                </button>
            @endforeach
        </div>
    @endif
</div>

@once
@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', () => {
    document.querySelectorAll('[data-hero-slider]').forEach((slider) => {
        const track  = slider.querySelector('[data-hero-track]');
        const dotsEl = slider.querySelector('[data-hero-dots]');
        const dots   = dotsEl ? Array.from(dotsEl.children) : [];
        const slides = track ? Array.from(track.children) : [];
        const btnPrev = slider.querySelector('[data-hero-prev]');
        const btnNext = slider.querySelector('[data-hero-next]');
        const setRatioFromIndex = (currentIndex) => {
            const currentImage = slides[currentIndex]?.querySelector('img');

            if (!currentImage || !currentImage.naturalWidth || !currentImage.naturalHeight) {
                return;
            }

            slider.style.setProperty('--store-hero-ratio', `${currentImage.naturalWidth} / ${currentImage.naturalHeight}`);
        };

        if (!track || slides.length <= 1) {
            setRatioFromIndex(0);
            if (btnPrev) btnPrev.style.display = 'none';
            if (btnNext) btnNext.style.display = 'none';
            return;
        }

        let index = 0;

        const showArrows = () => {
            if (btnPrev) btnPrev.style.opacity = '1';
            if (btnNext) btnNext.style.opacity = '1';
        };
        const hideArrows = () => {
            if (btnPrev) btnPrev.style.opacity = '0';
            if (btnNext) btnNext.style.opacity = '0';
        };

        const update = () => {
            track.style.transform = `translateX(${-index * 100}%)`;
            setRatioFromIndex(index);
            dots.forEach((dot, i) => {
                dot.style.background  = i === index ? 'rgba(255,255,255,0.9)' : 'rgba(255,255,255,0.3)';
                dot.style.fontWeight  = i === index ? '800' : '600';
                dot.style.transform   = i === index ? 'scale(1.1)' : 'scale(1)';
            });
        };

        const go = (n) => {
            index = (n + slides.length) % slides.length;
            update();
        };

        dots.forEach((dot, i) => dot.addEventListener('click', () => { go(i); resetTimer(); }));
        if (btnPrev) btnPrev.addEventListener('click', () => { go(index - 1); resetTimer(); });
        if (btnNext) btnNext.addEventListener('click', () => { go(index + 1); resetTimer(); });
        slides.forEach((slide, slideIndex) => {
            const image = slide.querySelector('img');

            if (!image) {
                return;
            }

            const applyRatio = () => {
                if (slideIndex === index) {
                    setRatioFromIndex(slideIndex);
                }
            };

            if (image.complete) {
                applyRatio();
                return;
            }

            image.addEventListener('load', applyRatio);
        });

        update();
        let timer = setInterval(() => go(index + 1), 5000);

        const resetTimer = () => { clearInterval(timer); timer = setInterval(() => go(index + 1), 5000); };

        slider.addEventListener('mouseenter', () => { clearInterval(timer); showArrows(); });
        slider.addEventListener('mouseleave', () => { timer = setInterval(() => go(index + 1), 5000); hideArrows(); });

        window.addEventListener('resize', update);
    });
});
</script>
@endpush
@endonce
