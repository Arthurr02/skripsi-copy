<?php

namespace Tests\Feature;

use App\Models\DaftarAnggota;
use App\Models\Dosen;
use App\Models\Mahasiswa;
use App\Models\Organisasi;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class UploadSecurityAndDosenTest extends TestCase
{
    use RefreshDatabase;

    public function test_organisasi_cannot_store_a_file_that_only_uses_a_pdf_extension(): void
    {
        Storage::fake('local');
        $organisasi = $this->buatOrganisasi('Badan Eksekutif Mahasiswa');

        $this->actingAs($organisasi, 'organisasi')
            ->post(route('organisasi.daftar-anggota.store'), [
                'tanggal_mulai_periode' => '2026-09-01',
                'tanggal_akhir_periode' => '2026-09-30',
                'file_daftar_anggota' => UploadedFile::fake()->createWithContent('berbahaya.pdf', '<?php echo "malicious";'),
            ])
            ->assertSessionHasErrors('file_daftar_anggota');

        $this->assertDatabaseCount('daftar_anggota', 0);
    }

    public function test_organisasi_upload_is_private_and_only_owner_or_dosen_can_download(): void
    {
        Storage::fake('local');
        $organisasi = $this->buatOrganisasi('Badan Eksekutif Mahasiswa');
        $organisasiLain = $this->buatOrganisasi('Dewan Perwakilan Mahasiswa');

        $this->actingAs($organisasi, 'organisasi')
            ->post(route('organisasi.daftar-anggota.store'), [
                'tanggal_mulai_periode' => '2026-09-01',
                'tanggal_akhir_periode' => '2026-09-30',
                'file_daftar_anggota' => UploadedFile::fake()->createWithContent('anggota.pdf', $this->isiPdfValid()),
            ])
            ->assertRedirect(route('organisasi.daftar-anggota.index'));

        $unggahan = DaftarAnggota::firstOrFail();
        Storage::disk('local')->assertExists($unggahan->file_path);

        $this->actingAs($organisasi, 'organisasi')
            ->get(route('organisasi.daftar-anggota.download', $unggahan))
            ->assertOk();

        $this->actingAs($organisasiLain, 'organisasi')
            ->get(route('organisasi.daftar-anggota.download', $unggahan))
            ->assertForbidden();

        $dosen = Dosen::create([
            'nama' => 'Dosen Penilai',
            'email' => 'dosen@stis.ac.id',
        ]);

        Auth::guard('organisasi')->logout();
        $this->actingAs($dosen, 'dosen')
            ->get(route('dosen.data-organisasi.index', [
                'organisasi' => 'Eksekutif',
                'sort' => 'tanggal_mulai',
                'direction' => 'asc',
            ]))
            ->assertOk()
            ->assertSee('Badan Eksekutif Mahasiswa')
            ->assertDontSee('Dewan Perwakilan Mahasiswa');

        $this->actingAs($dosen, 'dosen')
            ->get(route('dosen.data-organisasi.download', $unggahan))
            ->assertOk();
    }

    public function test_non_dosen_cannot_open_organization_data(): void
    {
        $mahasiswa = Mahasiswa::create([
            'nim' => '222222221',
            'email_kampus' => '222222221@stis.ac.id',
            'nama_lengkap' => 'Mahasiswa Biasa',
        ]);

        $this->actingAs($mahasiswa)
            ->get(route('dosen.data-organisasi.index'))
            ->assertRedirect(route('login'));
    }

    private function buatOrganisasi(string $nama): Organisasi
    {
        return Organisasi::create([
            'email_kampus' => str($nama)->slug().'@stis.ac.id',
            'nama_organisasi' => $nama,
            'lampiran_logo' => 'organisasi/logo.png',
        ]);
    }

    private function isiPdfValid(): string
    {
        return "%PDF-1.4\n1 0 obj\n<< /Type /Catalog >>\nendobj\ntrailer\n<< /Root 1 0 R >>\n%%EOF";
    }
}
