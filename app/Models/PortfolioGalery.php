<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PortfolioGalery extends Model
{
    use HasUuids;

    protected $table = 'portfolio_galery';

    public const UPDATED_AT = null;

    protected $fillable = [
        'image_url',
    ];

    public function portfolio(): BelongsTo
    {
        return $this->belongsTo(Portfolio::class, 'portfolio_id');
    }
}
