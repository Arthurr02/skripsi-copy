<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

class UpdateRecruitmentInformationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, string|array<int, string>>
     */
    public function rules(): array
    {
        return [
            'slogan' => ['required', 'string'],
            'deskripsi_rekrutmen' => ['required', 'string'],
            'banner' => ['nullable', 'file', 'mimetypes:image/jpeg,image/png', 'mimes:jpg,jpeg,png', 'max:2048'],
            'buku_pedoman' => ['nullable', 'file', 'mimetypes:application/pdf', 'mimes:pdf', 'max:5120'],
            'nama_posisi' => ['nullable', 'array'],
            'nama_posisi.*' => ['nullable', 'string'],
            'nama_jabatan' => ['required', 'array', 'min:1'],
            'nama_jabatan.*' => ['required', 'string', 'regex:/^[a-zA-Z0-9 ]+$/'],
            'jabatan_ids' => ['nullable', 'array'],
            'jabatan_ids.*' => ['nullable', 'integer', 'distinct'],
            'tahapan' => ['required', 'array', 'min:1'],
            'tahapan.*.id' => ['nullable', 'integer', 'distinct'],
            'tahapan.*.jenis_tahapan' => ['required', 'in:pengumuman,seleksi'],
            'tahapan.*.nama_tahapan' => ['required', 'string'],
            'tahapan.*.deskripsi' => ['required', 'string'],
            'tahapan.*.waktu_pengumuman' => ['nullable', 'date', 'required_if:tahapan.*.jenis_tahapan,pengumuman'],
            'tahapan.*.tanggal_mulai' => ['nullable', 'date', 'required_if:tahapan.*.jenis_tahapan,seleksi'],
            'tahapan.*.tanggal_selesai' => ['nullable', 'date', 'after_or_equal:tahapan.*.tanggal_mulai', 'required_if:tahapan.*.jenis_tahapan,seleksi'],
            'tahapan.*.tugas.*.jabatan_id' => ['nullable', 'integer'],
            'tahapan.*.tugas.*.id' => ['nullable', 'integer'],
            'tahapan.*.tugas.*.deskripsi_tugas' => ['nullable', 'string'],
            'tahapan_lampiran_*' => ['nullable', 'file', 'mimetypes:application/pdf', 'mimes:pdf', 'max:5120'],
            'tahapan.*.tugas.*.lampiran_files.*' => ['nullable', 'file', 'mimetypes:application/pdf,application/msword,application/vnd.openxmlformats-officedocument.wordprocessingml.document', 'mimes:pdf,doc,docx', 'max:2048'],
        ];
    }

    public function after(): array
    {
        return [function (Validator $validator): void {
            $tahapan = $this->input('tahapan', []);

            if (collect($tahapan)->where('jenis_tahapan', 'seleksi')->isEmpty()) {
                $validator->errors()->add('tahapan', 'Tambahkan minimal satu tahapan seleksi.');
            }

            foreach ($tahapan as $index => $data) {
                $jenis = $data['jenis_tahapan'] ?? null;
                $mulai = $jenis === 'pengumuman'
                    ? ($data['waktu_pengumuman'] ?? null)
                    : ($data['tanggal_mulai'] ?? null);
                $berakhir = $jenis === 'pengumuman'
                    ? $mulai
                    : ($data['tanggal_selesai'] ?? null);

                if (! $mulai) {
                    $validator->errors()->add(
                        "tahapan.$index.".($jenis === 'pengumuman' ? 'waktu_pengumuman' : 'tanggal_mulai'),
                        'Waktu tahapan wajib diisi.',
                    );
                }

                if ($jenis === 'seleksi' && ! $berakhir) {
                    $validator->errors()->add("tahapan.$index.tanggal_selesai", 'Waktu akhir tahapan seleksi wajib diisi.');
                }

                $timestampMulai = $mulai ? strtotime($mulai) : false;
                $timestampBerakhir = $berakhir ? strtotime($berakhir) : false;
                if ($timestampMulai !== false && $timestampBerakhir !== false && $timestampBerakhir < $timestampMulai) {
                    $validator->errors()->add("tahapan.$index.tanggal_selesai", 'Waktu akhir tidak boleh mendahului waktu mulai.');
                }
            }
        }];
    }
}
