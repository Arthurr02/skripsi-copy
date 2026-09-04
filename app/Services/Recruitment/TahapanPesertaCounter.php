<?php

namespace App\Services\Recruitment;

use App\Models\Jabatan;
use App\Models\KeputusanSeleksi;
use App\Models\Pendaftaran;
use App\Models\Tahapan;
use Illuminate\Support\Collection;

class TahapanPesertaCounter
{
    /**
     * Menambahkan jumlah peserta yang berhak mengikuti tiap tahapan.
     * Hanya Pilihan 1 yang diproses. Tahap setelah tahap pertama mensyaratkan
     * keputusan lulus pada tahapan langsung sebelumnya untuk jabatan yang sama.
     *
     * @param Collection<int, Tahapan> $tahapans
     * @return Collection<int, Tahapan>
     */
    public function tambahkanJumlahPeserta(Collection $tahapans, int $periodeId): Collection
    {
        $tahapans = $tahapans->values();
        $jabatanIds = Jabatan::query()
            ->where('periode_rekrutmen_id', $periodeId)
            ->pluck('id');

        $pendaftarans = $jabatanIds->isEmpty()
            ? collect()
            : Pendaftaran::query()
                ->select(['id', 'jabatan_1_id'])
                ->whereIn('jabatan_1_id', $jabatanIds)
                ->get();

        $keputusanLulus = $tahapans->isEmpty()
            ? collect()
            : KeputusanSeleksi::query()
                ->select(['pendaftaran_id', 'jabatan_id', 'tahapan_id'])
                ->where('keputusan', 'lulus')
                ->whereIn('tahapan_id', $tahapans->pluck('id'))
                ->get();

        return $tahapans->each(function (Tahapan $tahapan) use ($tahapans, $pendaftarans, $keputusanLulus, $jabatanIds) {
            $tahapanSebelumnya = $tahapans
                ->where('urutan_tahapan', '<', $tahapan->urutan_tahapan)
                ->sortByDesc('urutan_tahapan')
                ->first();

            $tahapan->peserta_per_jabatan = $jabatanIds
                ->mapWithKeys(function (int $jabatanId) use ($tahapanSebelumnya, $pendaftarans, $keputusanLulus) {
                    $pesertaJabatan = $pendaftarans
                        ->where('jabatan_1_id', $jabatanId);

                    if ($tahapanSebelumnya) {
                        $pesertaJabatan = $pesertaJabatan->filter(fn (Pendaftaran $pendaftaran) => $keputusanLulus->contains(
                            fn (KeputusanSeleksi $keputusan) =>
                                (int) $keputusan->pendaftaran_id === (int) $pendaftaran->id
                                && (int) $keputusan->jabatan_id === $jabatanId
                                && (int) $keputusan->tahapan_id === (int) $tahapanSebelumnya->id,
                        ));
                    }

                    return [$jabatanId => $pesertaJabatan->count()];
                })
                ->all();
            $tahapan->peserta_count = array_sum($tahapan->peserta_per_jabatan);
        });
    }
}
