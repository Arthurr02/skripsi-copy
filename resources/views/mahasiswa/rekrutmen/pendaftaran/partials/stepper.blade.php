<div class="mb-12 relative max-w-xs mx-auto">
    <!-- Garis Penghubung Background -->
    <div
        class="absolute top-1/2 left-0 w-full h-1 bg-slate-200 -translate-y-1/2 rounded-full hidden sm:block z-0"
    ></div>

    <!-- Garis Penghubung Progress (Biru) -->
    <div
        class="absolute top-1/2 left-0 h-1 bg-blue-600 -translate-y-1/2 rounded-full transition-all duration-500 hidden sm:block z-0"
        :style="tab === 1 ? 'width: 0%' : 'width: 100%'"
    ></div>

    <div
        class="relative flex flex-col sm:flex-row justify-between gap-4 sm:gap-0 z-10"
    >
        <!-- Tab 1 -->
        <button
            type="button"
            @click="
                tab = 1;
                window.scrollTo({ top: 0, behavior: 'smooth' });
            "
            class="relative flex items-center sm:flex-col sm:justify-center gap-3 sm:gap-2 group outline-none bg-slate-50 sm:bg-transparent rounded-lg sm:rounded-none p-3 sm:p-0"
        >
            <div
                :class="tab >= 1
                    ? 'bg-blue-600 text-white shadow-sm border-blue-600'
                    : 'bg-white text-slate-400 border-slate-300 group-hover:border-blue-300'"
                class="w-10 h-10 sm:w-12 sm:h-12 rounded-full border-2 flex items-center justify-center font-black text-sm sm:text-base transition-all duration-300 z-10 shrink-0"
            >
                <span x-show="tab === 1">1</span>
                <svg x-show="tab > 1" class="w-5 h-5 sm:w-6 sm:h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path></svg>
            </div>
            <span
                :class="tab === 1
                    ? 'text-blue-700 font-black'
                    : tab > 1
                      ? 'text-slate-800 font-bold'
                      : 'text-slate-500 font-bold'"
                class="text-xs sm:text-[11px] uppercase tracking-widest sm:absolute sm:-bottom-8 sm:whitespace-nowrap transition-colors"
            >
                Pilih Formasi
            </span>
        </button>

        <!-- Tab 2 -->
        <button
            type="button"
            @click="lanjutKeTugas()"
            class="relative flex items-center sm:flex-col sm:justify-center gap-3 sm:gap-2 group outline-none bg-slate-50 sm:bg-transparent rounded-lg sm:rounded-none p-3 sm:p-0"
        >
            <div
                :class="tab === 2
                    ? 'bg-blue-600 text-white shadow-sm border-blue-600'
                    : 'bg-white text-slate-400 border-slate-300 group-hover:border-blue-300'"
                class="w-10 h-10 sm:w-12 sm:h-12 rounded-full border-2 flex items-center justify-center font-black text-sm sm:text-base transition-all duration-300 z-10 shrink-0"
            >
                <span>2</span>
            </div>
            <span
                :class="tab === 2
                    ? 'text-blue-700 font-black'
                    : 'text-slate-500 font-bold'"
                class="text-xs sm:text-[11px] uppercase tracking-widest sm:absolute sm:-bottom-8 sm:whitespace-nowrap transition-colors"
            >
                Lembar Tugas
            </span>
        </button>
    </div>
</div>
