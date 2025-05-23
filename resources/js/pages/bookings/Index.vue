<script setup lang="ts">
import { Head, Link, useForm, router } from '@inertiajs/vue3';
import AppSidebarLayout from '@/layouts/app/AppSidebarLayout.vue';
import { Button } from '@/Components/ui/button';
import { CalendarDays, MapPin, Ticket, User, Clock, AlertCircle, QrCode, ArrowRight } from 'lucide-vue-next';

const props = defineProps<{
    bookings: Array<{
        id: number;
        name: string;
        tickets: Array<{
            id: number;
            code: string;
            name: string;
            show: { id: number; start: string; concert: { artist: { name: string; } } };
            seat: { seat_number: string };
        }>;
    }>;
}>();

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

// Navigate to specific ticket details
const viewTicketDetails = (bookingId: number, ticketId: number) => {
    router.visit(`/bookings/${bookingId}?ticket=${ticketId}`);
};
</script>

<template>
    <Head title="Your Tickets" />
    <AppSidebarLayout :breadcrumbs="[{ title: 'Tickets', href: '/bookings' }]">
        <!-- Header Section -->
        <div class="mb-8 p-4">
            <h1 class="text-2xl font-bold mb-2">Your Tickets</h1>
            <p class="text-muted-foreground">Manage your purchased tickets for upcoming events.</p>
        </div>

        <!-- Main Content -->
        <div class="p-4">
            <div v-if="bookings.length" class="space-y-6">
                <div v-for="booking in bookings" :key="booking.id" class="bg-card rounded-xl shadow-sm">
                    <!-- Booking Header -->
                    <div class="p-4 border-b border-border">
                        <h2 class="text-lg font-medium flex items-center">
                            <User class="h-5 w-5 mr-2 text-primary" />
                            Booking for {{ booking.name }}
                        </h2>
                    </div>

                    <!-- Tickets -->
                    <div class="p-4">
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
                            <div
                                v-for="ticket in booking.tickets"
                                :key="ticket.id"
                                @click="viewTicketDetails(booking.id, ticket.id)"
                                class="bg-muted/30 rounded-lg overflow-hidden border border-border p-3 cursor-pointer hover:border-primary/50 hover:shadow-sm transition-all"
                            >
                                <!-- Event Header -->
                                <div class="font-medium truncate mb-2">{{ ticket.show.concert.artist.name }}</div>

                                <!-- Ticket Details -->
                                <div class="space-y-2 text-sm">
                                    <!-- Date & Time -->
                                    <div class="flex items-center text-muted-foreground">
                                        <CalendarDays class="h-4 w-4 mr-1" />
                                        <span>{{ formatDate(ticket.show.start).month }} {{ formatDate(ticket.show.start).day }}</span>
                                    </div>
                                    <div class="flex items-center text-muted-foreground">
                                        <Clock class="h-4 w-4 mr-1" />
                                        <span>{{ formatDate(ticket.show.start).time }}</span>
                                    </div>

                                    <!-- Seat -->
                                    <div class="flex items-center">
                                        <MapPin class="h-4 w-4 mr-1 text-primary" />
                                        <span>Seat: {{ ticket.seat.seat_number }}</span>
                                    </div>

                                    <!-- View Details Indicator -->
                                    <div class="flex justify-end text-xs text-primary mt-2">
                                        <span class="flex items-center">
                                            View Details
                                            <ArrowRight class="h-3 w-3 ml-1" />
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Empty State -->
            <div v-else class="bg-card rounded-xl shadow-sm p-8 text-center">
                <div class="inline-flex items-center justify-center w-12 h-12 rounded-full bg-muted mb-4">
                    <Ticket class="h-6 w-6 text-muted-foreground" />
                </div>
                <h3 class="text-lg font-medium mb-1">No Tickets Found</h3>
                <p class="text-muted-foreground mb-4">You haven't purchased any tickets yet.</p>
                <Link href="/concerts" class="px-4 py-2 rounded-md bg-primary text-primary-foreground hover:bg-primary/90">
                    Browse Concerts
                </Link>
            </div>
        </div>
    </AppSidebarLayout>
</template>
