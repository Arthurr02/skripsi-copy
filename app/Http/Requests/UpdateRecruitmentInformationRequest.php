<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

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
            'tahapan.*.nama_tahapan' => ['required', 'string'],
            'tahapan.*.deskripsi' => ['required', 'string'],
            'tahapan.*.tugas.*.jabatan_id' => ['nullable', 'integer'],
            'tahapan.*.tugas.*.id' => ['nullable', 'integer'],
            'tahapan.*.tugas.*.deskripsi_tugas' => ['nullable', 'string'],
            'tahapan_lampiran_*' => ['nullable', 'file', 'mimetypes:application/pdf', 'mimes:pdf', 'max:5120'],
            'tahapan.*.tugas.*.lampiran_files.*' => ['nullable', 'file', 'mimetypes:application/pdf,application/msword,application/vnd.openxmlformats-officedocument.wordprocessingml.document', 'mimes:pdf,doc,docx', 'max:2048'],
        ];
    }
}
