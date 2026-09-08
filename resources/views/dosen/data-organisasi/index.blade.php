<x-app-layout>
    <div class="mx-auto max-w-7xl px-4 py-8 sm:px-6 lg:px-8">
        <div class="mb-8 border-b border-slate-200 pb-5">
            <h1 class="text-2xl font-extrabold tracking-tight text-slate-800">Data Organisasi</h1>
            <p class="mt-1 text-sm font-medium text-slate-500">Daftar anggota yang dikirim organisasi untuk kebutuhan penilaian IPKM.</p>
        </div>

        <form method="GET" class="mb-5 flex flex-col gap-3 rounded-xl border border-slate-200 bg-white p-4 shadow-sm sm:flex-row sm:items-end">
            <div class="w-full sm:max-w-sm">
                <label for="organisasi" class="mb-1.5 block text-xs font-extrabold uppercase tracking-wide text-slate-500">Nama organisasi</label>
                <input id="organisasi" name="organisasi" value="{{ $organisasi }}" placeholder="Cari organisasi..." class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm font-medium focus:border-blue-500 focus:ring-blue-500" />
            </div>
            <input type="hidden" name="sort" value="{{ $sort }}" />
            <input type="hidden" name="direction" value="{{ $direction }}" />
            <button class="rounded-lg bg-blue-600 px-4 py-2 text-sm font-extrabold text-white hover:bg-blue-700">Filter</button>
            <a href="{{ route('dosen.data-organisasi.index') }}" class="px-2 py-2 text-sm font-bold text-slate-600 hover:text-slate-900">Reset</a>
        </form>

        <section class="overflow-hidden rounded-xl border border-slate-200 bg-white shadow-sm">
            <div class="overflow-x-auto">
                <table class="min-w-full text-left text-sm">
                    <thead class="bg-slate-50 text-xs font-extrabold uppercase tracking-wide text-slate-500">
                        <tr>
                            @foreach (['organisasi' => 'Organisasi', 'tanggal_mulai' => 'Awal periode', 'tanggal_akhir' => 'Akhir periode', 'waktu_kirim' => 'Waktu pengiriman'] as $column => $label)
                                <th class="px-5 py-3"><a href="{{ request()->fullUrlWithQuery(['sort' => $column, 'direction' => $sort === $column && $direction === 'asc' ? 'desc' : 'asc']) }}" class="inline-flex items-center gap-1 hover:text-blue-700">{{ $label }} @if ($sort === $column)<span>{{ $direction === 'asc' ? '↑' : '↓' }}</span>@endif</a></th>
                            @endforeach
                            <th class="px-5 py-3">File PDF</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 text-slate-700">
                        @forelse ($dataOrganisasi as $data)
                            <tr>
                                <td class="px-5 py-4 font-bold text-slate-800">{{ $data->organisasi->nama_organisasi }}</td>
                                <td class="px-5 py-4 font-medium">{{ $data->tanggal_mulai_periode->translatedFormat('d F Y') }}</td>
                                <td class="px-5 py-4 font-medium">{{ $data->tanggal_akhir_periode->translatedFormat('d F Y') }}</td>
                                <td class="px-5 py-4 font-medium">{{ $data->created_at->timezone(config('app.timezone'))->format('d/m/Y H:i') }} WIB</td>
                                <td class="px-5 py-4"><a href="{{ route('dosen.data-organisasi.download', $data) }}" class="font-bold text-blue-700 hover:text-blue-800 hover:underline">{{ $data->nama_file_asli }}</a></td>
                            </tr>
                        @empty
                            <tr><td colspan="5" class="px-5 py-10 text-center text-sm font-medium text-slate-500">Belum ada daftar anggota yang dikirim organisasi.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if ($dataOrganisasi->hasPages())<div class="border-t border-slate-100 px-5 py-4">{{ $dataOrganisasi->links() }}</div>@endif
        </section>
    </div>
</x-app-layout>
