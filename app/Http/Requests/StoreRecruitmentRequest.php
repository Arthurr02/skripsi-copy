<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreRecruitmentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user('organisasi') !== null;
    }

    public function rules(): array
    {
        return [
            'tahun_periode' => ['bail', 'required', 'string', 'max:20'],
            'nim_panitia' => ['bail', 'required', 'array', 'min:1'],
            'nim_panitia.*' => ['bail', 'required', 'regex:/^\d{9}$/', 'distinct'],
        ];
    }

    public function messages(): array
    {
        return [
            'tahun_periode.required' => 'Pilih periode rekrutmen terlebih dahulu.',
            'nim_panitia.required' => 'Tambahkan setidaknya satu NIM panitia.',
            'nim_panitia.min' => 'Tambahkan setidaknya satu NIM panitia.',
            'nim_panitia.*.required' => 'NIM panitia wajib diisi.',
            'nim_panitia.*.regex' => 'NIM panitia tidak valid. Gunakan tepat 9 digit angka.',
            'nim_panitia.*.distinct' => 'NIM panitia tidak boleh didaftarkan lebih dari satu kali.',
        ];
    }
}
