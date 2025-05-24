<script setup lang="ts">
import { Head, Link, router } from '@inertiajs/vue3';
import AppSidebarLayout from '@/layouts/app/AppSidebarLayout.vue';
import { ref, watch, computed } from 'vue';
import {
    CalendarDays,
    MapPin,
    Music,
    Search,
    X,
    Ticket,
    Clock,
    Calendar,
    ArrowUpDown,
    ArrowUp,
    ArrowDown,
    Star,
    Zap,
    Heart,
    Headphones
} from 'lucide-vue-next';
import debounce from 'lodash/debounce';

const props = defineProps<{
    concerts: Array<{
        id: number;
        artist: string | { name: string; id?: number };
        artist_id?: number;
        location: { name: string; id: number };
        shows: Array<{ start: string; end?: string }>;
    }>;
    filters: {
        location?: string;
        date?: string;
        artist?: string;
        search?: string;
        location_id?: number;
        artist_id?: number;
        sort?: string;
        sort_direction?: string;
    };
    userBookings?: Record<string, any>;
    locations: Array<{ id: number; name: string }>;
    artists: Array<{ id: number; name: string }>;
}>();

// Create reactive filter state
const filterLocation = ref(props.filters.location || '');
const filterDate = ref(props.filters.date || '');
const filterArtist = ref(props.filters.artist || '');
const searchQuery = ref(props.filters.search || '');
const selectedLocationId = ref(props.filters.location_id || '');
const selectedArtistId = ref(props.filters.artist_id || '');

// Sorting options - default to date sorting
const sortField = ref(props.filters.sort || 'date');
const sortDirection = ref(props.filters.sort_direction || 'asc');

// Format date for display
const formatDate = (dateString: string) => {
    if (!dateString) return '';
    const date = new Date(dateString);
    return date.toLocaleDateString('en-US', {
        weekday: 'short',
        year: 'numeric',
        month: 'short',
        day: 'numeric',
        hour: '2-digit',
        minute: '2-digit'
    });
};

// Format date in a shorter way
const formatShortDate = (dateString: string) => {
    if (!dateString) return '';
    const date = new Date(dateString);
    return date.toLocaleDateString('en-US', {
        month: 'short',
        day: 'numeric',
    });
};

// Format time
const formatTime = (dateString: string) => {
    if (!dateString) return '';
    const date = new Date(dateString);
    return date.toLocaleTimeString('en-US', {
        hour: '2-digit',
        minute: '2-digit'
    });
};

// Apply filters and sorting
const applyFilters = debounce(() => {
    router.get(route('concerts.index'), {
        location: filterLocation.value || undefined,
        date: filterDate.value || undefined,
        artist: filterArtist.value || undefined,
        search: searchQuery.value || undefined,
        location_id: selectedLocationId.value || undefined,
        artist_id: selectedArtistId.value || undefined,
        sort: sortField.value || undefined,
        sort_direction: sortDirection.value || undefined
    }, {
        preserveState: true,
        replace: true
    });
}, 300);

// Modify the toggleSort function to only handle date sorting
const toggleSort = (field: string) => {
    if (field === 'date') {
        sortDirection.value = sortDirection.value === 'asc' ? 'desc' : 'asc';
        applyFilters();
    }
};

// Get sort icon
const getSortIcon = (field: string) => {
    if (sortField.value !== field) {
        return ArrowUpDown;
    }
    return sortDirection.value === 'asc' ? ArrowUp : ArrowDown;
};

// Watch for changes in search query
watch(searchQuery, () => {
    applyFilters();
});

// Watch for changes in filter selections to apply filters immediately
watch([selectedArtistId, selectedLocationId, filterDate], () => {
    applyFilters();
});

// Clear all filters
const clearFilters = () => {
    filterLocation.value = '';
    filterDate.value = '';
    filterArtist.value = '';
    searchQuery.value = '';
    selectedLocationId.value = '';
    selectedArtistId.value = '';
    sortField.value = 'date'; // Default to date sorting
    sortDirection.value = 'asc';
    router.get(route('concerts.index'), {}, {
        preserveState: true,
        replace: true
    });
};

// Watch for Enter key in inputs
const handleKeyDown = (e: KeyboardEvent) => {
    if (e.key === 'Enter') {
        e.preventDefault();
        applyFilters();
    }
};

// Check if user has bookings for a concert
const hasBookingsForConcert = (concertId: number) => {
    if (!props.userBookings) return false;
    return Object.values(props.userBookings).some(
        (booking: any) => booking.concert_id === concertId
    );
};

// Get user bookings for a concert
const getBookingsForConcert = (concertId: number) => {
    if (!props.userBookings) return null;
    return Object.values(props.userBookings).find(
        (booking: any) => booking.concert_id === concertId
    );
};

// Update the component to properly handle the artist object
const getArtistName = (concert) => {
    if (typeof concert.artist === 'object' && concert.artist !== null) {
        return concert.artist.name;
    }
    return concert.artist; // Fallback to the string if it's not an object
};

// Get the next show date for a concert
const getNextShowDate = (concert) => {
    if (!concert.shows || concert.shows.length === 0) return null;

    // Sort shows by date
    const sortedShows = [...concert.shows].sort((a, b) => {
        return new Date(a.start).getTime() - new Date(b.start).getTime();
    });

    // Find the first show that's in the future
    const now = new Date();
    const futureShows = sortedShows.filter(show => new Date(show.start) > now);

    return futureShows.length > 0 ? futureShows[0].start : sortedShows[0].start;
};

// Get the duration of a show
const getShowDuration = (show) => {
    if (!show.end) return null;

    const start = new Date(show.start);
    const end = new Date(show.end);
    const durationMs = end.getTime() - start.getTime();

    // Convert to minutes
    return Math.round(durationMs / (1000 * 60));
};

// Subtle color themes - much more muted
const getCardTheme = (index: number) => {
    const themes = [
        {
            headerBg: 'bg-primary/8',
            textColor: 'text-primary/80',
            accentBg: 'bg-primary/5',
            borderColor: 'border-primary/15',
            buttonBg: 'bg-primary/90',
            icon: Star
        },
        {
            headerBg: 'bg-accent/8',
            textColor: 'text-accent/80',
            accentBg: 'bg-accent/5',
            borderColor: 'border-accent/15',
            buttonBg: 'bg-accent/90',
            icon: Zap
        },
        {
            headerBg: 'bg-chart-4/8',
            textColor: 'text-chart-4/80',
            accentBg: 'bg-chart-4/5',
            borderColor: 'border-chart-4/15',
            buttonBg: 'bg-chart-4/90',
            icon: Heart
        },
        {
            headerBg: 'bg-chart-2/8',
            textColor: 'text-chart-2/80',
            accentBg: 'bg-chart-2/5',
            borderColor: 'border-chart-2/15',
            buttonBg: 'bg-chart-2/90',
            icon: Headphones
        }
    ];
    return themes[index % themes.length];
};

// Style approach selector
const styleApproach = ref('subtle-tinted');

// Theme-aware Date Badge styling
const getDateBadgeClasses = () => {
    return {
        // Light theme: Semi-transparent white with dark text
        light: 'bg-white/95 text-foreground border-white/30 shadow-lg backdrop-blur-sm',
        // Dark theme: Semi-transparent dark with light text
        dark: 'dark:bg-card/95 dark:text-card-foreground dark:border-border/30 dark:shadow-xl dark:backdrop-blur-sm'
    };
};

// Get complete date badge classes
const dateBadgeClasses = computed(() => {
    const classes = getDateBadgeClasses();
    return `absolute top-4 right-4 rounded-lg px-3 py-2 text-sm flex items-center border transition-all duration-200 ${classes.light} ${classes.dark}`;
});
</script>

<template>
    <Head title="Concerts" />
    <AppSidebarLayout :breadcrumbs="[{ title: 'Concerts', href: '/concerts' }]">
        <div class="p-6">
            <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-8">
                <h1 class="text-2xl font-bold mb-2 md:mb-0">Upcoming Concerts</h1>

                <div class="flex items-center space-x-4">
                    <!-- Style Selector for Demo -->
                    <select
                        v-model="styleApproach"
                        class="text-xs px-2 py-1 bg-secondary rounded-md border border-border"
                    >
                        <option value="subtle-tinted">Subtle Tinted</option>
                        <option value="soft-accent">Soft Accent</option>
                        <option value="minimal-border">Minimal Border</option>
                        <option value="gentle-pattern">Gentle Pattern</option>
                    </select>

                    <span class="text-sm text-muted-foreground">
                        {{ concerts.length }} {{ concerts.length === 1 ? 'concert' : 'concerts' }} found
                    </span>
                </div>
            </div>

            <!-- Enhanced Search and Filter Bar -->
            <div class="bg-card rounded-lg p-4 mb-8 shadow-sm border border-border">
                <div class="flex items-center justify-between mb-4">
                    <div class="flex items-center">
                        <Search class="h-5 w-5 text-primary mr-2" />
                        <h2 class="text-lg font-medium">Find Concerts</h2>
                    </div>
                </div>

                <!-- Search Bar -->
                <div class="relative mb-4">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <Search class="h-4 w-4 text-muted-foreground" />
                    </div>
                    <input
                        v-model="searchQuery"
                        placeholder="Search by artist or venue"
                        class="w-full pl-10 pr-3 py-2 bg-background border border-input rounded-md focus:outline-none focus:ring-2 focus:ring-ring focus:border-primary"
                        @keydown="handleKeyDown"
                    />
                </div>

                <!-- Always Visible Filter Options -->
                <div class="space-y-4 mt-4 pt-4 border-t border-border">
                    <!-- Filter Selects -->
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <!-- Artist Select -->
                        <div>
                            <label for="artist_id" class="block text-sm font-medium text-muted-foreground mb-1">
                                Artist
                            </label>
                            <select
                                id="artist_id"
                                v-model="selectedArtistId"
                                class="w-full px-3 py-2 bg-background border border-input rounded-md focus:outline-none focus:ring-2 focus:ring-ring focus:border-primary"
                            >
                                <option value="">All Artists</option>
                                <option v-for="artist in artists" :key="artist.id" :value="artist.id">
                                    {{ artist.name }}
                                </option>
                            </select>
                        </div>

                        <!-- Location Select -->
                        <div>
                            <label for="location_id" class="block text-sm font-medium text-muted-foreground mb-1">
                                Venue
                            </label>
                            <select
                                id="location_id"
                                v-model="selectedLocationId"
                                class="w-full px-3 py-2 bg-background border border-input rounded-md focus:outline-none focus:ring-2 focus:ring-ring focus:border-primary"
                            >
                                <option value="">All Venues</option>
                                <option v-for="location in locations" :key="location.id" :value="location.id">
                                    {{ location.name }}
                                </option>
                            </select>
                        </div>

                        <!-- Date Selection with Sort -->
                        <div>
                            <div class="flex items-center justify-between mb-1">
                                <label for="date" class="block text-sm font-medium text-muted-foreground">
                                    Date
                                </label>
                                <button
                                    @click="toggleSort('date')"
                                    class="text-xs flex items-center text-muted-foreground hover:text-primary transition-colors"
                                    title="Toggle date sort order"
                                >
                                    <component :is="getSortIcon('date')" class="h-3 w-3 mr-1" />
                                    <span>{{ sortDirection === 'asc' ? 'Earliest first' : 'Latest first' }}</span>
                                </button>
                            </div>
                            <div class="relative">
                                <CalendarDays class="absolute left-3 top-1/2 -translate-y-1/2 h-4 w-4 text-muted-foreground" />
                                <input
                                    id="date"
                                    v-model="filterDate"
                                    type="date"
                                    class="w-full pl-10 pr-3 py-2 bg-background border border-input rounded-md focus:outline-none focus:ring-2 focus:ring-ring focus:border-primary"
                                />
                            </div>
                        </div>
                    </div>

                    <div class="flex justify-end">
                        <button
                            type="button"
                            @click="clearFilters"
                            class="px-4 py-2 bg-secondary text-secondary-foreground rounded-md hover:bg-secondary/90 focus:outline-none focus:ring-2 focus:ring-ring focus:ring-offset-2 transition-colors"
                        >
                            Clear Filters
                        </button>
                    </div>
                </div>

                <!-- Active Filters -->
                <div v-if="filters.location || filters.date || filters.artist || filters.search || filters.location_id || filters.artist_id" class="mt-4 flex flex-wrap gap-2">
                    <div v-if="filters.search" class="inline-flex items-center bg-secondary text-secondary-foreground px-3 py-1 rounded-full text-sm">
                        <span class="mr-1">Search:</span>
                        <span class="font-medium">{{ filters.search }}</span>
                        <button
                            @click="() => { searchQuery = ''; }"
                            class="ml-2 text-secondary-foreground/70 hover:text-secondary-foreground transition-colors"
                        >
                            <X class="h-3 w-3" />
                        </button>
                    </div>

                    <div v-if="filters.artist_id" class="inline-flex items-center bg-secondary text-secondary-foreground px-3 py-1 rounded-full text-sm">
                        <span class="mr-1">Artist:</span>
                        <span class="font-medium">{{ artists.find(a => a.id == filters.artist_id)?.name }}</span>
                        <button
                            @click="() => { selectedArtistId = ''; }"
                            class="ml-2 text-secondary-foreground/70 hover:text-secondary-foreground transition-colors"
                        >
                            <X class="h-3 w-3" />
                        </button>
                    </div>

                    <div v-if="filters.location_id" class="inline-flex items-center bg-secondary text-secondary-foreground px-3 py-1 rounded-full text-sm">
                        <span class="mr-1">Venue:</span>
                        <span class="font-medium">{{ locations.find(l => l.id == filters.location_id)?.name }}</span>
                        <button
                            @click="() => { selectedLocationId = ''; }"
                            class="ml-2 text-secondary-foreground/70 hover:text-secondary-foreground transition-colors"
                        >
                            <X class="h-3 w-3" />
                        </button>
                    </div>

                    <div v-if="filters.date" class="inline-flex items-center bg-secondary text-secondary-foreground px-3 py-1 rounded-full text-sm">
                        <span class="mr-1">Date:</span>
                        <span class="font-medium">{{ filters.date }}</span>
                        <button
                            @click="() => { filterDate = ''; }"
                            class="ml-2 text-secondary-foreground/70 hover:text-secondary-foreground transition-colors"
                        >
                            <X class="h-3 w-3" />
                        </button>
                    </div>
                </div>
            </div>

            <!-- Concert List with Theme-Aware Date Badges -->
            <div v-if="concerts.length > 0" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                <div
                    v-for="(concert, index) in concerts"
                    :key="concert.id"
                    class="bg-card rounded-xl overflow-hidden shadow-sm hover:shadow-md transition-all duration-300 border border-border group"
                    :class="styleApproach === 'minimal-border' ? getCardTheme(index).borderColor : 'hover:border-primary/20'"
                >
                    <!-- APPROACH 1: Subtle Tinted Headers -->
                    <div v-if="styleApproach === 'subtle-tinted'" :class="['h-32 relative overflow-hidden', getCardTheme(index).headerBg]">
                        <!-- Very subtle overlay -->
                        <div class="absolute inset-0 bg-foreground/2"></div>

                        <!-- Theme-Aware Date Badge -->
                        <div :class="dateBadgeClasses">
                            <Calendar class="h-4 w-4 mr-2 text-primary dark:text-primary" />
                            <span class="font-medium">{{ formatShortDate(getNextShowDate(concert)) }}</span>
                        </div>

                        <!-- Artist and Venue Info -->
                        <div class="absolute bottom-0 left-0 p-5 text-foreground">
                            <h2 class="text-xl font-bold truncate mb-1">
                                {{ getArtistName(concert) }}
                            </h2>
                            <div class="flex items-center text-sm text-muted-foreground">
                                <MapPin class="h-4 w-4 mr-2 flex-shrink-0" />
                                <span class="truncate">{{ concert.location.name }}</span>
                            </div>
                        </div>
                    </div>

                    <!-- APPROACH 2: Soft Accent -->
                    <div v-else-if="styleApproach === 'soft-accent'" class="h-32 bg-muted/30 relative overflow-hidden">
                        <!-- Very subtle colored accent bar -->
                        <div :class="['absolute top-0 left-0 w-full h-0.5', getCardTheme(index).buttonBg]"></div>

                        <!-- Small accent icon -->
                        <div :class="['absolute top-4 right-16 p-2 rounded-full', getCardTheme(index).accentBg]">
                            <component :is="getCardTheme(index).icon" :class="['h-4 w-4', getCardTheme(index).textColor]" />
                        </div>

                        <!-- Theme-Aware Date Badge -->
                        <div :class="dateBadgeClasses">
                            <Calendar class="h-4 w-4 mr-2 text-primary dark:text-primary" />
                            <span class="font-medium">{{ formatShortDate(getNextShowDate(concert)) }}</span>
                        </div>

                        <!-- Artist and Venue Info -->
                        <div class="absolute bottom-0 left-0 p-5 text-foreground">
                            <h2 class="text-xl font-bold truncate mb-1">
                                {{ getArtistName(concert) }}
                            </h2>
                            <div class="flex items-center text-sm text-muted-foreground">
                                <MapPin class="h-4 w-4 mr-2 flex-shrink-0" />
                                <span class="truncate">{{ concert.location.name }}</span>
                            </div>
                        </div>
                    </div>

                    <!-- APPROACH 3: Minimal Border -->
                    <div v-else-if="styleApproach === 'minimal-border'" class="h-32 bg-background relative overflow-hidden border-l-2" :class="getCardTheme(index).borderColor.replace('border-', 'border-l-')">
                        <!-- Very subtle background tint -->
                        <div :class="['absolute inset-0 opacity-3', getCardTheme(index).headerBg]"></div>

                        <!-- Small category indicator -->
                        <div :class="['absolute top-4 left-4 p-1.5 rounded-md', getCardTheme(index).accentBg]">
                            <component :is="getCardTheme(index).icon" :class="['h-4 w-4', getCardTheme(index).textColor]" />
                        </div>

                        <!-- Theme-Aware Date Badge -->
                        <div :class="dateBadgeClasses">
                            <Calendar class="h-4 w-4 mr-2 text-primary dark:text-primary" />
                            <span class="font-medium">{{ formatShortDate(getNextShowDate(concert)) }}</span>
                        </div>

                        <!-- Artist and Venue Info -->
                        <div class="absolute bottom-0 left-0 p-5 text-foreground">
                            <h2 class="text-xl font-bold truncate mb-1">
                                {{ getArtistName(concert) }}
                            </h2>
                            <div class="flex items-center text-sm text-muted-foreground">
                                <MapPin class="h-4 w-4 mr-2 flex-shrink-0" />
                                <span class="truncate">{{ concert.location.name }}</span>
                            </div>
                        </div>
                    </div>

                    <!-- APPROACH 4: Gentle Pattern -->
                    <div v-else class="h-32 bg-muted/20 relative overflow-hidden">
                        <!-- Very subtle geometric shapes -->
                        <div :class="['absolute -top-6 -right-6 w-16 h-16 rounded-full opacity-5', getCardTheme(index).buttonBg]"></div>
                        <div :class="['absolute -bottom-3 -left-3 w-12 h-12 rounded-full opacity-3', getCardTheme(index).buttonBg]"></div>
                        <div :class="['absolute top-1/2 left-1/2 w-6 h-6 rotate-45 opacity-2', getCardTheme(index).buttonBg]"></div>

                        <!-- Theme-Aware Date Badge -->
                        <div :class="dateBadgeClasses">
                            <Calendar class="h-4 w-4 mr-2 text-primary dark:text-primary" />
                            <span class="font-medium">{{ formatShortDate(getNextShowDate(concert)) }}</span>
                        </div>

                        <!-- Artist and Venue Info -->
                        <div class="absolute bottom-0 left-0 p-5 text-foreground">
                            <h2 class="text-xl font-bold truncate mb-1">
                                {{ getArtistName(concert) }}
                            </h2>
                            <div class="flex items-center text-sm text-muted-foreground">
                                <MapPin class="h-4 w-4 mr-2 flex-shrink-0" />
                                <span class="truncate">{{ concert.location.name }}</span>
                            </div>
                        </div>
                    </div>

                    <div class="p-5">
                        <!-- User's Bookings for this concert -->
                        <div v-if="hasBookingsForConcert(concert.id)" :class="['mb-4 p-3 border rounded-lg', getCardTheme(index).accentBg, getCardTheme(index).borderColor]">
                            <div :class="['flex items-center mb-2', getCardTheme(index).textColor]">
                                <Ticket class="h-4 w-4 mr-2" />
                                <span class="font-medium text-sm">Your Booked Tickets</span>
                            </div>
                            <div v-for="(show, showIndex) in getBookingsForConcert(concert.id).shows" :key="showIndex" :class="['text-xs mb-1', getCardTheme(index).textColor, 'opacity-70']">
                                <div class="font-medium">{{ show.show_date }}</div>
                                <div class="flex flex-wrap gap-1 mt-1">
                                    <span v-for="(seat, seatIndex) in show.seats" :key="seatIndex"
                                          :class="['px-2 py-0.5 border rounded-full text-xs', getCardTheme(index).accentBg, getCardTheme(index).borderColor, getCardTheme(index).textColor]">
                                        {{ seat.row_name }}{{ seat.seat_number }}
                                    </span>
                                </div>
                            </div>
                        </div>

                        <!-- Show Details -->
                        <div v-if="concert.shows && concert.shows.length > 0" class="mb-4">
                            <div class="flex items-center justify-between mb-3">
                                <p class="text-xs uppercase tracking-wider text-muted-foreground font-medium">Next Show</p>
                                <span v-if="concert.shows.length > 1" :class="['text-xs font-medium', getCardTheme(index).textColor]">
                                    +{{ concert.shows.length - 1 }} more
                                </span>
                            </div>

                            <!-- Next Show Card -->
                            <div class="bg-muted/30 rounded-lg p-4 border border-border">
                                <div class="flex justify-between items-center">
                                    <div class="flex items-center">
                                        <div :class="['rounded-lg p-2 mr-3', getCardTheme(index).accentBg]">
                                            <Clock :class="['h-4 w-4', getCardTheme(index).textColor]" />
                                        </div>
                                        <div>
                                            <div class="font-medium text-sm">{{ formatTime(getNextShowDate(concert)) }}</div>
                                            <div class="text-xs text-muted-foreground">{{ formatDate(getNextShowDate(concert)) }}</div>
                                        </div>
                                    </div>
                                    <div :class="['text-xs px-3 py-1 rounded-full font-medium', getCardTheme(index).accentBg, getCardTheme(index).textColor]">
                                        {{ concert.shows[0].end ? `${getShowDuration(concert.shows[0])} min` : 'Live' }}
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div v-else class="mb-4 p-4 bg-muted/30 rounded-lg border border-border text-center">
                            <p class="text-sm text-muted-foreground">No upcoming shows</p>
                        </div>

                        <!-- Action Button -->
                        <Link
                            :href="route('concerts.show', concert.id)"
                            :class="['block w-full text-center px-4 py-3 rounded-lg transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-ring focus:ring-offset-2 font-medium shadow-sm hover:shadow-md', getCardTheme(index).buttonBg, 'text-white hover:opacity-95']"
                        >
                            View Details
                        </Link>
                    </div>
                </div>
            </div>

            <!-- Empty State -->
            <div v-else class="bg-card rounded-lg p-8 text-center border border-border">
                <div class="inline-flex items-center justify-center w-12 h-12 rounded-full bg-muted mb-4">
                    <Music class="h-6 w-6 text-primary" />
                </div>
                <h3 class="text-lg font-medium mb-2">No Concerts Found</h3>
                <p class="text-muted-foreground mb-6">
                    {{ filters.location || filters.date || filters.artist || filters.search || filters.location_id || filters.artist_id ? 'No concerts match your filters.' : 'There are no upcoming concerts at the moment.' }}
                </p>
                <button
                    v-if="filters.location || filters.date || filters.artist || filters.search || filters.location_id || filters.artist_id"
                    @click="clearFilters"
                    class="px-4 py-2 bg-primary text-primary-foreground rounded-md hover:bg-primary/90 transition-colors focus:outline-none focus:ring-2 focus:ring-ring focus:ring-offset-2"
                >
                    Clear Filters
                </button>
            </div>
        </div>
    </AppSidebarLayout>
</template>
