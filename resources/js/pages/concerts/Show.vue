<script setup lang="ts">
import { Head, Link, useForm } from '@inertiajs/vue3';
import AppSidebarLayout from '@/layouts/app/AppSidebarLayout.vue';
import { Button } from '@/Components/ui/button';
import { Dialog, DialogContent, DialogHeader, DialogTitle, DialogTrigger } from '@/Components/ui/dialog';
import SeatStatusIndicator from '@/components/SeatStatusIndicator.vue';
import { ref, onMounted, computed } from 'vue';
import { CalendarDays, MapPin, Music, Clock, Ticket, X, Calendar, AlertCircle } from 'lucide-vue-next';

const props = defineProps<{
    concert: {
        id: number;
        artist: string | { name: string };
        location: { name: string };
        shows: Array<{
            id: number;
            start: string;
            end: string;
            seats: Array<{
                id: number;
                seat_number: string;
                row_id: number;
                reservation: null | { id: number };
                ticket: null | { id: number };
                status?: string;
            }>;
            rows: Array<{
                id: number;
                name: string;
                order: number;
            }>;
        }>;
    };
    userBookings?: {
        concert_id: number;
        concert_name: string;
        shows: Array<{
            show_id: number;
            show_date: string;
            booking_id: number;
            booking_date: string;
            seats: Array<{
                id: number;
                seat_number: string;
                row_name: string;
                ticket_id: number;
                ticket_code: string;
            }>;
        }>;
    };
}>();

const selectedShow = ref<number | null>(null);
const form = useForm<{
    show_id: number | null;
    seat_ids: number[];
}>({
    show_id: null,
    seat_ids: [],
});
const error = ref('');

// Add a function to get the artist name
const getArtistName = (concert) => {
    if (typeof concert.artist === 'object' && concert.artist !== null) {
        return concert.artist.name;
    }
    return concert.artist; // Fallback to the string if it's not an object
};

onMounted(() => {
    console.log('Component mounted with concert data:', props.concert);
    if (props.concert.shows) {
        console.log('Number of shows:', props.concert.shows.length);
        props.concert.shows.forEach(show => {
            console.log(`Show ID ${show.id} has ${show.seats?.length || 0} seats and ${show.rows?.length || 0} rows`);
        });
    }

    if (props.userBookings) {
        console.log('User has bookings for this concert:', props.userBookings);
    }
});

const openSeatMap = (showId: number) => {
    console.log('Opening seat map for show ID:', showId);
    selectedShow.value = showId;
    // Reset selection when opening a new seat map
    form.show_id = showId;
    form.seat_ids = [];
    error.value = '';
};

const toggleSeatSelection = (showId: number, seatId: number) => {
    console.log('Toggling seat selection:', { showId, seatId });
    form.show_id = showId;

    // If seat is already selected, remove it
    const seatIndex = form.seat_ids.indexOf(seatId);
    if (seatIndex !== -1) {
        form.seat_ids.splice(seatIndex, 1);
    } else {
        // Add the seat to selection
        form.seat_ids.push(seatId);
    }

    console.log('Form after selection:', form);
};

const isSeatSelected = (seatId: number) => {
    return form.seat_ids.includes(seatId);
};

const reserveSeats = () => {
    if (form.seat_ids.length === 0) {
        error.value = 'Please select at least one seat.';
        return;
    }

    console.log('Attempting to reserve seats with form data:', form);
    form.post(route('reservations.store'), {
        onError: (errors) => {
            console.error('Reservation error:', errors);
            error.value = errors.seat || errors.seat_ids || errors.show || 'An error occurred.';
        },
        onSuccess: () => {
            console.log('Reservation successful!');
            selectedShow.value = null; // Close modal
        },
    });
};

// Clear selected seats
const clearSelection = () => {
    form.seat_ids = [];
};

const formatDate = (dateString: string) => {
    const date = new Date(dateString);
    return {
        day: date.getDate(),
        month: date.toLocaleString('default', { month: 'short' }),
        year: date.getFullYear(),
        time: date.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' }),
        full: date.toLocaleString()
    };
};

const getAvailableSeatsCount = (show: any) => {
    if (!show.seats) return 0;
    return show.seats.filter(seat => !seat.reservation && !seat.ticket).length;
};

const getDuration = (start: string, end: string) => {
    const startTime = new Date(start).getTime();
    const endTime = new Date(end).getTime();
    return Math.round((endTime - startTime) / (1000 * 60));
};

// Get sorted rows for a show
const getSortedRows = (show: any) => {
    if (!show || !show.rows) return [];
    return [...show.rows].sort((a, b) => a.order - b.order);
};

// Check if a seat is available (not reserved and not ticketed)
const isSeatAvailable = (seat: any) => {
    return !seat.reservation && !seat.ticket;
};

// Sort seats numerically by seat number
const getSortedSeats = (show: any, rowId: number) => {
    if (!show || !show.seats) return [];

    return show.seats
        .filter(seat => seat.row_id === rowId)
        .sort((a, b) => {
            // Convert seat numbers to integers for proper numeric sorting
            const seatNumA = parseInt(a.seat_number);
            const seatNumB = parseInt(b.seat_number);

            // If both are valid numbers, sort numerically
            if (!isNaN(seatNumA) && !isNaN(seatNumB)) {
                return seatNumA - seatNumB;
            }

            // Fallback to string comparison if not valid numbers
            return a.seat_number.localeCompare(b.seat_number);
        });
};

// Get current show
const currentShow = computed(() => {
    if (!selectedShow.value) return null;
    return props.concert.shows.find(show => show.id === selectedShow.value);
});

// Get selected seats information for display
const selectedSeats = computed(() => {
    if (!currentShow.value) return [];

    return form.seat_ids.map(seatId => {
        const seat = currentShow.value?.seats.find(s => s.id === seatId);
        return {
            id: seatId,
            number: seat?.seat_number || '',
            rowId: seat?.row_id
        };
    }).sort((a, b) => {
        // First sort by row
        if (a.rowId !== b.rowId) {
            return a.rowId - b.rowId;
        }

        // Then sort by seat number
        const numA = parseInt(a.number);
        const numB = parseInt(b.number);
        if (!isNaN(numA) && !isNaN(numB)) {
            return numA - numB;
        }
        return a.number.localeCompare(b.number);
    });
});

// Get row name by ID
const getRowName = (rowId: number) => {
    if (!currentShow.value) return '';
    const row = currentShow.value.rows.find(r => r.id === rowId);
    return row ? row.name : '';
};

// Check if user already has tickets for a show
const userHasTicketsForShow = (showId: number) => {
    if (!props.userBookings) return false;
    return props.userBookings.shows.some(show => show.show_id === showId);
};

// Get user's booked seats for a show
const getUserBookedSeatsForShow = (showId: number) => {
    if (!props.userBookings) return [];
    const show = props.userBookings.shows.find(show => show.show_id === showId);
    return show ? show.seats : [];
};
</script>

<template>
    <Head :title="getArtistName(concert)" />
    <AppSidebarLayout :breadcrumbs="[{ title: 'Concerts', href: '/concerts' }, { title: getArtistName(concert) }]">
        <!-- Header Section -->
        <div class="mb-8 p-4">
            <h1 class="text-2xl font-bold mb-2">{{ getArtistName(concert) }}</h1>
            <p class="text-muted-foreground flex items-center">
                <MapPin class="h-4 w-4 mr-1" />
                {{ concert.location.name }}
            </p>
        </div>

        <!-- User's Bookings Section -->
        <div v-if="userBookings" class="p-4 mb-6">
            <div class="bg-secondary/30 border border-secondary rounded-xl p-4">
                <h2 class="text-lg font-medium flex items-center text-secondary-foreground mb-3">
                    <Ticket class="h-5 w-5 mr-2 text-accent" />
                    Your Tickets for {{ getArtistName(concert) }}
                </h2>

                <div v-for="(show, index) in userBookings.shows" :key="index" class="mb-4 last:mb-0">
                    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-2">
                        <div>
                            <h3 class="font-medium text-foreground">{{ show.show_date }}</h3>
                            <p class="text-sm text-muted-foreground">Booked on {{ show.booking_date }}</p>

                            <div class="flex flex-wrap gap-1 mt-2">
                                <span v-for="(seat, seatIndex) in show.seats" :key="seatIndex"
                                      class="px-2 py-1 bg-secondary border border-secondary/50 rounded-full text-xs text-secondary-foreground">
                                    {{ seat.row_name }}{{ seat.seat_number }}
                                </span>
                            </div>
                        </div>

                        <Link :href="route('bookings.show', show.booking_id)"
                              class="px-3 py-1 text-sm rounded-md bg-accent text-accent-foreground hover:bg-accent/90 transition-colors inline-flex items-center focus:outline-none focus:ring-2 focus:ring-ring focus:ring-offset-2">
                            <Ticket class="h-4 w-4 mr-1" />
                            View Tickets
                        </Link>
                    </div>
                </div>
            </div>
        </div>

        <!-- Main Content -->
        <div class="p-4">
            <div class="bg-card rounded-xl shadow-sm mb-6 border border-border">
                <div class="p-4 border-b border-border flex justify-between items-center">
                    <h2 class="text-lg font-medium flex items-center">
                        <Music class="h-5 w-5 mr-2 text-primary" />
                        Available Shows
                    </h2>
                </div>

                <div class="p-4">
                    <div v-if="!concert.shows || concert.shows.length === 0" class="py-8 text-center">
                        <div class="inline-flex items-center justify-center w-12 h-12 rounded-full bg-muted mb-4">
                            <CalendarDays class="h-6 w-6 text-primary" />
                        </div>
                        <h3 class="text-lg font-medium mb-1">No Upcoming Shows</h3>
                        <p class="text-muted-foreground mb-4">There are no upcoming shows available for this concert.</p>
                        <Link :href="route('concerts.index')" class="px-4 py-2 rounded-md bg-primary text-primary-foreground hover:bg-primary/90 transition-colors focus:outline-none focus:ring-2 focus:ring-ring focus:ring-offset-2">
                            Browse Other Concerts
                        </Link>
                    </div>
                    <div v-else class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                        <div v-for="show in concert.shows" :key="show.id" class="bg-muted/30 rounded-lg overflow-hidden border border-border hover:border-primary/30 transition-colors">
                            <!-- Date Header -->
                            <div class="bg-primary/10 p-3 border-b border-border">
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center">
                                        <Calendar class="h-5 w-5 text-primary mr-2" />
                                        <span class="font-medium">{{ formatDate(show.start).month }} {{ formatDate(show.start).day }}, {{ formatDate(show.start).year }}</span>
                                    </div>
                                    <div class="text-xs px-2 py-1 rounded-full bg-primary/10 text-primary">
                                        {{ getAvailableSeatsCount(show) }} seats
                                    </div>
                                </div>
                            </div>

                            <!-- Show Details -->
                            <div class="p-4">
                                <!-- Time -->
                                <div class="mb-4">
                                    <div class="text-2xl font-bold text-foreground">
                                        {{ formatDate(show.start).time }}
                                    </div>
                                    <div class="flex items-center text-sm text-muted-foreground mt-1">
                                        <Clock class="h-4 w-4 mr-1" />
                                        <span>{{ getDuration(show.start, show.end) }} min</span>
                                    </div>
                                </div>

                                <!-- Location -->
                                <div class="mb-4 pb-4 border-b border-border">
                                    <div class="flex items-center text-sm">
                                        <MapPin class="h-4 w-4 mr-1 text-primary" />
                                        <span>{{ concert.location.name }}</span>
                                    </div>
                                </div>

                                <!-- User's Booked Seats for this Show -->
                                <div v-if="userHasTicketsForShow(show.id)" class="mb-4 p-2 bg-secondary/30 border border-secondary rounded-md">
                                    <div class="flex items-center text-secondary-foreground mb-1">
                                        <Ticket class="h-4 w-4 mr-1" />
                                        <span class="font-medium text-sm">Your Booked Seats</span>
                                    </div>
                                    <div class="flex flex-wrap gap-1 mt-1">
                                        <span v-for="(seat, seatIndex) in getUserBookedSeatsForShow(show.id)" :key="seatIndex"
                                              class="px-2 py-0.5 bg-secondary border border-secondary/50 rounded-full text-xs text-secondary-foreground">
                                            {{ seat.row_name }}{{ seat.seat_number }}
                                        </span>
                                    </div>
                                </div>

                                <!-- Action Button -->
                                <div v-if="userHasTicketsForShow(show.id)" class="text-center">
                                    <div class="p-3 bg-secondary/30 border border-secondary rounded-md mb-3">
                                        <div class="flex items-center text-secondary-foreground">
                                            <AlertCircle class="h-4 w-4 mr-1 flex-shrink-0" />
                                            <span class="text-sm">You already have tickets for this show</span>
                                        </div>
                                    </div>
                                    <Link :href="route('bookings.index')" class="w-full inline-block text-center px-4 py-2 bg-accent text-accent-foreground rounded-md hover:bg-accent/90 transition-colors focus:outline-none focus:ring-2 focus:ring-ring focus:ring-offset-2">
                                        <Ticket class="h-4 w-4 mr-1 inline-block" />
                                        View Your Bookings
                                    </Link>
                                </div>
                                <Dialog v-else>
                                    <DialogTrigger asChild>
                                        <Button
                                            @click="openSeatMap(show.id)"
                                            class="w-full bg-primary text-primary-foreground hover:bg-primary/90 transition-colors focus:outline-none focus:ring-2 focus:ring-ring focus:ring-offset-2"
                                        >
                                            <Ticket class="h-4 w-4 mr-2" />
                                            Select Seats
                                        </Button>
                                    </DialogTrigger>
                                    <DialogContent class="sm:max-w-[700px]">
                                        <DialogHeader>
                                            <DialogTitle>Seat Selection</DialogTitle>
                                        </DialogHeader>
                                        <div class="mt-4">
                                            <div class="mb-4">
                                                <h3 class="font-medium mb-1">{{ getArtistName(concert) }}</h3>
                                                <p class="text-sm text-muted-foreground">{{ formatDate(show.start).full }}</p>
                                                <p class="text-sm text-muted-foreground">{{ concert.location.name }}</p>
                                            </div>

                                            <div v-if="!show.seats || show.seats.length === 0" class="text-center py-4">
                                                <p class="text-muted-foreground">No seats available for this show.</p>
                                            </div>
                                            <template v-else>
                                                <!-- Stage Area -->
                                                <div class="mb-6 relative">
                                                    <div class="w-full h-12 bg-primary/20 rounded-t-xl flex items-center justify-center relative overflow-hidden">
                                                        <div class="absolute inset-0 bg-gradient-to-b from-primary/30 to-transparent"></div>
                                                        <div class="relative z-10 font-semibold text-primary uppercase tracking-wider">Stage</div>
                                                    </div>
                                                    <div class="w-full h-3 bg-primary/40 rounded-b-3xl"></div>
                                                </div>

                                                <!-- Seating Area -->
                                                <div class="overflow-x-auto pb-2">
                                                    <div class="min-w-max">
                                                        <div v-for="row in getSortedRows(show)" :key="row.id" class="mb-2 flex items-center">
                                                            <!-- Row Label -->
                                                            <div class="w-10 text-center font-medium text-sm mr-2">
                                                                {{ row.name }}
                                                            </div>

                                                            <!-- Seats in Row -->
                                                            <div class="flex gap-1">
                                                                <button
                                                                    v-for="seat in getSortedSeats(show, row.id)"
                                                                    :key="seat.id"
                                                                    :disabled="!isSeatAvailable(seat) || seat.status === 'your-booking'"
                                                                    @click="isSeatAvailable(seat) && seat.status !== 'your-booking' && toggleSeatSelection(show.id, seat.id)"
                                                                    class="focus:outline-none focus:ring-2 focus:ring-ring focus:ring-offset-1 disabled:opacity-100"
                                                                >
                                                                    <SeatStatusIndicator
                                                                        :seat="seat"
                                                                        :isSelected="isSeatSelected(seat.id)"
                                                                    />
                                                                </button>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>

                                                <!-- Selected Seats Display with Clear Button -->
                                                <div v-if="form.seat_ids.length > 0" class="flex items-center justify-between mb-4">
                                                    <div class="flex-1">
                                                        <div class="flex flex-wrap gap-2">
                                                            <div
                                                                v-for="seat in selectedSeats"
                                                                :key="seat.id"
                                                                class="px-2 py-1 bg-primary/20 rounded text-xs flex items-center"
                                                            >
                                                                <span>{{ getRowName(seat.rowId) }}{{ seat.number }}</span>
                                                                <button
                                                                    @click="toggleSeatSelection(show.id, seat.id)"
                                                                    class="ml-1 text-primary/70 hover:text-primary transition-colors"
                                                                >
                                                                    <X class="h-3 w-3" />
                                                                </button>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <button
                                                        @click="clearSelection"
                                                        class="text-sm text-muted-foreground hover:text-primary flex items-center ml-2 transition-colors"
                                                    >
                                                        <X class="h-3 w-3 mr-1" />
                                                        <span>Clear all</span>
                                                    </button>
                                                </div>

                                                <!-- Legend -->
                                                <div class="mb-4 p-3 bg-muted rounded-md">
                                                    <div class="flex flex-wrap justify-between gap-2">
                                                        <div class="flex items-center">
                                                            <div class="w-4 h-4 bg-primary rounded mr-2"></div>
                                                            <span class="text-xs">Available</span>
                                                        </div>
                                                        <div class="flex items-center">
                                                            <div class="w-4 h-4 bg-muted-foreground/30 rounded mr-2"></div>
                                                            <span class="text-xs">Booked</span>
                                                        </div>
                                                        <div class="flex items-center">
                                                            <div class="w-4 h-4 bg-secondary rounded mr-2"></div>
                                                            <span class="text-xs">Reserved</span>
                                                        </div>
                                                        <div class="flex items-center">
                                                            <div class="w-4 h-4 bg-accent rounded mr-2"></div>
                                                            <span class="text-xs">Your Booking</span>
                                                        </div>
                                                        <div class="flex items-center">
                                                            <div class="w-4 h-4 bg-primary/70 ring-2 ring-primary rounded mr-2"></div>
                                                            <span class="text-xs">Selected</span>
                                                        </div>
                                                    </div>
                                                </div>

                                                <!-- Selection Summary & Action Area -->
                                                <div class="mt-6 space-y-4">
                                                    <!-- Error Message -->
                                                    <div v-if="error" class="p-3 bg-destructive/10 border border-destructive/30 rounded-md text-destructive text-sm">
                                                        {{ error }}
                                                    </div>

                                                    <!-- Reserve Button -->
                                                    <Button
                                                        @click="reserveSeats"
                                                        class="w-full bg-primary text-primary-foreground hover:bg-primary/90 transition-colors focus:outline-none focus:ring-2 focus:ring-ring focus:ring-offset-2"
                                                        :disabled="form.processing || form.seat_ids.length === 0"
                                                    >
                                                        <Ticket class="h-4 w-4 mr-2" />
                                                        {{ form.seat_ids.length === 0 ? 'Select Seats to Reserve' :
                                                        form.seat_ids.length === 1 ? 'Reserve Selected Seat' :
                                                            `Reserve ${form.seat_ids.length} Selected Seats` }}
                                                    </Button>
                                                </div>
                                            </template>
                                        </div>
                                    </DialogContent>
                                </Dialog>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="flex justify-between items-center">
                <Link :href="route('concerts.index')" class="text-primary hover:underline flex items-center transition-colors focus:outline-none focus:ring-2 focus:ring-ring rounded-md px-2 py-1">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="mr-1"><path d="m15 18-6-6 6-6"/></svg>
                    Back to Concerts
                </Link>
            </div>
        </div>
    </AppSidebarLayout>
</template>
