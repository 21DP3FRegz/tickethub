<?php

namespace App\Services;

use App\Models\Concert;
use App\Models\Location;
use App\Models\Show;
use App\Models\Ticket;

class ConcertService
{
    /**
     * Get upcoming concerts for the homepage
     *
     * @param int $limit
     * @return array
     */
    public function getUpcomingConcerts($limit = 5)
    {
        return Concert::with('location')
            ->withCount('shows')
            ->withMin('shows', 'start')
            ->orderBy('shows_min_start')
            ->take($limit)
            ->get()
            ->map(function ($concert) {
                return [
                    'id' => $concert->id,
                    'artist' => $concert->artist,
                    'location_name' => $concert->location->name,
                    'next_show_date' => $concert->shows_min_start ? date('M d, Y', strtotime($concert->shows_min_start)) : 'TBA',
                    'shows_count' => $concert->shows_count
                ];
            });
    }

    /**
     * Get popular locations for the homepage
     *
     * @param int $limit
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getPopularLocations($limit = 5)
    {
        return Location::withCount('concerts')
            ->orderByDesc('concerts_count')
            ->take($limit)
            ->get();
    }

    /**
     * Get overall system stats
     *
     * @return array
     */
    public function getStats()
    {
        return [
            'total_concerts' => Concert::count(),
            'total_shows' => Show::count(),
            'total_locations' => Location::count(),
            'total_tickets' => Ticket::count(),
        ];
    }

    /**
     * Get all data needed for the homepage
     *
     * @return array
     */
    public function getHomePageData()
    {
        return [
            'upcomingConcerts' => $this->getUpcomingConcerts(),
            'popularLocations' => $this->getPopularLocations(),
            'stats' => $this->getStats(),
        ];
    }
}
