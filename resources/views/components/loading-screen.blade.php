<div
    x-data="{ pageLoading: false }"
    x-init="
        document.addEventListener('click', (e) => {
            let link = e.target.closest('a');
            if (
                link &&
                link.href &&
                !link.getAttribute('href').startsWith('#') &&
                link.target !== '_blank' &&
                !link.hasAttribute('download') &&
                !link.hasAttribute('data-no-loading') &&
                !e.ctrlKey &&
                !e.metaKey
            ) {
                // Jangan langsung aktifkan loading secara instan yang memblokir klik
                setTimeout(() => {
                    pageLoading = true;
                }, 50); // Jeda sangat singkat (50 milidetik) agar klik pertama sukses memicu navigasi dulu
            }
        });

        document.addEventListener('submit', (e) => {
            if (!e.defaultPrevented && !e.target.dataset.noLoading) {
                pageLoading = true;
            }
        });

        window.addEventListener('pageshow', (event) => {
            if (event.persisted) pageLoading = false;
        });
    "
    :class="pageLoading
        ? 'opacity-100 pointer-events-auto z-[9999]'
        : 'opacity-0 pointer-events-none -z-50'"
    class="fixed inset-0 flex flex-col items-center justify-center bg-white/50 backdrop-blur-md transition-opacity duration-200"
>
    <script src="https://unpkg.com/@lottiefiles/lottie-player@latest/dist/lottie-player.js"></script>

    <div class="relative z-0 flex justify-center items-center">
        <lottie-player
            src="{{ asset('json/loading.json') }}"
            background="transparent"
            speed="1.2"
            style="width: 140px; height: 140px"
            loop
            autoplay
        >
        </lottie-player>
    </div>

    <div class="flex flex-col items-center relative z-10 -mt-12">
        <h2
            class="text-xs font-bold text-[#43a0ff] tracking-[0.25em] uppercase drop-shadow-sm animate-pulse"
        >
            Memuat Halaman
        </h2>
    </div>
</div>
