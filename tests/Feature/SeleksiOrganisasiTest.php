<?php

namespace Tests\Feature;

use App\Models\Jabatan;
use App\Models\Mahasiswa;
use App\Models\Organisasi;
use App\Models\Pendaftaran;
use App\Models\PengumpulanTugas;
use App\Models\PeriodeRekrutmen;
use App\Models\Tahapan;
use App\Models\Tugas;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use ZipArchive;

class SeleksiOrganisasiTest extends TestCase
{
    use RefreshDatabase;

    public function test_organisasi_can_open_tahapan_with_update_and_selection_actions(): void
    {
        $organisasi = $this->buatOrganisasi();
        $periode = $this->buatPeriode($organisasi);
        $tahapan = $this->buatTahapan($periode);

        $response = $this->actingAs($organisasi, 'organisasi')
            ->get(route('organisasi.rekrutmen.seleksi'));

        $response
            ->assertOk()
            ->assertSee('Jadwal Tahapan Seleksi')
            ->assertSee($tahapan->nama_tahapan)
            ->assertSee('Update Tahapan')
            ->assertSee('Lakukan Seleksi')
            ->assertSee('tahapan_id='.$tahapan->id, false);
    }

    public function test_selection_table_shows_submission_and_missing_submission_statuses(): void
    {
        $organisasi = $this->buatOrganisasi();
        $periode = $this->buatPeriode($organisasi);
        $tahapan = $this->buatTahapan($periode);
        $jabatan = Jabatan::create([
            'periode_rekrutmen_id' => $periode->id,
            'nama_posisi' => 'Pengurus Harian',
            'nama_jabatan' => 'Sekretaris',
        ]);
        $tugas = Tugas::create([
            'tahapan_id' => $tahapan->id,
            'jabatan_id' => $jabatan->id,
            'tipe_tugas' => 'penugasan',
            'tipe_jawaban_tugas' => 'berkas',
            'deskripsi_tugas' => 'Kirim portofolio',
        ]);
        $mahasiswaPengumpul = $this->buatMahasiswa('222222221', 'Pengumpul Tugas');
        $mahasiswaTidakMengumpulkan = $this->buatMahasiswa('222222222', 'Belum Mengumpulkan');
        $pendaftaranPengumpul = Pendaftaran::create([
            'nim' => $mahasiswaPengumpul->nim,
            'jabatan_1_id' => $jabatan->id,
        ]);
        Pendaftaran::create([
            'nim' => $mahasiswaTidakMengumpulkan->nim,
            'jabatan_1_id' => $jabatan->id,
        ]);
        PengumpulanTugas::create([
            'tugas_id' => $tugas->id,
            'pendaftaran_id' => $pendaftaranPengumpul->id,
            'lampiran_jawaban' => ['berkas' => ['rekrutmen/tugas/portofolio.pdf']],
        ]);

        $response = $this->actingAs($organisasi, 'organisasi')
            ->get(route('organisasi.rekrutmen.seleksi.jawaban', [
                'tahapanId' => $tahapan->id,
                'jabatanId' => $jabatan->id,
            ]));

        $response
            ->assertOk()
            ->assertSee('Pengumpul Tugas')
            ->assertSee('Belum Mengumpulkan')
            ->assertSee('Tidak mengumpulkan')
            ->assertSee('Waktu Pengumpulan')
            ->assertSee('Buka berkas 1')
            ->assertSee('Luluskan')
            ->assertSee('Tidak Lolos');
    }

    public function test_organisasi_can_save_a_selection_decision_for_a_participant(): void
    {
        $organisasi = $this->buatOrganisasi();
        $periode = $this->buatPeriode($organisasi);
        $tahapan = $this->buatTahapan($periode);
        $jabatan = $this->buatJabatan($periode);
        $mahasiswa = $this->buatMahasiswa('222222223', 'Peserta Lolos');
        $pendaftaran = Pendaftaran::create([
            'nim' => $mahasiswa->nim,
            'jabatan_1_id' => $jabatan->id,
        ]);

        $response = $this->actingAs($organisasi, 'organisasi')
            ->post(route('organisasi.rekrutmen.seleksi.keputusan', [
                'tahapanId' => $tahapan->id,
                'jabatanId' => $jabatan->id,
                'pendaftaranId' => $pendaftaran->id,
            ]), ['keputusan' => 'lulus']);

        $response->assertRedirect();
        $this->assertSame('Lulus Tahap 1', $pendaftaran->fresh()->status_seleksi);
    }

    public function test_form_answers_can_be_exported_to_an_excel_workbook_with_question_columns(): void
    {
        $organisasi = $this->buatOrganisasi();
        $periode = $this->buatPeriode($organisasi);
        $tahapan = $this->buatTahapan($periode);
        $jabatan = $this->buatJabatan($periode);
        $tugas = Tugas::create([
            'tahapan_id' => $tahapan->id,
            'jabatan_id' => $jabatan->id,
            'tipe_tugas' => 'pengisian_form',
            'tipe_jawaban_tugas' => 'form',
            'lampiran_tugas' => [
                'form' => [
                    ['id' => 'pertanyaan_nama', 'name' => 'isian_nama', 'tipe' => 'text_long', 'label' => 'Nama panggilan', 'required' => true],
                    ['id' => 'pertanyaan_kelas', 'name' => 'isian_kelas', 'tipe' => 'text_long', 'label' => 'Kelas', 'required' => true],
                ],
            ],
        ]);
        $mahasiswa = $this->buatMahasiswa('222222224', 'Peserta Form');
        $pendaftaran = Pendaftaran::create([
            'nim' => $mahasiswa->nim,
            'jabatan_1_id' => $jabatan->id,
        ]);
        PengumpulanTugas::create([
            'tugas_id' => $tugas->id,
            'pendaftaran_id' => $pendaftaran->id,
            'lampiran_jawaban' => [
                'form' => ['pertanyaan_nama' => 'Peta', 'pertanyaan_kelas' => '4SI'],
            ],
        ]);

        $response = $this->actingAs($organisasi, 'organisasi')
            ->get(route('organisasi.rekrutmen.seleksi.export', [
                'tahapanId' => $tahapan->id,
                'jabatanId' => $jabatan->id,
                'tugasId' => $tugas->id,
            ]));

        $response
            ->assertOk()
            ->assertHeader('content-type', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        $this->assertStringStartsWith('PK', $response->getContent());

        $path = tempnam(sys_get_temp_dir(), 'seleksi-export-');
        file_put_contents($path, $response->getContent());
        $zip = new ZipArchive;

        try {
            $this->assertTrue($zip->open($path) === true);
            $worksheet = $zip->getFromName('xl/worksheets/sheet1.xml');
            $this->assertStringContainsString('Nama panggilan', $worksheet);
            $this->assertStringContainsString('Kelas', $worksheet);
            $this->assertStringContainsString('Peta', $worksheet);
        } finally {
            $zip->close();
            @unlink($path);
        }
    }

    public function test_organisasi_can_complete_and_store_an_interview_form(): void
    {
        $organisasi = $this->buatOrganisasi();
        $periode = $this->buatPeriode($organisasi);
        $tahapan = $this->buatTahapan($periode);
        $jabatan = $this->buatJabatan($periode);
        $tugas = Tugas::create([
            'tahapan_id' => $tahapan->id,
            'jabatan_id' => $jabatan->id,
            'tipe_tugas' => 'wawancara',
            'tipe_jawaban_tugas' => 'wawancara',
            'lampiran_tugas' => [
                'form' => [
                    ['id' => 'pertanyaan_evaluasi', 'name' => 'isian_evaluasi', 'tipe' => 'text_long', 'label' => 'Catatan evaluasi', 'required' => true],
                ],
            ],
        ]);
        $mahasiswa = $this->buatMahasiswa('222222225', 'Peserta Wawancara');
        $pendaftaran = Pendaftaran::create([
            'nim' => $mahasiswa->nim,
            'jabatan_1_id' => $jabatan->id,
        ]);
        $parameter = [
            'tahapanId' => $tahapan->id,
            'jabatanId' => $jabatan->id,
            'tugasId' => $tugas->id,
            'pendaftaranId' => $pendaftaran->id,
        ];

        $this->actingAs($organisasi, 'organisasi')
            ->get(route('organisasi.rekrutmen.seleksi.wawancara', $parameter))
            ->assertOk()
            ->assertSee('Catatan evaluasi');

        $this->actingAs($organisasi, 'organisasi')
            ->post(route('organisasi.rekrutmen.seleksi.wawancara.store', $parameter), [
                'jawaban' => ['pertanyaan_evaluasi' => 'Komunikasi peserta sangat baik.'],
            ])
            ->assertRedirect(route('organisasi.rekrutmen.seleksi.jawaban', [
                'tahapanId' => $tahapan->id,
                'jabatanId' => $jabatan->id,
            ]));

        $pengumpulan = PengumpulanTugas::query()
            ->where('tugas_id', $tugas->id)
            ->where('pendaftaran_id', $pendaftaran->id)
            ->firstOrFail();
        $this->assertSame(
            'Komunikasi peserta sangat baik.',
            $pengumpulan->lampiran_jawaban['jawaban_wawancara']['form']['pertanyaan_evaluasi'],
        );
    }

    private function buatOrganisasi(): Organisasi
    {
        return Organisasi::create([
            'email_kampus' => fake()->unique()->safeEmail(),
            'nama_organisasi' => 'Badan Eksekutif Mahasiswa',
            'lampiran_logo' => 'organisasi/logo.png',
        ]);
    }

    private function buatPeriode(Organisasi $organisasi): PeriodeRekrutmen
    {
        return PeriodeRekrutmen::create([
            'organisasi_id' => $organisasi->id,
            'tahun_periode' => '2026/2027',
            'status_aktif' => 1,
        ]);
    }

    private function buatTahapan(PeriodeRekrutmen $periode): Tahapan
    {
        return Tahapan::create([
            'periode_rekrutmen_id' => $periode->id,
            'nama_tahapan' => 'Seleksi Administrasi',
            'deskripsi_tahapan' => 'Pemeriksaan kelengkapan dokumen.',
            'waktu_mulai' => now()->subDay(),
            'waktu_berakhir' => now()->addDay(),
            'urutan_tahapan' => 1,
        ]);
    }

    private function buatJabatan(PeriodeRekrutmen $periode): Jabatan
    {
        return Jabatan::create([
            'periode_rekrutmen_id' => $periode->id,
            'nama_posisi' => 'Pengurus Harian',
            'nama_jabatan' => 'Sekretaris',
        ]);
    }

    private function buatMahasiswa(string $nim, string $nama): Mahasiswa
    {
        return Mahasiswa::create([
            'nim' => $nim,
            'email_kampus' => $nim.'@stis.ac.id',
            'nama_lengkap' => $nama,
        ]);
    }
}
