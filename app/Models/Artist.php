<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Artist extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'bio',
        'image_url',
    ];

    /**
     * Get the concerts for the artist.
     */
    public function concerts(): HasMany
    {
        return $this->hasMany(Concert::class);
    }
}
