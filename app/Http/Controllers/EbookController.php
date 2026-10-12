<?php

namespace App\Http\Controllers;

use App\Http\Requests\EbookRequest;
use App\Models\Ebook;
use App\Services\MediaService;
use App\Support\ActivityLogger;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class EbookController extends Controller
{
    public function index(): View
    {
        return view('dashboard.ebook.index', [
            'ebooks' => Ebook::latest()->get(),
        ]);
    }

    public function create(): View
    {
        return view('dashboard.ebook.create', [
            'ebook' => new Ebook,
        ]);
    }

    public function store(EbookRequest $request, MediaService $media): RedirectResponse
    {
        $data = $request->safe()->except(['cover']);

        if ($request->hasFile('cover')) {
            $data['cover'] = $media->upload(
                $request->file('cover'),
                config('media.ebook_folder')
            )['url'];
        }

        $ebook = Ebook::create($data);

        ActivityLogger::log(
            'ebook.created',
            'Menambahkan ebook "'.$ebook->title.'"',
            ['ebook_id' => $ebook->id],
        );

        return redirect()
            ->route('dashboard.ebook.index')
            ->with('success', 'Ebook berhasil dibuat');
    }

    public function edit(Ebook $ebook): View
    {
        return view('dashboard.ebook.edit', [
            'ebook' => $ebook,
        ]);
    }

    public function update(EbookRequest $request, Ebook $ebook, MediaService $media): RedirectResponse
    {
        $data = $request->safe()->except(['cover']);

        if ($request->hasFile('cover')) {
            $media->delete($ebook->cover);
            $data['cover'] = $media->upload(
                $request->file('cover'),
                config('media.ebook_folder')
            )['url'];
        }

        $ebook->update($data);

        ActivityLogger::log(
            'ebook.updated',
            'Memperbarui ebook "'.$ebook->title.'"',
            ['ebook_id' => $ebook->id],
        );

        return redirect()
            ->route('dashboard.ebook.index')
            ->with('success', 'Ebook berhasil diperbarui');
    }

    public function destroy(Ebook $ebook, MediaService $media): RedirectResponse
    {
        $title = $ebook->title;

        $media->delete($ebook->cover);

        $ebook->delete();

        ActivityLogger::log(
            'ebook.deleted',
            'Menghapus ebook "'.$title.'"',
            ['ebook_id' => $ebook->id],
        );

        return redirect()
            ->route('dashboard.ebook.index')
            ->with('success', 'Ebook berhasil dihapus');
    }
}
