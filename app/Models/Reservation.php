<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Reservation extends Model
{
    protected $fillable = ['show_id', 'seat_id', 'reservation_token', 'reserved_until', 'user_id'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function show()
    {
        return $this->belongsTo(Show::class);
    }

    public function seat()
    {
        return $this->belongsTo(Seat::class);
    }
}
