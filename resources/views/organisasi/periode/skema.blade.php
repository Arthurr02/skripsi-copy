<x-app-layout>
    <div class="p-8">
        <h2 class="text-2xl font-bold mb-4">
            Pengaturan Skema - Periode {{ $periode->tahun_periode }}
        </h2>

        <form
            action="{{ route('organisasi.periode.store_skema', $periode->id) }}"
            method="POST"
            class="space-y-4"
        >
            @csrf
            <div>
                <label class="block font-bold">Nama Tahapan 1</label>
                <input
                    type="text"
                    name="tahapan[0][nama]"
                    class="border p-2 w-full max-w-md"
                    placeholder="Contoh: Seleksi Berkas"
                />
            </div>

            <div>
                <label class="block font-bold">Penugasan Tahapan 1</label>
                <textarea
                    name="penugasan[0][deskripsi]"
                    class="border p-2 w-full max-w-md"
                    placeholder="Contoh: Kumpulkan CV dan Motivation Letter"
                ></textarea>
            </div>

            <button
                type="submit"
                class="bg-green-600 text-white px-6 py-2 rounded font-semibold hover:bg-green-700 mt-4"
            >
                Publikasikan Rekrutmen
            </button>
        </form>
    </div>
</x-app-layout>
