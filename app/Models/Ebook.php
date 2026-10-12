<?php

namespace App\Models;

use Database\Factories\EbookFactory;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ebook extends Model
{
    /** @use HasFactory<EbookFactory> */
    use HasFactory, HasUuids;

    protected $table = 'ebook';

    protected $fillable = [
        'cover',
        'title',
        'description',
        'url',
    ];
}
