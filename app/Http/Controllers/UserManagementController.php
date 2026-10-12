<?php

namespace App\Http\Controllers;

use App\Enums\Role;
use App\Models\User;
use App\Support\ActivityLogger;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;
use Illuminate\View\View;

class UserManagementController extends Controller
{
    public function index(Request $request): View
    {
        $users = User::query()
            // The original hid the signed-in user from their own list.
            ->whereKeyNot($request->user()->id)
            ->when($request->input('search'), fn ($query, $search) => $query->where(function ($q) use ($search) {
                $q->where('fullname', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            }))
            ->latest()
            ->get();

        return view('dashboard.users.index', [
            'users' => $users,
            'roles' => Role::cases(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'fullname' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'confirmed', Password::min(8)],
            'password_confirmation' => ['required', 'string'],
            'role' => ['required', Rule::enum(Role::class)],
        ], [
            'fullname.required' => 'Nama wajib diisi',
            'email.required' => 'Email wajib diisi',
            'email.email' => 'Email tidak valid',
            'email.unique' => 'Email sudah digunakan',
            'password.required' => 'Password wajib diisi',
            'password.confirmed' => 'Konfirmasi password tidak cocok',
            'password_confirmation.required' => 'Konfirmasi password wajib diisi',
            'role.required' => 'Role wajib dipilih',
        ]);

        User::create([
            'fullname' => $data['fullname'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'role' => $data['role'],
        ]);

        ActivityLogger::log(
            'user.created',
            'Membuat akun "'.$data['fullname'].'" ('.$data['role'].')',
            ['email' => $data['email'], 'role' => $data['role']],
        );

        return back()->with('success', 'User berhasil dibuat');
    }

    public function update(Request $request, User $user): RedirectResponse
    {
        // The original edit dialog called the create action, so "Edit" created
        // a second user and then failed on the duplicate-email check.
        $data = $request->validate([
            'fullname' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', Rule::unique('users', 'email')->ignore($user->id)],
            // Form edit tidak merender password_confirmation, jadi aturan
            // `confirmed` di sini hanya akan memblokir pergantian password
            // (error "tidak cocok" padahal field konfirmasi tak terlihat).
            // Password opsional, minimal 8 karakter; kosong berarti tidak diubah.
            'password' => ['nullable', Password::min(8)],
            'role' => ['required', Rule::enum(Role::class)],
        ], [
            'fullname.required' => 'Nama wajib diisi',
            'email.required' => 'Email wajib diisi',
            'email.email' => 'Email tidak valid',
            'email.unique' => 'Email sudah digunakan',
            'role.required' => 'Role wajib dipilih',
        ]);

        $user->fullname = $data['fullname'];
        $user->email = $data['email'];
        $user->role = $data['role'];

        if (filled($data['password'] ?? null)) {
            $user->password = Hash::make($data['password']);
        }

        // Menghindari lockout: superadmin tidak boleh menurunkan role akunnya
        // sendiri lewat permintaan rakitan (list user memang menyembunyikan diri).
        // Bandingkan nilai enum (string), bukan objek Role vs string mentah.
        if ($user->id === $request->user()->id
            && $data['role'] !== $request->user()->role?->value) {
            abort(422, 'Anda tidak dapat mengubah role akun sendiri');
        }

        $user->save();

        ActivityLogger::log(
            'user.updated',
            'Memperbarui akun "'.$user->fullname.'"',
            ['user_id' => $user->id, 'email' => $user->email, 'role' => $user->role->value],
        );

        return back()->with('success', 'User berhasil diperbarui');
    }

    public function destroy(Request $request, User $user): RedirectResponse
    {
        if ($user->id === $request->user()->id) {
            return back()->with('success', 'Anda tidak dapat menghapus akun sendiri');
        }

        // Mencegah lockout total: superadmin terakhir tidak boleh dihapus.
        if ($user->isSuperadmin()
            && User::where('role', Role::SUPERADMIN->value)->count() <= 1) {
            abort(422, 'Tidak dapat menghapus superadmin terakhir');
        }

        $fullname = $user->fullname;

        $user->delete();

        ActivityLogger::log(
            'user.deleted',
            'Menghapus akun "'.$fullname.'"',
            ['user_id' => $user->id],
        );

        return back()->with('success', 'Akun berhasil dihapus');
    }
}
