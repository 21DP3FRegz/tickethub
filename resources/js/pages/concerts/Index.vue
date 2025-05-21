<script setup lang="ts">
import { Head, Link, router } from '@inertiajs/vue3';
import AppSidebarLayout from '@/layouts/app/AppSidebarLayout.vue';
import { ref, watch } from 'vue';
import { CalendarDays, MapPin, Music, Search, X, User } from 'lucide-vue-next';

const props = defineProps<{
    concerts: Array<{
        id: number;
        artist: string;
        location: { name: string };
        shows: Array<{ start: string }>;
    }>;
    filters: { location?: string; date?: string; artist?: string };
}>();

// Create reactive filter state
const filterLocation = ref(props.filters.location || '');
const filterDate = ref(props.filters.date || '');
const filterArtist = ref(props.filters.artist || '');

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

// Apply filters
const applyFilters = () => {
    router.get(route('concerts.index'), {
        location: filterLocation.value || undefined,
        date: filterDate.value || undefined,
        artist: filterArtist.value || undefined
    }, {
        preserveState: true,
        replace: true
    });
};

// Clear all filters
const clearFilters = () => {
    filterLocation.value = '';
    filterDate.value = '';
    filterArtist.value = '';
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
</script>

<template>
    <Head title="Concerts" />
    <AppSidebarLayout :breadcrumbs="[{ title: 'Concerts', href: '/concerts' }]">
        <div class="p-6">
            <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-8">
                <h1 class="text-2xl font-bold mb-2 md:mb-0">Upcoming Concerts</h1>

                <div class="flex items-center space-x-2">
                    <span class="text-sm text-muted-foreground">
                        {{ concerts.length }} {{ concerts.length === 1 ? 'concert' : 'concerts' }} found
                    </span>
                </div>
            </div>

            <!-- Filter Form -->
            <div class="bg-card rounded-lg p-4 mb-8 shadow-sm">
                <div class="flex items-center mb-4">
                    <Search class="h-5 w-5 text-muted-foreground mr-2" />
                    <h2 class="text-lg font-medium">Filter Concerts</h2>
                </div>

                <form @submit.prevent="applyFilters" class="space-y-4 md:space-y-0 md:flex md:items-end md:gap-4">
                    <div class="flex-1">
                        <label for="artist" class="block text-sm font-medium text-muted-foreground mb-1">
                            Artist
                        </label>
                        <div class="relative">
                            <User class="absolute left-3 top-1/2 -translate-y-1/2 h-4 w-4 text-muted-foreground" />
                            <input
                                id="artist"
                                v-model="filterArtist"
                                placeholder="Filter by artist"
                                class="w-full pl-10 pr-3 py-2 bg-background border border-input rounded-md focus:outline-none focus:ring-2 focus:ring-primary focus:border-primary"
                                @keydown="handleKeyDown"
                            />
                        </div>
                    </div>

                    <div class="flex-1">
                        <label for="location" class="block text-sm font-medium text-muted-foreground mb-1">
                            Location
                        </label>
                        <div class="relative">
                            <MapPin class="absolute left-3 top-1/2 -translate-y-1/2 h-4 w-4 text-muted-foreground" />
                            <input
                                id="location"
                                v-model="filterLocation"
                                placeholder="Filter by location"
                                class="w-full pl-10 pr-3 py-2 bg-background border border-input rounded-md focus:outline-none focus:ring-2 focus:ring-primary focus:border-primary"
                                @keydown="handleKeyDown"
                            />
                        </div>
                    </div>

                    <div class="flex-1">
                        <label for="date" class="block text-sm font-medium text-muted-foreground mb-1">
                            Date
                        </label>
                        <div class="relative">
                            <CalendarDays class="absolute left-3 top-1/2 -translate-y-1/2 h-4 w-4 text-muted-foreground" />
                            <input
                                id="date"
                                v-model="filterDate"
                                type="date"
                                class="w-full pl-10 pr-3 py-2 bg-background border border-input rounded-md focus:outline-none focus:ring-2 focus:ring-primary focus:border-primary"
                                @keydown="handleKeyDown"
                            />
                        </div>
                    </div>

                    <div class="flex gap-2">
                        <button
                            type="submit"
                            class="px-4 py-2 bg-primary text-primary-foreground rounded-md hover:bg-primary/90 focus:outline-none focus:ring-2 focus:ring-primary focus:ring-offset-2"
                        >
                            Apply Filters
                        </button>

                        <button
                            type="button"
                            @click="clearFilters"
                            class="px-4 py-2 bg-secondary text-secondary-foreground rounded-md hover:bg-secondary/90 focus:outline-none focus:ring-2 focus:ring-secondary focus:ring-offset-2"
                        >
                            <X class="h-4 w-4" />
                        </button>
                    </div>
                </form>

                <!-- Active Filters -->
                <div v-if="filters.location || filters.date || filters.artist" class="mt-4 flex flex-wrap gap-2">
                    <div v-if="filters.artist" class="inline-flex items-center bg-secondary/50 text-secondary-foreground px-3 py-1 rounded-full text-sm">
                        <span class="mr-1">Artist:</span>
                        <span class="font-medium">{{ filters.artist }}</span>
                        <button
                            @click="() => { filterArtist = ''; applyFilters(); }"
                            class="ml-2 text-secondary-foreground/70 hover:text-secondary-foreground"
                        >
                            <X class="h-3 w-3" />
                        </button>
                    </div>

                    <div v-if="filters.location" class="inline-flex items-center bg-secondary/50 text-secondary-foreground px-3 py-1 rounded-full text-sm">
                        <span class="mr-1">Location:</span>
                        <span class="font-medium">{{ filters.location }}</span>
                        <button
                            @click="() => { filterLocation = ''; applyFilters(); }"
                            class="ml-2 text-secondary-foreground/70 hover:text-secondary-foreground"
                        >
                            <X class="h-3 w-3" />
                        </button>
                    </div>

                    <div v-if="filters.date" class="inline-flex items-center bg-secondary/50 text-secondary-foreground px-3 py-1 rounded-full text-sm">
                        <span class="mr-1">Date:</span>
                        <span class="font-medium">{{ filters.date }}</span>
                        <button
                            @click="() => { filterDate = ''; applyFilters(); }"
                            class="ml-2 text-secondary-foreground/70 hover:text-secondary-foreground"
                        >
                            <X class="h-3 w-3" />
                        </button>
                    </div>
                </div>
            </div>

            <!-- Concert List -->
            <div v-if="concerts.length > 0" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                <div
                    v-for="concert in concerts"
                    :key="concert.id"
                    class="bg-card rounded-lg overflow-hidden shadow-sm hover:shadow-md transition-shadow border border-border"
                >
                    <div class="p-6">
                        <div class="flex items-start justify-between mb-4">
                            <div class="flex-1">
                                <h2 class="text-xl font-bold truncate">{{ concert.artist }}</h2>
                                <div class="flex items-center text-sm text-muted-foreground mt-1">
                                    <MapPin class="h-4 w-4 mr-1 flex-shrink-0" />
                                    <span class="truncate">{{ concert.location.name }}</span>
                                </div>
                            </div>
                            <div class="w-10 h-10 rounded-full bg-primary/10 flex items-center justify-center flex-shrink-0">
                                <Music class="h-5 w-5 text-primary" />
                            </div>
                        </div>

                        <div class="space-y-2 mb-4">
                            <div v-if="concert.shows && concert.shows.length > 0" class="space-y-1">
                                <p class="text-xs uppercase tracking-wider text-muted-foreground font-medium">Next Show</p>
                                <div class="flex items-center text-sm">
                                    <CalendarDays class="h-4 w-4 mr-1 text-muted-foreground" />
                                    <span>{{ formatDate(concert.shows[0]?.start) }}</span>
                                </div>
                                <p v-if="concert.shows.length > 1" class="text-xs text-muted-foreground">
                                    +{{ concert.shows.length - 1 }} more show{{ concert.shows.length > 2 ? 's' : '' }}
                                </p>
                            </div>
                            <div v-else class="text-sm text-muted-foreground">
                                No upcoming shows
                            </div>
                        </div>

                        <Link
                            :href="route('concerts.show', concert.id)"
                            class="block w-full text-center px-4 py-2 bg-primary text-primary-foreground rounded-md hover:bg-primary/90 transition-colors"
                        >
                            View Details
                        </Link>
                    </div>
                </div>
            </div>

            <!-- Empty State -->
            <div v-else class="bg-card rounded-lg p-8 text-center">
                <div class="inline-flex items-center justify-center w-12 h-12 rounded-full bg-muted mb-4">
                    <Music class="h-6 w-6 text-muted-foreground" />
                </div>
                <h3 class="text-lg font-medium mb-2">No Concerts Found</h3>
                <p class="text-muted-foreground mb-6">
                    {{ filters.location || filters.date || filters.artist ? 'No concerts match your filters.' : 'There are no upcoming concerts at the moment.' }}
                </p>
                <button
                    v-if="filters.location || filters.date || filters.artist"
                    @click="clearFilters"
                    class="px-4 py-2 bg-primary text-primary-foreground rounded-md hover:bg-primary/90"
                >
                    Clear Filters
                </button>
            </div>
        </div>
    </AppSidebarLayout>
</template>
