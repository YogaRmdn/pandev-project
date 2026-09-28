<?php

namespace App\Models;

use App\Enums\PortfolioStatus;
use Database\Factories\PortfolioFactory;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Portfolio extends Model
{
    /** @use HasFactory<PortfolioFactory> */
    use HasFactory, HasUuids;

    protected $table = 'portfolio';

    protected $fillable = [
        'thumbnail',
        'name',
        'category',
        'description',
        'demo_link',
        'repository_link',
        'status',
        'tech_stacks',
        'created_by',
    ];

    protected function casts(): array
    {
        return [
            'status' => PortfolioStatus::class,
            'tech_stacks' => 'array',
        ];
    }

    public function galery(): HasMany
    {
        return $this->hasMany(PortfolioGalery::class, 'portfolio_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function scopePublished($query)
    {
        return $query->where('status', PortfolioStatus::PUBLISHED->value);
    }

    public function scopeOwnedBy($query, User $user)
    {
        return $query->where('created_by', $user->id);
    }

    public function getIsDraftAttribute(): bool
    {
        return $this->status === PortfolioStatus::DRAFT;
    }
}
