<script setup lang="ts">
import { Head } from '@inertiajs/vue3';
import AdminLayout from '@/layouts/AdminLayout.vue';
import {
    Users,
    Music,
    MapPin,
    Calendar,
    Ticket,
    Clock,
    TrendingUp,
    Activity
} from 'lucide-vue-next';

const props = defineProps<{
    stats: {
        total_users: number;
        total_artists: number;
        total_concerts: number;
        total_locations: number;
        total_shows: number;
        total_bookings: number;
        active_reservations: number;
        upcoming_shows: number;
    };
    recentBookings: Array<{
        id: number;
        user_name: string;
        user_email: string;
        tickets_count: number;
        concert_name: string;
        created_at: string;
    }>;
    upcomingShows: Array<{
        id: number;
        artist_name: string;
        location_name: string;
        start_date: string;
        tickets_sold: number;
        total_seats: number;
    }>;
}>();

const statCards = [
    { title: 'Total Users', value: props.stats.total_users, icon: Users, color: 'text-blue-600' },
    { title: 'artists', value: props.stats.total_artists, icon: Music, color: 'text-purple-600' },
    { title: 'Concerts', value: props.stats.total_concerts, icon: Calendar, color: 'text-green-600' },
    { title: 'Locations', value: props.stats.total_locations, icon: MapPin, color: 'text-orange-600' },
    { title: 'Total Shows', value: props.stats.total_shows, icon: Activity, color: 'text-indigo-600' },
    { title: 'Total Bookings', value: props.stats.total_bookings, icon: Ticket, color: 'text-emerald-600' },
    { title: 'Active Reservations', value: props.stats.active_reservations, icon: Clock, color: 'text-yellow-600' },
    { title: 'Upcoming Shows', value: props.stats.upcoming_shows, icon: TrendingUp, color: 'text-red-600' },
];
</script>

<template>
    <Head title="Admin Dashboard" />

    <AdminLayout>
        <div class="mb-8">
            <h1 class="text-2xl font-bold text-foreground">Admin Dashboard</h1>
            <p class="text-muted-foreground">Overview of your ticket management system</p>
        </div>

        <!-- Stats Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            <div
                v-for="stat in statCards"
                :key="stat.title"
                class="bg-card rounded-lg p-6 border border-border shadow-sm"
            >
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-muted-foreground">{{ stat.title }}</p>
                        <p class="text-2xl font-bold text-foreground">{{ stat.value.toLocaleString() }}</p>
                    </div>
                    <div class="p-3 rounded-full bg-muted">
                        <component :is="stat.icon" :class="['h-6 w-6', stat.color]" />
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent Activity -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <!-- Recent Bookings -->
            <div class="bg-card rounded-lg border border-border shadow-sm">
                <div class="p-6 border-b border-border">
                    <h2 class="text-lg font-semibold text-foreground">Recent Bookings</h2>
                </div>
                <div class="p-6">
                    <div v-if="recentBookings.length > 0" class="space-y-4">
                        <div
                            v-for="booking in recentBookings"
                            :key="booking.id"
                            class="flex items-center justify-between p-4 bg-muted/30 rounded-lg"
                        >
                            <div>
                                <p class="font-medium text-foreground">{{ booking.user_name }}</p>
                                <p class="text-sm text-muted-foreground">{{ booking.concert_name }}</p>
                                <p class="text-xs text-muted-foreground">{{ booking.created_at }}</p>
                            </div>
                            <div class="text-right">
                                <p class="text-sm font-medium text-foreground">{{ booking.tickets_count }} ticket{{ booking.tickets_count > 1 ? 's' : '' }}</p>
                            </div>
                        </div>
                    </div>
                    <div v-else class="text-center py-8">
                        <p class="text-muted-foreground">No recent bookings</p>
                    </div>
                </div>
            </div>

            <!-- Upcoming Shows -->
            <div class="bg-card rounded-lg border border-border shadow-sm">
                <div class="p-6 border-b border-border">
                    <h2 class="text-lg font-semibold text-foreground">Upcoming Shows</h2>
                </div>
                <div class="p-6">
                    <div v-if="upcomingShows.length > 0" class="space-y-4">
                        <div
                            v-for="show in upcomingShows"
                            :key="show.id"
                            class="flex items-center justify-between p-4 bg-muted/30 rounded-lg"
                        >
                            <div>
                                <p class="font-medium text-foreground">{{ show.artist_name }}</p>
                                <p class="text-sm text-muted-foreground">{{ show.location_name }}</p>
                                <p class="text-xs text-muted-foreground">{{ show.start_date }}</p>
                            </div>
                            <div class="text-right">
                                <p class="text-sm font-medium text-foreground">{{ show.tickets_sold }}/{{ show.total_seats }}</p>
                                <p class="text-xs text-muted-foreground">tickets sold</p>
                            </div>
                        </div>
                    </div>
                    <div v-else class="text-center py-8">
                        <p class="text-muted-foreground">No upcoming shows</p>
                    </div>
                </div>
            </div>
        </div>
    </AdminLayout>
</template>
