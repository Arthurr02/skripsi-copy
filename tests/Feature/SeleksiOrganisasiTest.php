<?php

namespace Tests\Feature;

use App\Models\Jabatan;
use App\Models\KeputusanSeleksi;
use App\Models\Mahasiswa;
use App\Models\Organisasi;
use App\Models\Panitia;
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
        $this->assertDatabaseHas('keputusan_seleksi', [
            'tahapan_id' => $tahapan->id,
            'jabatan_id' => $jabatan->id,
            'pendaftaran_id' => $pendaftaran->id,
            'keputusan' => 'lulus',
            'aktor_tipe' => 'Organisasi',
            'aktor_nama' => $organisasi->nama_organisasi,
        ]);
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

        $this->actingAs($organisasi, 'organisasi')
            ->get(route('organisasi.rekrutmen.seleksi.jawaban', [
                'tahapanId' => $tahapan->id,
                'jabatanId' => $jabatan->id,
            ]))
            ->assertOk()
            ->assertSee('data-no-loading', false);

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
        $this->assertSame('Organisasi', $pengumpulan->pewawancara_tipe);
        $this->assertSame($organisasi->nama_organisasi, $pengumpulan->pewawancara_nama);
    }

    public function test_failed_participants_are_not_listed_in_later_selection_stages(): void
    {
        $organisasi = $this->buatOrganisasi();
        $periode = $this->buatPeriode($organisasi);
        $tahapanPertama = $this->buatTahapan($periode);
        $tahapanKedua = Tahapan::create([
            'periode_rekrutmen_id' => $periode->id,
            'nama_tahapan' => 'Wawancara Lanjutan',
            'waktu_mulai' => now()->subDay(),
            'waktu_berakhir' => now()->addDay(),
            'urutan_tahapan' => 2,
        ]);
        $jabatan = $this->buatJabatan($periode);
        Tugas::create([
            'tahapan_id' => $tahapanKedua->id,
            'jabatan_id' => $jabatan->id,
            'tipe_tugas' => 'penugasan',
            'tipe_jawaban_tugas' => 'berkas',
        ]);
        $mahasiswaGagal = $this->buatMahasiswa('222222226', 'Peserta Tidak Lolos');
        $mahasiswaLanjut = $this->buatMahasiswa('222222227', 'Peserta Lanjut');
        $mahasiswaBelumDiputuskan = $this->buatMahasiswa('222222235', 'Peserta Belum Diputuskan');
        $pendaftaranGagal = Pendaftaran::create([
            'nim' => $mahasiswaGagal->nim,
            'jabatan_1_id' => $jabatan->id,
        ]);
        $pendaftaranLanjut = Pendaftaran::create([
            'nim' => $mahasiswaLanjut->nim,
            'jabatan_1_id' => $jabatan->id,
        ]);
        Pendaftaran::create([
            'nim' => $mahasiswaBelumDiputuskan->nim,
            'jabatan_1_id' => $jabatan->id,
        ]);
        KeputusanSeleksi::create([
            'tahapan_id' => $tahapanPertama->id,
            'jabatan_id' => $jabatan->id,
            'pendaftaran_id' => $pendaftaranGagal->id,
            'keputusan' => 'tidak_lolos',
            'aktor_tipe' => 'Organisasi',
            'aktor_nama' => $organisasi->nama_organisasi,
            'diputuskan_pada' => now(),
        ]);
        KeputusanSeleksi::create([
            'tahapan_id' => $tahapanPertama->id,
            'jabatan_id' => $jabatan->id,
            'pendaftaran_id' => $pendaftaranLanjut->id,
            'keputusan' => 'lulus',
            'aktor_tipe' => 'Organisasi',
            'aktor_nama' => $organisasi->nama_organisasi,
            'diputuskan_pada' => now(),
        ]);

        $this->actingAs($organisasi, 'organisasi')
            ->get(route('organisasi.rekrutmen.seleksi.jawaban', [
                'tahapanId' => $tahapanKedua->id,
                'jabatanId' => $jabatan->id,
            ]))
            ->assertOk()
            ->assertSee('Peserta Lanjut')
            ->assertDontSee('Peserta Tidak Lolos')
            ->assertDontSee('Peserta Belum Diputuskan');
    }

    public function test_student_is_locked_after_a_failed_stage_and_cannot_open_later_task(): void
    {
        $organisasi = $this->buatOrganisasi();
        $periode = $this->buatPeriode($organisasi);
        $tahapanPertama = $this->buatTahapan($periode);
        $tahapanKedua = Tahapan::create([
            'periode_rekrutmen_id' => $periode->id,
            'nama_tahapan' => 'Tahap Lanjutan',
            'waktu_mulai' => now()->subDay(),
            'waktu_berakhir' => now()->addDay(),
            'urutan_tahapan' => 2,
        ]);
        $jabatan = $this->buatJabatan($periode);
        $tugasKedua = Tugas::create([
            'tahapan_id' => $tahapanKedua->id,
            'jabatan_id' => $jabatan->id,
            'tipe_tugas' => 'pengisian_form',
            'tipe_jawaban_tugas' => 'form',
            'lampiran_tugas' => ['form' => []],
        ]);
        $mahasiswa = $this->buatMahasiswa('222222228', 'Peserta Dikunci');
        $pendaftaran = Pendaftaran::create([
            'nim' => $mahasiswa->nim,
            'jabatan_1_id' => $jabatan->id,
        ]);
        KeputusanSeleksi::create([
            'tahapan_id' => $tahapanPertama->id,
            'jabatan_id' => $jabatan->id,
            'pendaftaran_id' => $pendaftaran->id,
            'keputusan' => 'tidak_lolos',
            'aktor_tipe' => 'Organisasi',
            'aktor_nama' => $organisasi->nama_organisasi,
            'diputuskan_pada' => now(),
        ]);

        $this->actingAs($mahasiswa)
            ->get(route('mahasiswa.rekrutmen.diikuti.tahapan', ['id' => $pendaftaran->id]))
            ->assertOk()
            ->assertSee('Anda tidak dinyatakan lulus.')
            ->assertSee('Tahapan terkunci');

        $this->actingAs($mahasiswa)
            ->get(route('mahasiswa.rekrutmen.diikuti.tugas_detail', [
                'pendaftaran' => $pendaftaran->id,
                'tugas' => $tugasKedua->id,
            ]))
            ->assertForbidden();
    }

    public function test_closed_recruitment_history_is_read_only(): void
    {
        $organisasi = $this->buatOrganisasi();
        $periode = $this->buatPeriode($organisasi);
        $periode->update(['status_aktif' => 0]);
        $tahapan = $this->buatTahapan($periode);

        $this->actingAs($organisasi, 'organisasi')
            ->get(route('organisasi.riwayat.index'))
            ->assertOk()
            ->assertSee($periode->tahun_periode);

        $this->actingAs($organisasi, 'organisasi')
            ->get(route('organisasi.riwayat.periode', ['periode_id' => $periode->id]))
            ->assertOk()
            ->assertSee('Riwayat Tahapan Seleksi')
            ->assertSee('Lihat Riwayat Seleksi')
            ->assertDontSee('Update Tahapan')
            ->assertDontSee('Lakukan Seleksi');

        $jabatan = $this->buatJabatan($periode);
        Tugas::create([
            'tahapan_id' => $tahapan->id,
            'jabatan_id' => $jabatan->id,
            'tipe_tugas' => 'penugasan',
            'tipe_jawaban_tugas' => 'berkas',
        ]);

        $this->actingAs($organisasi, 'organisasi')
            ->get(route('organisasi.riwayat.tahapan', [
                'periode_id' => $periode->id,
                'jabatan_id' => $jabatan->id,
                'tahapan_id' => $tahapan->id,
            ]))
            ->assertOk()
            ->assertSee('Riwayat Seleksi Peserta')
            ->assertDontSee('Luluskan')
            ->assertDontSee('Tidak Lolos');
    }

    public function test_panitia_name_is_recorded_for_selection_and_interview_submission(): void
    {
        $organisasi = $this->buatOrganisasi();
        $periode = $this->buatPeriode($organisasi);
        $tahapan = $this->buatTahapan($periode);
        $jabatan = $this->buatJabatan($periode);
        $peserta = $this->buatMahasiswa('222222229', 'Peserta Panitia');
        $pendaftaran = Pendaftaran::create([
            'nim' => $peserta->nim,
            'jabatan_1_id' => $jabatan->id,
        ]);
        $panitiaMahasiswa = $this->buatMahasiswa('222222230', 'Panitia Penguji');
        $panitia = Panitia::create([
            'periode_rekrutmen_id' => $periode->id,
            'nim' => $panitiaMahasiswa->nim,
        ]);
        $tugas = Tugas::create([
            'tahapan_id' => $tahapan->id,
            'jabatan_id' => $jabatan->id,
            'tipe_tugas' => 'wawancara',
            'tipe_jawaban_tugas' => 'wawancara',
            'lampiran_tugas' => [
                'form' => [
                    ['id' => 'catatan', 'tipe' => 'text_long', 'label' => 'Catatan', 'required' => true],
                ],
            ],
        ]);
        $parameterSeleksi = [
            'tahapanId' => $tahapan->id,
            'jabatanId' => $jabatan->id,
            'pendaftaranId' => $pendaftaran->id,
        ];

        $this->actingAs($panitiaMahasiswa)
            ->post(route('panitia.rekrutmen.seleksi.keputusan', $parameterSeleksi), ['keputusan' => 'lulus'])
            ->assertRedirect();

        $this->assertDatabaseHas('keputusan_seleksi', [
            'tahapan_id' => $tahapan->id,
            'jabatan_id' => $jabatan->id,
            'pendaftaran_id' => $pendaftaran->id,
            'aktor_tipe' => 'Panitia',
            'aktor_akun_id' => $panitia->id,
            'aktor_nama' => $panitiaMahasiswa->nama_lengkap,
        ]);

        $this->actingAs($panitiaMahasiswa)
            ->post(route('panitia.rekrutmen.seleksi.wawancara.store', [
                ...$parameterSeleksi,
                'tugasId' => $tugas->id,
            ]), ['jawaban' => ['catatan' => 'Wawancara telah dilakukan.']])
            ->assertRedirect();

        $this->assertDatabaseHas('pengumpulan_tugas', [
            'tugas_id' => $tugas->id,
            'pendaftaran_id' => $pendaftaran->id,
            'pewawancara_id' => $panitia->id,
            'pewawancara_tipe' => 'Panitia',
            'pewawancara_akun_id' => $panitia->id,
            'pewawancara_nama' => $panitiaMahasiswa->nama_lengkap,
        ]);
    }

    public function test_closed_history_form_answers_can_be_exported_to_excel(): void
    {
        $organisasi = $this->buatOrganisasi();
        $periode = $this->buatPeriode($organisasi);
        $periode->update(['status_aktif' => 0]);
        $tahapan = $this->buatTahapan($periode);
        $jabatan = $this->buatJabatan($periode);
        $tugas = Tugas::create([
            'tahapan_id' => $tahapan->id,
            'jabatan_id' => $jabatan->id,
            'tipe_tugas' => 'pengisian_form',
            'tipe_jawaban_tugas' => 'form',
            'lampiran_tugas' => [
                'form' => [
                    ['id' => 'motivasi', 'tipe' => 'text_long', 'label' => 'Motivasi', 'required' => true],
                ],
            ],
        ]);
        $mahasiswa = $this->buatMahasiswa('222222231', 'Peserta Arsip');
        $pendaftaran = Pendaftaran::create([
            'nim' => $mahasiswa->nim,
            'jabatan_1_id' => $jabatan->id,
        ]);

        $this->actingAs($organisasi, 'organisasi')
            ->get(route('organisasi.riwayat.periode', ['periode_id' => $periode->id]))
            ->assertOk()
            ->assertSee('1 peserta');

        PengumpulanTugas::create([
            'tugas_id' => $tugas->id,
            'pendaftaran_id' => $pendaftaran->id,
            'lampiran_jawaban' => ['form' => ['motivasi' => 'Belajar berorganisasi.']],
        ]);

        $this->actingAs($organisasi, 'organisasi')
            ->get(route('organisasi.riwayat.tahapan', [
                'periode_id' => $periode->id,
                'jabatan_id' => $jabatan->id,
                'tahapan_id' => $tahapan->id,
            ]))
            ->assertOk()
            ->assertSee('Export Excel');

        $response = $this->actingAs($organisasi, 'organisasi')
            ->get(route('organisasi.riwayat.export', [
                'periode_id' => $periode->id,
                'jabatan_id' => $jabatan->id,
                'tahapan_id' => $tahapan->id,
                'tugasId' => $tugas->id,
            ]));

        $response
            ->assertOk()
            ->assertHeader('content-type', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        $this->assertStringStartsWith('PK', $response->getContent());
    }

    public function test_organisasi_can_manage_panitia_for_the_active_recruitment(): void
    {
        $organisasi = $this->buatOrganisasi();
        $periode = $this->buatPeriode($organisasi);
        $mahasiswa = $this->buatMahasiswa('222222232', 'Panitia Baru');

        $this->actingAs($organisasi, 'organisasi')
            ->get(route('organisasi.rekrutmen.panitia'))
            ->assertOk()
            ->assertSee('Daftar Panitia');

        $this->actingAs($organisasi, 'organisasi')
            ->post(route('organisasi.rekrutmen.panitia.store'), ['nim' => $mahasiswa->nim])
            ->assertRedirect();

        $panitia = Panitia::query()
            ->where('periode_rekrutmen_id', $periode->id)
            ->where('nim', $mahasiswa->nim)
            ->firstOrFail();

        $this->actingAs($organisasi, 'organisasi')
            ->delete(route('organisasi.rekrutmen.panitia.destroy', $panitia))
            ->assertRedirect();

        $this->assertDatabaseMissing('panitia', ['id' => $panitia->id]);
    }

    public function test_current_recruitment_menu_routes_are_blocked_without_an_active_recruitment(): void
    {
        $organisasi = $this->buatOrganisasi();

        $this->actingAs($organisasi, 'organisasi')
            ->get(route('organisasi.rekrutmen.pendaftar'))
            ->assertRedirect(route('organisasi.dashboard'))
            ->assertSessionHas('error_server', 'Belum ada rekrutmen yang sedang berjalan. Buka rekrutmen terlebih dahulu.');
    }

    public function test_organisasi_can_close_an_active_recruitment(): void
    {
        $organisasi = $this->buatOrganisasi();
        $periode = $this->buatPeriode($organisasi);

        $this->actingAs($organisasi, 'organisasi')
            ->post(route('organisasi.rekrutmen.tutup'))
            ->assertRedirect(route('organisasi.dashboard'));

        $this->assertSame(0, (int) $periode->fresh()->status_aktif);
    }

    public function test_participant_list_supports_nested_position_and_selection_status_filters(): void
    {
        $organisasi = $this->buatOrganisasi();
        $periode = $this->buatPeriode($organisasi);
        $jabatanSatu = $this->buatJabatan($periode);
        $jabatanDua = Jabatan::create([
            'periode_rekrutmen_id' => $periode->id,
            'nama_posisi' => 'Pengurus Harian',
            'nama_jabatan' => 'Bendahara',
        ]);
        $mahasiswaSatu = $this->buatMahasiswa('222222233', 'Peserta Lulus');
        $mahasiswaDua = $this->buatMahasiswa('222222234', 'Peserta Lain');
        $pendaftaranSatu = Pendaftaran::create([
            'nim' => $mahasiswaSatu->nim,
            'jabatan_1_id' => $jabatanSatu->id,
        ]);
        $pendaftaranSatu->status_seleksi = 'Lulus Tahap 1';
        $pendaftaranSatu->save();
        $pendaftaranDua = Pendaftaran::create([
            'nim' => $mahasiswaDua->nim,
            'jabatan_1_id' => $jabatanDua->id,
        ]);
        $pendaftaranDua->status_seleksi = 'Tidak Lolos';
        $pendaftaranDua->save();

        $this->actingAs($organisasi, 'organisasi')
            ->get(route('organisasi.rekrutmen.pendaftar', [
                'filter_jabatan' => $jabatanSatu->id,
                'pilihan_tipe' => ['1'],
            ]))
            ->assertOk()
            ->assertSee('Posisi dan Jabatan')
            ->assertSee('Peserta Lulus')
            ->assertDontSee('Peserta Lain');

        $this->actingAs($organisasi, 'organisasi')
            ->get(route('organisasi.rekrutmen.pendaftar', ['filter_status' => 'Tidak Lolos']))
            ->assertOk()
            ->assertSee('Peserta Lain')
            ->assertDontSee('Peserta Lulus');
    }

    public function test_selection_submission_time_sort_is_processed_on_the_server(): void
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
            'lampiran_tugas' => ['form' => []],
        ]);
        $mahasiswaLama = $this->buatMahasiswa('222222235', 'Peserta Lama');
        $mahasiswaBaru = $this->buatMahasiswa('222222236', 'Peserta Baru');
        $pendaftaranLama = Pendaftaran::create(['nim' => $mahasiswaLama->nim, 'jabatan_1_id' => $jabatan->id]);
        $pendaftaranBaru = Pendaftaran::create(['nim' => $mahasiswaBaru->nim, 'jabatan_1_id' => $jabatan->id]);

        $pengumpulanLama = PengumpulanTugas::create(['tugas_id' => $tugas->id, 'pendaftaran_id' => $pendaftaranLama->id, 'lampiran_jawaban' => ['form' => []]]);
        $pengumpulanLama->timestamps = false;
        $pengumpulanLama->updated_at = now()->subHour();
        $pengumpulanLama->save();
        $pengumpulanBaru = PengumpulanTugas::create(['tugas_id' => $tugas->id, 'pendaftaran_id' => $pendaftaranBaru->id, 'lampiran_jawaban' => ['form' => []]]);
        $pengumpulanBaru->timestamps = false;
        $pengumpulanBaru->updated_at = now()->subMinutes(10);
        $pengumpulanBaru->save();

        $response = $this->actingAs($organisasi, 'organisasi')
            ->get(route('organisasi.rekrutmen.seleksi.jawaban', [
                'tahapanId' => $tahapan->id,
                'jabatanId' => $jabatan->id,
                'urutan_waktu' => 'asc',
            ]));

        $response->assertOk();
        $konten = $response->getContent();
        $this->assertTrue(strpos($konten, 'Peserta Lama') < strpos($konten, 'Peserta Baru'));
    }

    public function test_panitia_can_be_added_with_any_nine_digit_nim_before_first_google_login(): void
    {
        $organisasi = $this->buatOrganisasi();
        $periode = $this->buatPeriode($organisasi);
        $nim = '222222237';

        $this->actingAs($organisasi, 'organisasi')
            ->post(route('organisasi.rekrutmen.panitia.store'), ['nim' => $nim])
            ->assertRedirect();

        $this->assertDatabaseHas('mahasiswa', [
            'nim' => $nim,
            'email_kampus' => $nim.'@stis.ac.id',
            'nama_lengkap' => 'Menunggu login Google',
        ]);
        $this->assertDatabaseHas('panitia', [
            'periode_rekrutmen_id' => $periode->id,
            'nim' => $nim,
        ]);
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
