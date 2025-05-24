<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Artist;
use App\Models\Concert;
use App\Models\Location;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ConcertController extends Controller
{
    public function index()
    {
        $concerts = Concert::with(['artist', 'location'])
            ->withCount('shows')
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return Inertia::render('Admin/Concerts/Index', [
            'concerts' => $concerts,
        ]);
    }

    public function create()
    {
        $artists = Artist::orderBy('name')->get();
        $locations = Location::orderBy('name')->get();

        return Inertia::render('Admin/Concerts/Create', [
            'artists' => $artists,
            'locations' => $locations,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'artist_id' => 'required|exists:artists,id',
            'location_id' => 'required|exists:locations,id',
        ]);

        Concert::create($validated);

        return redirect()->route('admin.concerts.index')
            ->with('success', 'Concert created successfully.');
    }

    public function show(Concert $concert)
    {
        $concert->load(['artist', 'location', 'shows.tickets', 'shows.reservations']);

        return Inertia::render('Admin/Concerts/Show', [
            'concert' => $concert,
        ]);
    }

    public function edit(Concert $concert)
    {
        $artists = Artist::orderBy('name')->get();
        $locations = Location::orderBy('name')->get();

        // Load the artist and location relationships
        $concert->load(['artist', 'location']);

        return Inertia::render('Admin/Concerts/Edit', [
            'concert' => $concert,
            'artists' => $artists,
            'locations' => $locations,
        ]);
    }

    public function update(Request $request, Concert $concert)
    {
        $validated = $request->validate([
            'artist_id' => 'required|exists:artists,id',
            'location_id' => 'required|exists:locations,id',
        ]);

        $concert->update($validated);

        return redirect()->route('admin.concerts.index')
            ->with('success', 'Concert updated successfully.');
    }

    public function destroy(Concert $concert)
    {
        if ($concert->shows()->count() > 0) {
            return back()->withErrors(['error' => 'Cannot delete concert with existing shows.']);
        }

        $concert->delete();

        return redirect()->route('admin.concerts.index')
            ->with('success', 'Concert deleted successfully.');
    }
}
