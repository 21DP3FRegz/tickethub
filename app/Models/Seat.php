<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Seat extends Model
{
    use HasFactory;

    public function show()
    {
        return $this->belongsTo(Show::class);
    }

    public function row()
    {
        return $this->belongsTo(Row::class);
    }

    public function reservation()
    {
        return $this->hasOne(Reservation::class);
    }

    public function ticket()
    {
        return $this->hasOne(Ticket::class);
    }
}
