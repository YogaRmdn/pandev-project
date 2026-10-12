<?php

namespace App\Http\Requests;

use Closure;
use Illuminate\Foundation\Http\FormRequest;

class EbookRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string'],
            // Aturan `url` Laravel hanya memakai FILTER_VALIDATE_URL, yang masih
            // menerima `javascript:...`. Blokir semua skema selain http/https
            // supaya tautan yang tersimpan tidak bisa menjadi vektor XSS.
            'url' => [
                'required',
                'string',
                'max:2048',
                'url',
                function (string $attribute, mixed $value, Closure $fail) {
                    if (! is_string($value) || preg_match('#^https?://#i', $value) !== 1) {
                        $fail('URL ebook harus diawali http:// atau https://');
                    }
                },
            ],
            'cover' => [
                'nullable',
                'image',
                'mimes:'.implode(',', config('media.mimes')),
                'max:'.config('media.ebook_max_kb'),
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'title.required' => 'Nama ebook wajib diisi',
            'description.required' => 'Deskripsi ebook wajib diisi',
            'url.required' => 'URL ebook wajib diisi',
            'url.url' => 'URL ebook tidak valid',
            'cover.image' => 'Format file harus JPG, PNG, atau WEBP',
            'cover.max' => 'Ukuran file maksimal '.self::maxMegabytes().'MB',
        ];
    }

    private static function maxMegabytes(): int
    {
        return (int) ceil((int) config('media.ebook_max_kb') / 1024);
    }
}
