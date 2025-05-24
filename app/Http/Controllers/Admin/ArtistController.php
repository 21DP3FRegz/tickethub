<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Artist;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ArtistController extends Controller
{
    public function index()
    {
        $artists = Artist::withCount('concerts')
            ->orderBy('name')
            ->paginate(15);

        return Inertia::render('Admin/Artists/Index', [
            'artists' => $artists,
        ]);
    }

    public function create()
    {
        return Inertia::render('Admin/Artists/Create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'bio' => 'nullable|string',
            'image_url' => 'nullable|url',
        ]);

        Artist::create($validated);

        return redirect()->route('admin.artists.index')
            ->with('success', 'Artist created successfully.');
    }

    public function show(Artist $artist)
    {
        $artist->load(['concerts.location', 'concerts.shows']);

        return Inertia::render('Admin/Artists/Show', [
            'artist' => $artist,
        ]);
    }

    public function edit(Artist $artist)
    {
        return Inertia::render('Admin/Artists/Edit', [
            'artist' => $artist,
        ]);
    }

    public function update(Request $request, Artist $artist)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'bio' => 'nullable|string',
            'image_url' => 'nullable|url',
        ]);

        $artist->update($validated);

        return redirect()->route('admin.artists.index')
            ->with('success', 'Artist updated successfully.');
    }

    public function destroy(Artist $artist)
    {
        if ($artist->concerts()->count() > 0) {
            return back()->withErrors(['error' => 'Cannot delete artist with existing concerts.']);
        }

        $artist->delete();

        return redirect()->route('admin.artists.index')
            ->with('success', 'Artist deleted successfully.');
    }
}
