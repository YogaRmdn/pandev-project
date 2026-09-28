<?php

namespace App\Http\Controllers;

use App\Services\MediaService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class SettingsController extends Controller
{
    public function edit(Request $request): View
    {
        return view('dashboard.settings.index', [
            'user' => $request->user(),
        ]);
    }

    public function updateProfile(Request $request): RedirectResponse
    {
        $user = $request->user();

        $data = $request->validate([
            'fullname' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', Rule::unique('users', 'email')->ignore($user->id)],
        ], [
            'fullname.required' => 'Nama wajib diisi',
            'email.required' => 'Email wajib diisi',
            'email.email' => 'Email tidak valid',
            'email.unique' => 'Email sudah digunakan oleh pengguna lain',
        ]);

        $user->update($data);

        return back()->with('success', 'Profil berhasil diperbarui');
    }

    public function updateAvatar(Request $request, MediaService $media): RedirectResponse
    {
        $user = $request->user();

        if ($request->boolean('remove')) {
            $media->delete($user->image);
            $user->update(['image' => null]);

            return back()->with('success', 'Avatar berhasil dihapus');
        }

        $request->validate([
            'avatar' => ['required', 'image', 'mimes:'.implode(',', config('media.mimes')), 'max:'.config('media.avatar_max_kb')],
        ], [
            'avatar.required' => 'Avatar wajib diunggah',
            'avatar.image' => 'File harus berupa gambar',
            'avatar.max' => 'Ukuran file maksimal 2 MB',
        ]);

        $media->delete($user->image);

        $user->update([
            'image' => $media->upload(
                $request->file('avatar'),
                config('media.avatar_folder'),
                'avatar-'.$user->id
            )['url'],
        ]);

        return back()->with('success', 'Avatar berhasil disimpan');
    }
}
