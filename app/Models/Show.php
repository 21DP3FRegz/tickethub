<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Show extends Model
{
    use HasFactory;

    protected $hidden = [
        'concert_id',
    ];

    protected $dates = [
        'start',
        'end',
    ];

    public function concert()
    {
        return $this->belongsTo(Concert::class);
    }

    public function rows()
    {
        return $this->hasMany(Row::class);
    }

    public function seats()
    {
        return $this->hasMany(Seat::class);
    }

    public function reservations()
    {
        return $this->hasMany(Reservation::class);
    }

    public function tickets()
    {
        return $this->hasMany(Ticket::class);
    }
}
