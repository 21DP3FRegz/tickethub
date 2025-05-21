<?php

namespace App\Http\Controllers;

use App\Models\Show;
use Illuminate\Http\Request;
use Inertia\Inertia;

class SeatController extends Controller
{
    public function index(Show $show)
    {
        $show->load([
            'seats' => function ($query) {
                $query->with(['reservation', 'ticket'])->orderBy('row_id')->orderBy('seat_number');
            },
            'rows' => function ($query) {
                $query->orderBy('order');
            },
        ]);

        return Inertia::render('seats/Index', [
            'show' => $show,
            'seats' => $show->seats,
            'rows' => $show->rows,
        ]);
    }
}
