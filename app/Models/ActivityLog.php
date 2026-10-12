<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ActivityLog extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'user_id',
        'action',
        'description',
        'ip_address',
        'user_agent',
        'meta',
    ];

    protected function casts(): array
    {
        return [
            'meta' => 'array',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Label aksi dalam Bahasa Indonesia untuk ditampilkan di UI.
     */
    public function actionLabel(): string
    {
        return match ($this->action) {
            'auth.login' => 'Login',
            'auth.logout' => 'Logout',
            'auth.password_changed' => 'Ganti Password',
            'portfolio.created' => 'Tambah Portfolio',
            'portfolio.updated' => 'Perbarui Portfolio',
            'portfolio.deleted' => 'Hapus Portfolio',
            'transaction.created' => 'Tambah Transaksi',
            'transaction.updated' => 'Perbarui Transaksi',
            'transaction.deleted' => 'Hapus Transaksi',
            'invoice.created' => 'Tambah Faktur',
            'invoice.updated' => 'Perbarui Faktur',
            'invoice.status_updated' => 'Ubah Status Faktur',
            'invoice.deleted' => 'Hapus Faktur',
            'ebook.created' => 'Tambah Ebook',
            'ebook.updated' => 'Perbarui Ebook',
            'ebook.deleted' => 'Hapus Ebook',
            'user.created' => 'Tambah User',
            'user.updated' => 'Perbarui User',
            'user.deleted' => 'Hapus User',
            'profile.updated' => 'Perbarui Profil',
            'avatar.updated' => 'Ganti Avatar',
            'avatar.deleted' => 'Hapus Avatar',
            'activity.cleared' => 'Bersihkan Log',
            default => (string) $this->action,
        };
    }

    /**
     * Warna badge per jenis aksi.
     */
    public function actionTone(): string
    {
        return match (true) {
            str_starts_with($this->action, 'auth.') => 'bg-primary/10 text-primary',
            str_contains($this->action, '.created'),
            str_contains($this->action, 'auth.login') => 'bg-success/10 text-success',
            str_contains($this->action, '.deleted'),
            str_contains($this->action, 'auth.logout') => 'bg-error/10 text-error',
            str_contains($this->action, '.updated'),
            str_contains($this->action, '.cleared') => 'bg-warning/10 text-warning',
            default => 'bg-base-200 text-base-content/70',
        };
    }
}
