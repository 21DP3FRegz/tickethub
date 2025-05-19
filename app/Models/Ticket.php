<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ticket extends Model
{
    use HasFactory;

    protected $dates = [
        'created_at',
        'updated_at',
    ];

    public function loadAllInfo()
    {
        $this->load('booking', 'show.concert.location', 'seat.row');

        return [
            'id' => $this->id,
            'code' => $this->code,
            'name' => $this->booking->name,
            'created_at' => $this->created_at,
            'seat' => [
                'id' => $this->seat->id,
                'number' => $this->seat->seat_number,
                'label' => $this->seat->label,
                'row' => $this->seat->row ? [
                    'id' => $this->seat->row->id,
                    'name' => $this->seat->row->name,
                ] : null,
            ],
            'show' => $this->show,
        ];
    }

    public function booking()
    {
        return $this->belongsTo(Booking::class);
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
