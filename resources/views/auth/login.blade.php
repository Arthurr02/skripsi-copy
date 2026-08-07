<x-guest-layout>
    <div
        class="min-h-screen relative flex items-center justify-center w-full bg-[#fafafa] overflow-hidden font-sans selection:bg-blue-600 selection:text-white"
    >
        <div
            class="absolute top-0 w-full h-full overflow-hidden pointer-events-none"
        >
            <div
                class="absolute inset-0 bg-[url('data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iNDAiIGhlaWdodD0iNDAiIHhtbG5zPSJodHRwOi8vd3d3LnczLm9yZy8yMDAwL3N2ZyI+PHBhdGggZD0iTTAgMGg0MHY0MEgwVjB6bTIwIDIwaDIwdjIwSDIwaC0yMHptMCAwaC0yMHYtMjBoMjB2MjB6IiBmaWxsPSJub25lIiBmaWxsLXJ1bGU9ImV2ZW5vZGQiIHN0cm9rZT0iI2YxZjVmOSIgc3Ryb2tlLXdpZHRoPSIxIi8+PC9zdmc+')] opacity-60"
            ></div>

            <div
                class="absolute -top-[10%] -left-[10%] w-[40%] h-[40%] rounded-full bg-gradient-to-br from-blue-100/80 to-blue-50/20 blur-[100px]"
            ></div>
            <div
                class="absolute top-[20%] -right-[10%] w-[35%] h-[40%] rounded-full bg-gradient-to-bl from-indigo-100/60 to-transparent blur-[120px]"
            ></div>
            <div
                class="absolute -bottom-[20%] left-[20%] w-[50%] h-[50%] rounded-full bg-gradient-to-tr from-sky-100/60 to-transparent blur-[100px]"
            ></div>
        </div>

        <div class="relative z-10 w-full max-w-[420px] mx-4 py-12">
            <div
                class="relative bg-white/80 backdrop-blur-2xl rounded-[2rem] p-10 sm:p-12 shadow-[0_8px_30px_rgb(0,0,0,0.04),0_30px_60px_rgb(0,0,0,0.02)] border border-white ring-1 ring-slate-900/5 transform transition-all duration-700 hover:shadow-[0_8px_30px_rgb(0,0,0,0.06),0_30px_60px_rgb(0,0,0,0.04)]"
            >
                <div
                    class="absolute inset-0 rounded-[2rem] border border-white/60 pointer-events-none"
                ></div>

                <div class="relative">
                    <div class="text-center mb-6">
                        <div class="relative inline-flex mb-3">
                            <div
                                class="absolute inset-0 bg-blue-100 rounded-full blur-2xl transform scale-150"
                            ></div>

                            <div
                                class="relative w-36 h-36 bg-white rounded-full p-0.5 flex items-center justify-center ring-[10px] ring-blue-50 shadow-sm border border-slate-100"
                            >
                                <img
                                    src="https://stis.ac.id/media/source/up.png"
                                    alt="Logo Politeknik Statistika STIS"
                                    class="w-full h-full object-contain"
                                    loading="lazy"
                                />
                            </div>
                        </div>

                        <h2
                            class="text-[28px] font-extrabold tracking-tight mb-2.5 bg-clip-text text-transparent bg-gradient-to-r from-slate-900 via-slate-800 to-slate-600"
                        >
                            Sistem Rekrutmen
                        </h2>
                        <p class="text-sm font-semibold text-slate-500 leading-relaxed">
                            Organisasi Mahasiswa<br />
                            <span class="text-blue-600 font-bold tracking-wide"
                                >POLITEKNIK STATISTIKA STIS</span
                            >
                        </p>
                    </div>

                    <x-auth-session-status
                        class="mb-8 font-medium text-center"
                        :status="session('status')"
                    />

                    @if (session('error'))
                        <div
                            class="mb-8 flex items-start gap-3 p-4 bg-red-50/80 backdrop-blur-sm border border-red-100/80 rounded-2xl"
                        >
                            <svg class="w-5 h-5 text-red-500 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                            <div>
                                <p class="text-sm font-bold text-red-800 mb-0.5">Login Gagal</p>
                                <p class="text-xs font-medium text-red-700/90 leading-relaxed">
                                    {{
                                        session(
                                            'error',
                                        )
                                    }}
                                </p>
                            </div>
                        </div>
                    @endif

                    <div class="space-y-6">
                        <a
                            href="{{ route('google.login') }}"
                            class="group relative w-full flex items-center justify-center gap-3.5 px-6 py-3.5 bg-white rounded-2xl text-sm font-bold text-slate-700 transition-all duration-300 ring-1 ring-slate-900/10 shadow-[0_2px_4px_rgb(0,0,0,0.02)] hover:shadow-[0_4px_12px_rgb(0,0,0,0.05)] hover:ring-slate-900/20 active:scale-[0.98] focus:outline-none focus:ring-2 focus:ring-blue-500/50 overflow-hidden"
                        >
                            <div
                                class="absolute inset-0 bg-gradient-to-r from-transparent via-slate-50/50 to-transparent translate-x-[-100%] group-hover:translate-x-[100%] transition-transform duration-700 ease-in-out"
                            ></div>

                            <img
                                src="https://upload.wikimedia.org/wikipedia/commons/c/c1/Google_%22G%22_logo.svg"
                                alt="Google Logo"
                                class="w-[18px] h-[18px] transition-transform duration-500 group-hover:scale-110"
                            />
                            <span class="relative z-10"
                                >Masuk dengan Akun Google</span
                            >
                        </a>
                    </div>

                    <div class="mt-2 text-center">
                        <div
                            class="inline-flex items-center justify-center gap-2 text-xs font-semibold text-slate-500 bg-slate-50/80 px-4 py-2 rounded-full"
                        >
                            <span class="relative flex h-2 w-2">
                                <span
                                    class="animate-ping absolute inline-flex h-full w-full rounded-full bg-blue-400 opacity-75"
                                ></span>
                                <span
                                    class="relative inline-flex rounded-full h-2 w-2 bg-blue-500"
                                ></span>
                            </span>
                            Khusus email
                            <span class="text-slate-700">
                                @stis
                                .ac.id</span
                            >
                        </div>
                    </div>
                </div>
            </div>

            <div
                class="text-center mt-8 opacity-70 hover:opacity-100 transition-opacity duration-300"
            >
                <p class="text-[11px] font-bold text-slate-500 tracking-widest uppercase">&copy; {{ date('Y') }} POLITEKNIK STATISTIKA STIS</p>
            </div>
        </div>
    </div>
</x-guest-layout>
