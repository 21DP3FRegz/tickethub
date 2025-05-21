<script setup lang="ts">
import { Head, Link, useForm } from '@inertiajs/vue3';
import AppSidebarLayout from '@/layouts/app/AppSidebarLayout.vue';
import { Button } from '@/Components/ui/button';

defineProps<{
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
    form.delete(route('bookings.destroy', bookingId), {
        onSuccess: () => form.reset(),
    });
};
</script>

<template>
    <Head title="Your Tickets" />
    <AppSidebarLayout :breadcrumbs="[{ title: 'Tickets', href: '/bookings' }]">
        <div class="p-6">
            <h1 class="text-2xl font-bold mb-4">Your Tickets</h1>
            <div v-if="bookings.length" class="grid gap-4">
                <div v-for="booking in bookings" :key="booking.id" class="border p-4 rounded-lg">
                    <p class="font-medium">Booking for {{ booking.name }}</p>
                    <div v-for="ticket in booking.tickets" :key="ticket.id" class="mt-2">
                        <p>{{ ticket.show.concert.artist }} - {{ new Date(ticket.show.start).toLocaleString() }}</p>
                        <p>Seat: {{ ticket.seat.seat_number }}</p>
                        <p>Ticket Code: {{ ticket.code }}</p>
                    </div>
                    <Button
                        @click="cancelBooking(booking.id)"
                        class="mt-2 bg-red-500 text-white"
                        :disabled="form.processing"
                    >
                        Cancel Booking
                    </Button>
                </div>
            </div>
            <p v-else class="text-gray-500">No tickets purchased.</p>
            <Link :href="route('concerts.index')" class="mt-6 inline-block text-[#f53003] underline">
                Browse Concerts
            </Link>
        </div>
    </AppSidebarLayout>
</template>
