<?php

namespace App\Http\Requests;

use App\Rules\SafeUploadedFile;
use Illuminate\Foundation\Http\FormRequest;

class StoreDaftarAnggotaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user('organisasi') !== null;
    }

    public function rules(): array
    {
        return [
            'file_daftar_anggota' => ['bail', 'required', new SafeUploadedFile(['pdf'])],
            'tanggal_mulai_periode' => ['bail', 'required', 'date'],
            'tanggal_akhir_periode' => ['bail', 'required', 'date', 'after_or_equal:tanggal_mulai_periode'],
        ];
    }

    public function messages(): array
    {
        return [
            'tanggal_akhir_periode.after_or_equal' => 'Tanggal akhir periode tidak boleh mendahului tanggal awal periode.',
        ];
    }
}
