<?php

namespace App\Http\Requests;

use App\Enums\PortfolioStatus;
use App\Support\PortfolioOptions;
use Closure;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Storage;
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
            'demo_link' => ['nullable', 'string', 'max:255', 'url'],
            'repository_link' => ['nullable', 'string', 'max:255', 'url'],
            'tech_stacks' => ['nullable', 'array'],
            'tech_stacks.*' => ['string', 'max:50'],
            // The thumbnail column is NOT NULL, so a thumbnail is required
            // when the record is created — either as an uploaded file or as
            // the URL returned by the instant media upload.
            'thumbnail' => array_merge(
                $this->isMethod('post') ? ['required_without:thumbnail_url'] : [],
                [
                    'nullable',
                    'image',
                    'mimes:'.implode(',', config('media.mimes')),
                    'max:'.config('media.portfolio_max_kb'),
                ],
            ),
            'thumbnail_url' => ['nullable', 'string', 'max:2048', $this->portfolioUrlRule()],
            'galery' => ['nullable', 'array'],
            'galery.*' => ['string', 'max:2048'],
            'galery_files' => ['nullable', 'array', 'max:12'],
            'galery_files.*' => ['image', 'mimes:'.implode(',', config('media.mimes')), 'max:'.config('media.portfolio_max_kb')],
            'galery_urls' => ['nullable', 'array', 'max:12'],
            'galery_urls.*' => ['string', 'max:2048', $this->portfolioUrlRule()],
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
            'thumbnail.required' => 'Thumbnail wajib dipilih',
            'thumbnail.required_without' => 'Thumbnail wajib dipilih',
            'thumbnail.image' => 'Format file harus JPG, PNG, atau WEBP',
            'thumbnail.max' => 'Ukuran file maksimal '.self::maxMegabytes().'MB',
            'galery_files.*.image' => 'Format file harus JPG, PNG, atau WEBP',
            'galery_files.*.max' => 'Ukuran file maksimal '.self::maxMegabytes().'MB',
        ];
    }

    private static function maxMegabytes(): int
    {
        return (int) ceil((int) config('media.portfolio_max_kb') / 1024);
    }

    /**
     * Instant-uploaded URLs must live inside the portfolio media folder —
     * this stops arbitrary URLs (e.g. a remote avatar) being stored as media.
     */
    private function portfolioUrlRule(): Closure
    {
        return function (string $attribute, mixed $value, Closure $fail): void {
            if (blank($value)) {
                return;
            }

            if (! is_string($value)) {
                $fail('URL gambar tidak valid');

                return;
            }

            $folder = (string) config('media.portfolio_folder');
            $prefix = Storage::disk((string) config('media.portfolio_disk', 'public'))->url($folder.'/');

            $owned = str_starts_with($value, $prefix)
                || str_starts_with($value, $folder.'/')
                || (filled(config('media.cloud_name'))
                    && str_starts_with($value, 'https://res.cloudinary.com/'.config('media.cloud_name').'/'));

            if (! $owned) {
                $fail('URL gambar tidak valid');
            }
        };
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
