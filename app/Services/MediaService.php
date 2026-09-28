<?php

namespace App\Services;

use Cloudinary\Uploader;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

/**
 * Portfolio media went through Cloudinary in the original app. Local setups
 * have no Cloudinary credentials, so uploads fall back to the public disk and
 * get a `/storage/...` URL. Delete understands both shapes.
 */
class MediaService
{
    /**
     * Resolved lazily so the container can build this with a plain
     * `MediaService $media` type-hint.
     */
    private function disk(): string
    {
        return (string) config('media.portfolio_disk', 'public');
    }

    public function isRemote(): bool
    {
        return filled(config('media.cloud_name'));
    }

    /**
     * @return array{url: string, public_id: ?string, disk: string}
     */
    public function upload(UploadedFile $file, string $folder, ?string $publicId = null): array
    {
        if ($this->isRemote()) {
            $this->configureCloudinary();

            return $this->uploadToCloudinary($file, $folder, $publicId);
        }

        $name = ($publicId ? Str::last($publicId) : Str::random(20)).'.'.$file->getClientOriginalExtension();

        $path = $file->storeAs($folder, $name, $this->disk());

        return [
            'url' => Storage::disk($this->disk())->url($path),
            'public_id' => null,
            'disk' => $this->disk(),
        ];
    }

    public function delete(?string $urlOrPublicId): void
    {
        if (blank($urlOrPublicId)) {
            return;
        }

        if ($this->isRemote() && ! str_starts_with($urlOrPublicId, '/storage/')) {
            $this->destroyCloudinary($urlOrPublicId);

            return;
        }

        $relative = str_replace(Storage::disk($this->disk())->url(''), '', $urlOrPublicId);
        $relative = ltrim(parse_url($relative, PHP_URL_PATH) ?: $relative, '/');

        if ($relative !== '' && Storage::disk($this->disk())->exists($relative)) {
            Storage::disk($this->disk())->delete($relative);
        }
    }

    /**
     * @return array{url: string, public_id: ?string, disk: string}
     */
    private function uploadToCloudinary(UploadedFile $file, string $folder, ?string $publicId): array
    {
        $result = Uploader::upload(
            $file->getRealPath(),
            [
                'folder' => $folder,
                'resource_type' => 'image',
                'overwrite' => true,
            ]
        );

        return [
            'url' => $result['secure_url'],
            'public_id' => $result['public_id'],
            'disk' => 'cloudinary',
        ];
    }

    private function destroyCloudinary(string $publicId): void
    {
        $this->configureCloudinary();

        Uploader::destroy($publicId, ['resource_type' => 'image']);
    }

    private function configureCloudinary(): void
    {
        \Cloudinary::config([
            'cloud' => [
                'cloud_name' => config('media.cloud_name'),
                'api_key' => config('media.api_key'),
                'api_secret' => config('media.api_secret'),
            ],
        ]);
    }
}
