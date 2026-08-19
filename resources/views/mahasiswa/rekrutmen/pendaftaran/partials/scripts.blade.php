<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const premiumSwal = Swal.mixin({
            customClass: {
                popup: 'rounded-lg shadow-sm border border-slate-200 font-sans p-6',
                title: 'text-xl font-extrabold text-slate-800 tracking-tight',
                htmlContainer: 'text-sm font-normal text-slate-500 mt-2',
                confirmButton:
                    'px-6 py-2.5 rounded-md font-bold text-sm bg-blue-600 hover:bg-blue-700 text-white transition-colors',
            },
            buttonsStyling: false,
        });

        @if ($errors->any() || session('error_server'))
        setTimeout(() => {
            document
                .querySelectorAll('.loader, #loader, #preloader, [class*="memuat"]')
                .forEach((el) => (el.style.display = 'none'));
            document.body.classList.remove('overflow-hidden');
        }, 100);
        @endif

        @if ($errors->any())
        premiumSwal.fire({
            icon: 'error',
            title: 'Pendaftaran Tertunda',
            html: `
                <div class="text-sm text-red-600 text-left mt-2">
                    <p class="font-bold mb-2">Server menolak data karena kesalahan validasi:</p>
                    <ul class="list-disc pl-5 font-medium space-y-1 text-xs text-red-700">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            `,
            customClass: {
                popup: 'rounded-lg shadow-sm border border-slate-200 font-sans p-6',
                title: 'text-xl font-extrabold text-slate-800 tracking-tight',
                confirmButton:
                    'px-6 py-2.5 rounded-md font-bold text-sm bg-red-600 hover:bg-red-700 text-white transition-colors',
            },
        });
        @endif

        @if (session('error_server'))
        premiumSwal.fire({
            icon: 'error',
            title: 'Gagal Memproses',
            text: '{!!
        session(
            'error_server',
        )
    !!}',
            customClass: {
                popup: 'rounded-lg shadow-sm border border-slate-200 font-sans p-6',
                title: 'text-xl font-extrabold text-slate-800 tracking-tight',
                htmlContainer: 'text-sm font-normal text-slate-500 mt-2',
                confirmButton:
                    'px-6 py-2.5 rounded-md font-bold text-sm bg-red-600 hover:bg-red-700 text-white transition-colors',
            },
        });
        @endif
    });
</script>
