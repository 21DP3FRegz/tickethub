<script setup lang="ts">
import { Head, Link, useForm } from '@inertiajs/vue3';
import AppSidebarLayout from '@/layouts/app/AppSidebarLayout.vue';
import { Button } from '@/Components/ui/button';

defineProps<{
    reservations: Array<{
        id: number;
        show: { id: number; start: string; concert: { artist: string } };
        seat: { seat_number: string };
        reserved_until: string;
    }>;
}>();

const form = useForm({});

const cancelReservation = (reservationId: number) => {
    form.delete(route('reservations.destroy', reservationId), {
        onSuccess: () => form.reset(),
    });
};
</script>

<template>
    <Head title="Your Reservations" />
    <AppSidebarLayout :breadcrumbs="[{ title: 'Reservations', href: '/reservations' }]">
        <div class="p-6">
            <h1 class="text-2xl font-bold mb-4">Your Reservations</h1>
            <div v-if="reservations.length" class="grid gap-4">
                <div v-for="reservation in reservations" :key="reservation.id" class="border p-4 rounded-lg">
                    <p class="font-medium">{{ reservation.show.concert.artist }}</p>
                    <p>{{ new Date(reservation.show.start).toLocaleString() }}</p>
                    <p>Seat: {{ reservation.seat.seat_number }}</p>
                    <p>Expires: {{ new Date(reservation.reserved_until).toLocaleString() }}</p>
                    <div class="flex gap-2 mt-2">
                        <Button
                            @click="cancelReservation(reservation.id)"
                            class="bg-red-500 text-white"
                            :disabled="form.processing"
                        >
                            Cancel Reservation
                        </Button>
                        <Link
                            :href="route('bookings.create', { reservation_id: reservation.id })"
                            class="inline-flex items-center px-4 py-2 bg-[#f53003] text-white rounded"
                        >
                            Book Now
                        </Link>
                    </div>
                </div>
            </div>
            <p v-else class="text-gray-500">No active reservations.</p>
            <Link :href="route('concerts.index')" class="mt-6 inline-block text-[#f53003] underline">
                Browse Concerts
            </Link>
        </div>
    </AppSidebarLayout>
</template>
