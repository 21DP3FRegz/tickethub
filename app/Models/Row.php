<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Row extends Model
{
    use HasFactory;

    public function show()
    {
        return $this->belongsTo(Show::class);
    }

    public function seats()
    {
        return $this->hasMany(Seat::class);
    }
}
