<?php

namespace App\Http\Requests;

use App\Enums\PortfolioStatus;
use App\Support\PortfolioOptions;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class PortfolioRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string'],
            'category' => ['required', 'string', Rule::in(PortfolioOptions::categories())],
            'status' => ['required', Rule::enum(PortfolioStatus::class)],
            'demo_link' => ['nullable', 'string', 'max:255'],
            'repository_link' => ['nullable', 'string', 'max:255'],
            'tech_stacks' => ['nullable', 'array'],
            'tech_stacks.*' => ['string', 'max:50'],
            // The thumbnail column is NOT NULL, so a thumbnail is required
            // when the record is created even though it is optional on edit.
            'thumbnail' => [
                $this->isMethod('post') ? 'required' : 'nullable',
                'image',
                'mimes:'.implode(',', config('media.mimes')),
                'max:'.config('media.portfolio_max_kb'),
            ],
            'galery' => ['nullable', 'array'],
            'galery.*' => ['string', 'max:2048'],
            'galery_files' => ['nullable', 'array', 'max:12'],
            'galery_files.*' => ['image', 'mimes:'.implode(',', config('media.mimes')), 'max:'.config('media.portfolio_max_kb')],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Nama wajib diisi',
            'description.required' => 'Deskripsi wajib diisi',
            'category.required' => 'Kategori wajib diisi',
            'category.in' => 'Kategori tidak valid',
            'status.required' => 'Status wajib diisi',
            'demo_link.url' => 'Link demo tidak valid',
            'repository_link.url' => 'Link repository tidak valid',
            'thumbnail.image' => 'Format file harus JPG, PNG, atau WEBP',
            'thumbnail.max' => 'Ukuran file maksimal 5MB',
            'galery_files.*.image' => 'Format file harus JPG, PNG, atau WEBP',
            'galery_files.*.max' => 'Ukuran file maksimal 5MB',
        ];
    }

    protected function prepareForValidation(): void
    {
        $stacks = $this->input('tech_stacks', []);

        if (is_string($stacks)) {
            $stacks = explode(',', $stacks);
        }

        $this->merge([
            'tech_stacks' => array_values(array_filter((array) $stacks)),
        ]);
    }
}
