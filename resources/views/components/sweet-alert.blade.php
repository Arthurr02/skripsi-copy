@once
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        (() => {
            const alertClasses = {
                popup: 'rounded-xl border border-slate-200 p-6 font-sans shadow-xl',
                title: 'text-xl font-extrabold tracking-tight text-slate-800',
                htmlContainer: 'mt-2 text-sm font-medium leading-relaxed text-slate-500',
                confirmButton: 'rounded-lg bg-blue-600 px-5 py-2.5 text-sm font-bold text-white transition-colors hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2',
                cancelButton: 'rounded-lg bg-slate-100 px-5 py-2.5 text-sm font-bold text-slate-700 transition-colors hover:bg-slate-200 focus:outline-none focus:ring-2 focus:ring-slate-400 focus:ring-offset-2',
            };

            const standardizeOptions = (options) => {
                if (!options || typeof options !== 'object' || Array.isArray(options)) {
                    return options;
                }

                return {
                    ...options,
                    buttonsStyling: false,
                    reverseButtons: options.reverseButtons ?? true,
                    customClass: {
                        ...(options.customClass || {}),
                        ...alertClasses,
                    },
                };
            };

            const standardizeInstance = (instance) => {
                const fire = instance.fire.bind(instance);
                const mixin = instance.mixin.bind(instance);

                instance.fire = (options, ...argumentsLain) => fire(standardizeOptions(options), ...argumentsLain);
                instance.mixin = (options = {}) => standardizeInstance(mixin(standardizeOptions(options)));

                return instance;
            };

            window.Swal = standardizeInstance(window.Swal);
            window.rekrutmenAlert = (options) => window.Swal.fire(options);

            const flashAlert = @json(session('flash_alert'));
            if (flashAlert) {
                document.addEventListener('DOMContentLoaded', () => window.rekrutmenAlert(flashAlert), { once: true });
            }
        })();
    </script>
@endonce
