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
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class MahasiswaRiwayatRekrutmenTest extends TestCase
{
    use RefreshDatabase;

    public function test_future_stages_do_not_render_announcement_or_task_details(): void
    {
        [$mahasiswa, $periode, $pendaftaran] = $this->buatPendaftaran(statusAktif: 1);

        Tahapan::create([
            'periode_rekrutmen_id' => $periode->id,
            'jenis_tahapan' => 'pengumuman',
            'nama_tahapan' => 'Pengumuman Mendatang',
            'deskripsi_tahapan' => 'RAHASIA PENGUMUMAN',
            'lampiran_tahapan' => ['rahasia-pengumuman.pdf'],
            'waktu_mulai' => now()->addDay(),
            'waktu_berakhir' => now()->addDay(),
            'urutan_tahapan' => 1,
        ]);
        $tahapanTugas = Tahapan::create([
            'periode_rekrutmen_id' => $periode->id,
            'jenis_tahapan' => 'seleksi',
            'nama_tahapan' => 'Penugasan Mendatang',
            'deskripsi_tahapan' => 'RAHASIA TAHAPAN',
            'waktu_mulai' => now()->addDays(2),
            'waktu_berakhir' => now()->addDays(3),
            'urutan_tahapan' => 2,
        ]);
        $tugas = Tugas::create([
            'tahapan_id' => $tahapanTugas->id,
            'jabatan_id' => $pendaftaran->jabatan_1_id,
            'tipe_tugas' => 'penugasan',
            'tipe_jawaban_tugas' => 'berkas',
            'deskripsi_tugas' => 'RAHASIA INSTRUKSI TUGAS',
            'lampiran_tugas' => ['rahasia-tugas.pdf'],
        ]);

        $this->actingAs($mahasiswa)
            ->get(route('mahasiswa.rekrutmen.diikuti.tahapan', $pendaftaran->id))
            ->assertOk()
            ->assertSee('Pengumuman Mendatang')
            ->assertSee('Penugasan Mendatang')
            ->assertDontSee('RAHASIA PENGUMUMAN')
            ->assertDontSee('rahasia-pengumuman.pdf')
            ->assertDontSee('RAHASIA TAHAPAN')
            ->assertDontSee('RAHASIA INSTRUKSI TUGAS')
            ->assertDontSee('rahasia-tugas.pdf');

        $this->actingAs($mahasiswa)
            ->get(route('mahasiswa.rekrutmen.diikuti.tugas_detail', [$pendaftaran->id, $tugas->id]))
            ->assertRedirect(route('mahasiswa.rekrutmen.diikuti.tahapan', $pendaftaran->id))
            ->assertSessionHas('error', 'Informasi penugasan belum tersedia karena tahapan belum dimulai.');
    }

    public function test_history_is_split_between_joined_and_unjoined_closed_recruitments(): void
    {
        [$mahasiswa, $periodeDiikuti] = $this->buatPendaftaran(statusAktif: 0, namaOrganisasi: 'Organisasi Diikuti');
        $periodeTidakDiikuti = $this->buatPeriode('Organisasi Tidak Diikuti', 0);
        $periodeAktif = $this->buatPeriode('Organisasi Masih Aktif', 1);

        $this->actingAs($mahasiswa)
            ->get(route('mahasiswa.riwayat.index'))
            ->assertOk()
            ->assertSee('Rekrutmen yang Pernah Diikuti')
            ->assertSee('Rekrutmen yang Tidak Diikuti')
            ->assertSee($periodeDiikuti->organisasi->nama_organisasi)
            ->assertSee($periodeTidakDiikuti->organisasi->nama_organisasi)
            ->assertDontSee($periodeAktif->organisasi->nama_organisasi)
            ->assertSee('Lihat Penugasan Terkirim')
            ->assertSee('Lihat Tahapan yang Telah Berlangsung');
    }

    public function test_first_selection_deadline_locks_registration_in_the_ui_and_on_the_server(): void
    {
        [, $periode, $pendaftaran] = $this->buatPendaftaran(statusAktif: 2);
        $mahasiswaLain = Mahasiswa::create([
            'nim' => '222222222',
            'email_kampus' => '222222222@stis.ac.id',
            'nama_lengkap' => 'Mahasiswa Kedua',
        ]);
        Tahapan::create([
            'periode_rekrutmen_id' => $periode->id,
            'jenis_tahapan' => 'seleksi',
            'nama_tahapan' => 'Pendaftaran',
            'waktu_mulai' => now()->subDays(2),
            'waktu_berakhir' => now()->subMinute(),
            'urutan_tahapan' => 1,
        ]);

        $this->actingAs($mahasiswaLain)
            ->get(route('mahasiswa.rekrutmen.index'))
            ->assertOk()
            ->assertSee('Pendaftaran Ditutup');

        $this->actingAs($mahasiswaLain)
            ->get(route('mahasiswa.rekrutmen.info', $periode->id))
            ->assertOk()
            ->assertSee('Pendaftaran Ditutup');

        $this->actingAs($mahasiswaLain)
            ->get(route('mahasiswa.rekrutmen.daftar', $periode->id))
            ->assertRedirect(route('mahasiswa.rekrutmen.index'))
            ->assertSessionHas('error_server', 'Pendaftaran telah ditutup karena batas waktu tahapan pertama sudah berakhir.');

        $this->actingAs($mahasiswaLain)
            ->post(route('mahasiswa.rekrutmen.submit', $periode->id), [
                'jabatan_1_id' => $pendaftaran->jabatan_1_id,
            ])
            ->assertRedirect(route('mahasiswa.rekrutmen.index'))
            ->assertSessionHas('error_server', 'Pendaftaran telah ditutup karena batas waktu tahapan pertama sudah berakhir.');
    }

    public function test_registration_saves_file_answers_from_dynamic_form_fields(): void
    {
        Storage::fake('public');
        [, $periode, $pendaftaran] = $this->buatPendaftaran(statusAktif: 2);
        $mahasiswaLain = Mahasiswa::create([
            'nim' => '222222223',
            'email_kampus' => '222222223@stis.ac.id',
            'nama_lengkap' => 'Mahasiswa Pengunggah',
        ]);
        $tahapan = Tahapan::create([
            'periode_rekrutmen_id' => $periode->id,
            'jenis_tahapan' => 'seleksi',
            'nama_tahapan' => 'Pendaftaran',
            'waktu_mulai' => now()->subHour(),
            'waktu_berakhir' => now()->addHour(),
            'urutan_tahapan' => 1,
        ]);
        $tugas = Tugas::create([
            'tahapan_id' => $tahapan->id,
            'jabatan_id' => $pendaftaran->jabatan_1_id,
            'tipe_tugas' => 'pengisian_form',
            'tipe_jawaban_tugas' => 'form',
            'lampiran_tugas' => [
                'form' => [
                    [
                        'tipe' => 'text_short',
                        'label' => 'Nama lengkap',
                        'required' => true,
                    ],
                    [
                        'tipe' => 'checkbox',
                        'label' => 'Pilihan',
                        'required' => true,
                    ],
                    [
                        'tipe' => 'file',
                        'label' => 'CV',
                        'required' => true,
                        'allowed_formats' => ['pdf'],
                    ],
                ],
            ],
        ]);

        $this->actingAs($mahasiswaLain)
            ->post(route('mahasiswa.rekrutmen.submit', $periode->id), [
                'jabatan_1_id' => $pendaftaran->jabatan_1_id,
                'dynamic_answers' => [
                    'isian_0' => 'Saya peserta dengan nama Fathur',
                    'isian_1' => ['g'],
                ],
                'dynamic_files' => [
                    'isian_2' => [UploadedFile::fake()->createWithContent('cv.pdf', $this->isiPdfValid())],
                ],
            ])
            ->assertRedirect(route('mahasiswa.rekrutmen.index'));

        $pendaftaranBaru = Pendaftaran::query()
            ->where('nim', $mahasiswaLain->nim)
            ->firstOrFail();
        $jawaban = PengumpulanTugas::query()
            ->where('pendaftaran_id', $pendaftaranBaru->id)
            ->where('tugas_id', $tugas->id)
            ->firstOrFail()
            ->lampiran_jawaban;

        $this->assertArrayHasKey('form', $jawaban);
        $this->assertSame('Saya peserta dengan nama Fathur', $jawaban['form']['isian_0']);
        $this->assertSame(['g'], $jawaban['form']['isian_1']);
        $this->assertCount(1, $jawaban['form']['isian_2']);
        $this->assertStringStartsWith('rekrutmen/jawaban-form/', $jawaban['form']['isian_2'][0]);
        Storage::disk('public')->assertExists($jawaban['form']['isian_2'][0]);
    }

    public function test_selection_task_submission_is_rejected_at_its_deadline(): void
    {
        [$mahasiswa, $periode, $pendaftaran] = $this->buatPendaftaran(statusAktif: 1);
        $tahapan = Tahapan::create([
            'periode_rekrutmen_id' => $periode->id,
            'jenis_tahapan' => 'seleksi',
            'nama_tahapan' => 'Penugasan Tertutup',
            'waktu_mulai' => now()->subDays(2),
            'waktu_berakhir' => now(),
            'urutan_tahapan' => 1,
        ]);
        $tugas = Tugas::create([
            'tahapan_id' => $tahapan->id,
            'jabatan_id' => $pendaftaran->jabatan_1_id,
            'tipe_tugas' => 'penugasan',
            'tipe_jawaban_tugas' => 'berkas',
        ]);

        $this->actingAs($mahasiswa)
            ->post(route('mahasiswa.rekrutmen.diikuti.tugas_submit', [$pendaftaran->id, $tugas->id]))
            ->assertRedirect()
            ->assertSessionHasErrors('tugas');

        $this->assertDatabaseMissing('pengumpulan_tugas', [
            'pendaftaran_id' => $pendaftaran->id,
            'tugas_id' => $tugas->id,
        ]);
    }

    public function test_history_task_uses_its_own_breadcrumb_and_is_only_visible_to_its_owner(): void
    {
        [$mahasiswa, $periode, $pendaftaran] = $this->buatPendaftaran(statusAktif: 0);
        $tahapan = Tahapan::create([
            'periode_rekrutmen_id' => $periode->id,
            'jenis_tahapan' => 'seleksi',
            'nama_tahapan' => 'Penugasan Arsip',
            'waktu_mulai' => now()->subDays(3),
            'waktu_berakhir' => now()->subDay(),
            'urutan_tahapan' => 1,
        ]);
        $tugas = Tugas::create([
            'tahapan_id' => $tahapan->id,
            'jabatan_id' => $pendaftaran->jabatan_1_id,
            'tipe_tugas' => 'pengisian_form',
            'tipe_jawaban_tugas' => 'form',
            'lampiran_tugas' => ['form' => []],
        ]);
        PengumpulanTugas::create([
            'pendaftaran_id' => $pendaftaran->id,
            'tugas_id' => $tugas->id,
            'lampiran_jawaban' => ['form' => ['isian_0' => 'Jawaban arsip']],
        ]);
        $mahasiswaLain = Mahasiswa::create([
            'nim' => '222222224',
            'email_kampus' => '222222224@stis.ac.id',
            'nama_lengkap' => 'Mahasiswa Lain',
        ]);

        $this->actingAs($mahasiswa)
            ->get(route('mahasiswa.riwayat.diikuti.tugas', [$pendaftaran->id, $tugas->id]))
            ->assertOk()
            ->assertSee('Riwayat Rekrutmen')
            ->assertSee('Tahapan Seleksi')
            ->assertSee('Detail Tugas');

        $this->actingAs($mahasiswa)
            ->get(route('mahasiswa.rekrutmen.diikuti.tugas_detail', [$pendaftaran->id, $tugas->id]))
            ->assertNotFound();

        $this->actingAs($mahasiswaLain)
            ->get(route('mahasiswa.riwayat.diikuti.tugas', [$pendaftaran->id, $tugas->id]))
            ->assertNotFound();
    }

    /** @return array{Mahasiswa, PeriodeRekrutmen, Pendaftaran} */
    private function buatPendaftaran(int $statusAktif, string $namaOrganisasi = 'Organisasi Contoh'): array
    {
        $mahasiswa = Mahasiswa::create([
            'nim' => '222222221',
            'email_kampus' => '222222221@stis.ac.id',
            'nama_lengkap' => 'Mahasiswa Contoh',
        ]);
        $periode = $this->buatPeriode($namaOrganisasi, $statusAktif);
        $jabatan = Jabatan::create([
            'periode_rekrutmen_id' => $periode->id,
            'nama_posisi' => 'Pengurus Harian',
            'nama_jabatan' => 'Sekretaris',
        ]);
        $pendaftaran = Pendaftaran::create([
            'nim' => $mahasiswa->nim,
            'jabatan_1_id' => $jabatan->id,
        ]);

        return [$mahasiswa, $periode, $pendaftaran];
    }

    private function buatPeriode(string $namaOrganisasi, int $statusAktif): PeriodeRekrutmen
    {
        $organisasi = Organisasi::create([
            'email_kampus' => str($namaOrganisasi)->slug().'@stis.ac.id',
            'nama_organisasi' => $namaOrganisasi,
            'lampiran_logo' => 'organisasi/logo.png',
        ]);

        return PeriodeRekrutmen::create([
            'organisasi_id' => $organisasi->id,
            'tahun_periode' => '2026/2027',
            'status_aktif' => $statusAktif,
        ]);
    }

    private function isiPdfValid(): string
    {
        return "%PDF-1.4\n1 0 obj\n<< /Type /Catalog >>\nendobj\ntrailer\n<< /Root 1 0 R >>\n%%EOF";
    }
}
