<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head, Link } from '@inertiajs/vue3';
import { CalendarDays, Clock, MapPin, Ticket } from 'lucide-vue-next';

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Dashboard',
        href: '/dashboard',
    },
];

defineProps({
    reservations: {
        type: Array,
        default: () => []
    },
    bookings: {
        type: Array,
        default: () => []
    },
    upcomingShows: {
        type: Array,
        default: () => []
    }
});
</script>

<template>
    <Head title="Dashboard" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <!-- Dashboard Overview -->
        <div class="mb-8 p-4">
            <h1 class="text-2xl font-bold mb-2">Welcome Back, {{ $page.props.auth.user.name }}</h1>
            <p class="text-muted-foreground">Manage your reservations and tickets from your personal dashboard.</p>
        </div>

        <!-- Dashboard Grid -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 p-4">
            <!-- Main Content - Reservations and Tickets -->
            <div class="lg:col-span-2 space-y-6">
                <!-- Reservations Section - Only visible if there are reservations -->
                <div v-if="reservations && reservations.length > 0" class="bg-card rounded-xl shadow-sm">
                    <div class="p-4 border-b border-border flex justify-between items-center">
                        <h2 class="text-lg font-medium flex items-center">
                            <div class="h-5 w-5 mr-2 text-primary flex items-center justify-center">
                                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                            </div>
                            Active Reservations
                        </h2>
                    </div>

                    <div class="p-4">
                        <ul class="divide-y divide-border">
                            <li v-for="reservation in reservations" :key="reservation.id" class="py-4 first:pt-0 last:pb-0">
                                <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                                    <div>
                                        <h3 class="font-medium">{{ reservation.concert }}</h3>
                                        <div class="flex items-center text-sm text-muted-foreground mt-1">
                                            <CalendarDays class="h-4 w-4 mr-1" />
                                            <span>{{ reservation.show }}</span>
                                        </div>
                                        <div class="flex items-center text-sm text-muted-foreground mt-1">
                                            <MapPin class="h-4 w-4 mr-1" />
                                            <span>Seat: {{ reservation.seat }}</span>
                                        </div>
                                    </div>
                                    <div class="flex items-center gap-2">
                                        <div class="text-xs px-2 py-1 rounded-full bg-primary/10 text-primary">
                                            Reserved
                                        </div>
                                        <Link :href="route('reservations.createBooking', reservation.id)" class="px-3 py-1 text-sm rounded-md bg-primary text-primary-foreground hover:bg-primary/90">
                                            Complete Purchase
                                        </Link>
                                    </div>
                                </div>
                            </li>
                        </ul>
                    </div>
                </div>

                <!-- Tickets Section -->
                <div class="bg-card rounded-xl shadow-sm">
                    <div class="p-4 border-b border-border flex justify-between items-center">
                        <h2 class="text-lg font-medium flex items-center">
                            <Ticket class="h-5 w-5 mr-2 text-primary" />
                            Your Tickets
                        </h2>
                        <Link :href="route('bookings.index')" class="text-sm text-primary hover:underline">
                            View All
                        </Link>
                    </div>

                    <div class="p-4">
                        <div v-if="bookings && bookings.length > 0">
                            <ul class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <li v-for="booking in bookings" :key="booking.id" class="bg-muted/50 rounded-lg p-4 hover:bg-muted transition-colors">
                                    <div class="flex flex-col h-full">
                                        <div class="flex-1">
                                            <h3 class="font-medium">{{ booking.concert }}</h3>
                                            <div class="flex items-center text-sm text-muted-foreground mt-1">
                                                <CalendarDays class="h-4 w-4 mr-1" />
                                                <span>{{ booking.show }}</span>
                                            </div>
                                            <div class="flex items-center text-sm text-muted-foreground mt-1">
                                                <MapPin class="h-4 w-4 mr-1" />
                                                <span>Seat: {{ booking.seat }}</span>
                                            </div>
                                            <div class="mt-2 p-2 bg-secondary/50 rounded text-sm font-mono">
                                                {{ booking.code }}
                                            </div>
                                        </div>
                                        <div class="mt-4 pt-4 border-t border-border flex justify-between items-center">
                                            <span class="text-xs px-2 py-1 rounded-full bg-accent/10 text-accent">
                                                Confirmed
                                            </span>
                                            <Link :href="route('bookings.show', booking.booking_id)" class="text-sm text-primary hover:underline">
                                                View Details
                                            </Link>
                                        </div>
                                    </div>
                                </li>
                            </ul>
                        </div>
                        <div v-else class="py-8 text-center">
                            <div class="inline-flex items-center justify-center w-12 h-12 rounded-full bg-muted mb-4">
                                <Ticket class="h-6 w-6 text-muted-foreground" />
                            </div>
                            <h3 class="text-lg font-medium mb-1">No Tickets Found</h3>
                            <p class="text-muted-foreground mb-4">You haven't purchased any tickets yet.</p>
                            <Link :href="route('concerts.index')" class="px-4 py-2 rounded-md bg-primary text-primary-foreground hover:bg-primary/90">
                                Browse Concerts
                            </Link>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Sidebar - Upcoming Shows only -->
            <div>
                <!-- Upcoming Shows -->
                <div class="bg-card rounded-xl shadow-sm">
                    <div class="p-4 border-b border-border">
                        <h2 class="text-lg font-medium">Upcoming Shows</h2>
                    </div>
                    <div class="p-4">
                        <div v-if="upcomingShows && upcomingShows.length > 0">
                            <ul class="divide-y divide-border">
                                <li v-for="show in upcomingShows" :key="show.id" class="py-3 first:pt-0 last:pb-0">
                                    <h3 class="font-medium">{{ show.artist }}</h3>
                                    <div class="flex items-center text-sm text-muted-foreground mt-1">
                                        <Clock class="h-4 w-4 mr-1" />
                                        <span>{{ show.date }}</span>
                                    </div>
                                    <div class="flex items-center text-sm text-muted-foreground mt-1">
                                        <MapPin class="h-4 w-4 mr-1" />
                                        <span>{{ show.location }}</span>
                                    </div>
                                    <div class="mt-2">
                                        <Link :href="route('concerts.show', show.id)" class="text-sm text-primary hover:underline">
                                            View Details
                                        </Link>
                                    </div>
                                </li>
                            </ul>
                        </div>
                        <div v-else class="py-6 text-center">
                            <p class="text-muted-foreground mb-4">No upcoming shows at the moment.</p>
                            <Link :href="route('concerts.index')" class="text-sm text-primary hover:underline">
                                Check back later
                            </Link>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
