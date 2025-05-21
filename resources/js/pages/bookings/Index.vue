<script setup lang="ts">
import { Head, Link, useForm, router } from '@inertiajs/vue3';
import AppSidebarLayout from '@/layouts/app/AppSidebarLayout.vue';
import { Button } from '@/Components/ui/button';
import { CalendarDays, MapPin, Ticket, User, Clock, AlertCircle, QrCode } from 'lucide-vue-next';

const props = defineProps<{
    bookings: Array<{
        id: number;
        name: string;
        tickets: Array<{
            id: number;
            code: string;
            name: string;
            show: { id: number; start: string; concert: { artist: string } };
            seat: { seat_number: string };
        }>;
    }>;
}>();

const form = useForm({});

const cancelBooking = (bookingId: number) => {
    if (confirm('Are you sure you want to cancel this booking? This action cannot be undone.')) {
        router.delete(route('bookings.destroy', bookingId), {
            onSuccess: () => form.reset(),
        });
    }
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
            <div v-if="bookings.length" class="space-y-8">
                <div v-for="booking in bookings" :key="booking.id" class="bg-card rounded-xl shadow-sm">
                    <!-- Booking Header -->
                    <div class="p-4 border-b border-border flex justify-between items-center">
                        <h2 class="text-lg font-medium flex items-center">
                            <User class="h-5 w-5 mr-2 text-primary" />
                            Booking for {{ booking.name }}
                        </h2>
                        <Button
                            @click="cancelBooking(booking.id)"
                            variant="outline"
                            size="sm"
                            class="border-destructive text-destructive hover:bg-destructive/10"
                            :disabled="form.processing"
                        >
                            Cancel Booking
                        </Button>
                    </div>

                    <!-- Tickets -->
                    <div class="p-4">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div
                                v-for="ticket in booking.tickets"
                                :key="ticket.id"
                                class="bg-muted/30 rounded-lg overflow-hidden border border-border hover:border-primary/30 transition-colors"
                            >
                                <!-- Event Header -->
                                <div class="bg-primary/10 p-3 border-b border-border">
                                    <div class="font-medium truncate">{{ ticket.show.concert.artist }}</div>
                                </div>

                                <!-- Ticket Details -->
                                <div class="p-4">
                                    <!-- Date & Time -->
                                    <div class="mb-4">
                                        <div class="flex items-center text-sm text-muted-foreground mb-1">
                                            <CalendarDays class="h-4 w-4 mr-1" />
                                            <span>{{ formatDate(ticket.show.start).month }} {{ formatDate(ticket.show.start).day }}, {{ formatDate(ticket.show.start).year }}</span>
                                        </div>
                                        <div class="flex items-center text-sm text-muted-foreground">
                                            <Clock class="h-4 w-4 mr-1" />
                                            <span>{{ formatDate(ticket.show.start).time }}</span>
                                        </div>
                                    </div>

                                    <!-- Seat -->
                                    <div class="mb-4 pb-4 border-b border-border">
                                        <div class="flex items-center text-sm">
                                            <MapPin class="h-4 w-4 mr-1 text-primary" />
                                            <span>Seat: {{ ticket.seat.seat_number }}</span>
                                        </div>
                                    </div>

                                    <!-- Ticket Code -->
                                    <div>
                                        <div class="flex items-center mb-2">
                                            <QrCode class="h-4 w-4 mr-1 text-primary" />
                                            <span class="text-sm font-medium">Ticket Code</span>
                                        </div>
                                        <div class="bg-secondary/50 p-3 rounded-md font-mono text-center select-all">
                                            {{ ticket.code }}
                                        </div>
                                    </div>

                                    <!-- View Details Link -->
                                    <div class="mt-4 text-center">
                                        <Link
                                            :href="`/bookings/${booking.id}`"
                                            class="text-primary hover:underline text-sm inline-flex items-center"
                                        >
                                            <Ticket class="h-4 w-4 mr-1" />
                                            View Ticket Details
                                        </Link>
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
