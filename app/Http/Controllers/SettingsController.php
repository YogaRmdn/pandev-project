<?php

namespace App\Http\Controllers;

use App\Services\MediaService;
use App\Support\ActivityLogger;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;
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

        // Form gabungan: password bersifat opsional. Block keamanan hanya
        // divalidasi kalau salah satu fieldnya diisi, sehingga menyimpan data
        // diri saja tetap bisa dengan satu tombol Simpan.
        $changingPassword = $request->filled('current_password')
            || $request->filled('password')
            || $request->filled('password_confirmation');

        $rules = [
            'fullname' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', Rule::unique('users', 'email')->ignore($user->id)],
        ];

        if ($changingPassword) {
            $rules['current_password'] = ['required', 'current_password'];
            $rules['password'] = ['required', Password::min(8), 'confirmed'];
        }

        $data = $request->validate($rules, [
            'fullname.required' => 'Nama wajib diisi',
            'email.required' => 'Email wajib diisi',
            'email.email' => 'Email tidak valid',
            'email.unique' => 'Email sudah digunakan oleh pengguna lain',
            'current_password.required' => 'Password saat ini wajib diisi',
            'current_password.current_password' => 'Password saat ini tidak sesuai',
            'password.required' => 'Password baru wajib diisi',
            'password.min' => 'Password minimal 8 karakter',
            'password.confirmed' => 'Konfirmasi password tidak cocok',
        ]);

        $user->update([
            'fullname' => $data['fullname'],
            'email' => $data['email'],
        ]);

        ActivityLogger::log(
            'profile.updated',
            'Memperbarui profil sendiri',
        );

        if ($changingPassword) {
            $user->update(['password' => Hash::make($data['password'])]);

            ActivityLogger::log(
                'auth.password_changed',
                $user->fullname.' mengganti password',
            );
        }

        return back()->with('success', 'Profil berhasil diperbarui');
    }

    public function updateAvatar(Request $request, MediaService $media): RedirectResponse
    {
        $user = $request->user();

        if ($request->boolean('remove')) {
            $media->delete($user->image);
            $user->update(['image' => null]);

            ActivityLogger::log('avatar.deleted', 'Menghapus avatar sendiri');

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

        ActivityLogger::log('avatar.updated', 'Mengganti avatar sendiri');

        return back()->with('success', 'Avatar berhasil disimpan');
    }
}
