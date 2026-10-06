<?php

namespace App\Http\Controllers;

use App\Models\Portfolio;
use App\Services\MediaService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class PortfolioMediaController extends Controller
{
    /**
     * Instant upload (Dropzone) — stores the file on the media disk and
     * returns its URL. The form submits that URL, not the file itself.
     */
    public function store(Request $request, MediaService $media): JsonResponse
    {
        $kind = $request->input('kind') === 'thumbnail' ? 'thumbnail' : 'gallery';

        $request->validate([
            'file' => [
                'required',
                'file',
                'image',
                'mimes:'.implode(',', config('media.mimes')),
                'max:'.config('media.portfolio_max_kb'),
            ],
        ], [
            'file.required' => 'File wajib dipilih',
            'file.image' => 'Format file harus JPG, PNG, atau WEBP',
            'file.mimes' => 'Format file harus JPG, PNG, atau WEBP',
            'file.max' => 'Ukuran file maksimal '.(int) ceil((int) config('media.portfolio_max_kb') / 1024).'MB',
        ]);

        $folder = config('media.portfolio_folder').($kind === 'thumbnail' ? '/thumbnails' : '/gallery');

        return response()->json([
            'url' => $media->upload($request->file('file'), $folder)['url'],
        ]);
    }

    /**
     * File-only removal for uploads that never got linked to a portfolio row.
     * Guarded so the endpoint can never delete outside the portfolio folder.
     */
    public function destroy(Request $request, MediaService $media): JsonResponse
    {
        $request->validate([
            'url' => ['required', 'string', 'max:2048'],
        ], [
            'url.required' => 'URL file wajib diisi',
        ]);

        $url = (string) $request->input('url');

        abort_unless($this->ownsUrl($url), 422, 'URL gambar tidak diizinkan');

        $media->delete($url);

        return response()->json(['ok' => true]);
    }

    /**
     * Instant thumbnail removal on edit: deletes the file AND clears the
     * column immediately, so abandoning the form can't leave a dead URL.
     */
    public function destroyThumbnail(Request $request, string $uuid, MediaService $media): JsonResponse
    {
        $portfolio = $this->findOwned($request, $uuid);

        $media->delete($portfolio->thumbnail);
        $portfolio->thumbnail = '';
        $portfolio->save();

        return response()->json(['ok' => true]);
    }

    /**
     * Instant gallery removal: deletes the row and its file immediately.
     */
    public function destroyGalery(Request $request, string $uuid, string $galery, MediaService $media): JsonResponse
    {
        $portfolio = $this->findOwned($request, $uuid);
        $image = $portfolio->galery()->findOrFail($galery);

        $media->delete($image->image_url);
        $image->delete();

        return response()->json(['ok' => true]);
    }

    private function ownsUrl(string $url): bool
    {
        $folder = (string) config('media.portfolio_folder');
        $prefix = Storage::disk((string) config('media.portfolio_disk', 'public'))->url($folder.'/');

        if (str_starts_with($url, $prefix) || str_starts_with($url, $folder.'/')) {
            return true;
        }

        return filled(config('media.cloud_name'))
            && str_starts_with($url, 'https://res.cloudinary.com/'.config('media.cloud_name').'/');
    }

    private function findOwned(Request $request, string $uuid): Portfolio
    {
        $portfolio = Portfolio::with('galery')->findOrFail($uuid);

        abort_if(
            $portfolio->created_by !== $request->user()->id,
            403
        );

        return $portfolio;
    }
}
