<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateKategoriRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $kategori = $this->route('kategori');

        return [
            'kode_kategori' => [
                'required',
                'string',
                'max:255',
                Rule::unique('kategoris', 'kode_kategori')->ignore($kategori),
            ],
            'nama_kategori' => ['required', 'string', 'max:255'],
        ];
    }
}
