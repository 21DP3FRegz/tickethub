<?php

namespace App\Http\Controllers;

use App\Models\Concert;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ConcertController extends Controller
{
    public function index(Request $request)
    {
        $query = Concert::with(['location', 'shows'])
            ->orderBy('artist');

        // Filter by artist
        if ($request->has('artist')) {
            $query->where('artist', 'like', '%' . $request->input('artist') . '%');
        }

        // Filter by location
        if ($request->has('location')) {
            $query->whereHas('location', function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->input('location') . '%');
            });
        }

        // Filter by date
        if ($request->has('date')) {
            $query->whereHas('shows', function ($q) use ($request) {
                $q->whereDate('start', $request->input('date'));
            });
        }

        $concerts = $query->get();

        return Inertia::render('concerts/Index', [
            'concerts' => $concerts,
            'filters' => $request->only(['location', 'date', 'artist']),
        ]);
    }

    public function show(Concert $concert)
    {
        // Load concert with location, shows, and the related seats and rows for each show
        $concert->load([
            'location',
            'shows' => function ($query) {
                $query->where('start', '>=', now())->orderBy('start');
            },
            'shows.seats', // Load seats for each show
            'shows.rows' // Load rows for each show
        ]);

        return Inertia::render('concerts/Show', [
            'concert' => $concert,
        ]);
    }
}
