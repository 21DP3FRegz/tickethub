<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Concert extends Model
{
    use HasFactory;

    protected $fillable = [
        'artist',
        'artist_id',
        'location_id',
    ];

    /**
     * Get the location that hosts the concert.
     */
    public function location(): BelongsTo
    {
        return $this->belongsTo(Location::class);
    }

    /**
     * Get the artist performing at the concert.
     */
    public function artist(): BelongsTo
    {
        return $this->belongsTo(Artist::class);
    }

    /**
     * Get the shows for the concert.
     */
    public function shows(): HasMany
    {
        return $this->hasMany(Show::class)->orderBy('start', 'asc');
    }
}
