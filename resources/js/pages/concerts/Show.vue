<script setup lang="ts">
import { Head, Link, useForm } from '@inertiajs/vue3';
import AppSidebarLayout from '@/layouts/app/AppSidebarLayout.vue';
import { Button } from '@/Components/ui/button';
import { Dialog, DialogContent, DialogHeader, DialogTitle, DialogTrigger } from '@/Components/ui/dialog';
import { ref, onMounted } from 'vue';

const props = defineProps<{
    concert: {
        id: number;
        artist: string;
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
            }>;
            rows: Array<{
                id: number;
                name: string;
                order: number;
            }>;
        }>;
    };
}>();

onMounted(() => {
    console.log('Component mounted with concert data:', props.concert);
    if (props.concert.shows) {
        console.log('Number of shows:', props.concert.shows.length);
        props.concert.shows.forEach(show => {
            console.log(`Show ID ${show.id} has ${show.seats?.length || 0} seats and ${show.rows?.length || 0} rows`);
        });
    }
});

const selectedShow = ref<number | null>(null);
const form = useForm<{
    show_id: number | null;
    seat_id: number | null;
}>({
    show_id: null,
    seat_id: null,
});
const error = ref('');

const openSeatMap = (showId: number) => {
    console.log('Opening seat map for show ID:', showId);
    selectedShow.value = showId;
};

const selectSeat = (showId: number, seatId: number) => {
    console.log('Selecting seat:', { showId, seatId });
    form.show_id = showId;
    form.seat_id = seatId;
    error.value = '';
    console.log('Form after selection:', form);
};

const reserveSeat = () => {
    console.log('Attempting to reserve seat with form data:', form);
    form.post(route('reservations.store'), {
        onError: (errors) => {
            console.error('Reservation error:', errors);
            error.value = errors.seat || 'An error occurred.';
        },
        onSuccess: () => {
            console.log('Reservation successful!');
            selectedShow.value = null; // Close modal
        },
    });
};
</script>

<template>
    <Head :title="concert.artist" />
    <AppSidebarLayout :breadcrumbs="[{ title: 'Concerts', href: '/concerts' }, { title: concert.artist }]">
        <div class="p-6 max-w-4xl mx-auto">
            <h1 class="text-2xl font-bold mb-4">{{ concert.artist }}</h1>
            <p class="mb-4">Location: {{ concert.location.name }}</p>
            <h2 class="text-xl font-semibold mb-4">Available Shows</h2>
            <div v-if="!concert.shows || concert.shows.length === 0" class="text-center py-8">
                <p>No upcoming shows available for this concert.</p>
            </div>
            <div v-else class="grid gap-6">
                <div v-for="show in concert.shows" :key="show.id" class="border p-4 rounded-lg">
                    <p class="font-medium">
                        {{ new Date(show.start).toLocaleString() }} - {{ new Date(show.end).toLocaleString() }}
                    </p>
                    <Dialog>
                        <DialogTrigger as-child>
                            <Button @click="openSeatMap(show.id)" class="mt-2 bg-[#f53003] text-white">
                                Select Seats
                            </Button>
                        </DialogTrigger>
                        <DialogContent class="sm:max-w-[600px]">
                            <DialogHeader>
                                <DialogTitle>Seat Map for {{ concert.artist }}</DialogTitle>
                            </DialogHeader>
                            <div class="mt-4">
                                <div v-if="!show.seats || show.seats.length === 0" class="text-center py-4">
                                    <p>No seats available for this show.</p>
                                </div>
                                <template v-else>
                                    <div v-for="row in show.rows" :key="row.id" class="mb-4">
                                        <p class="font-medium">{{ row.name }}</p>
                                        <div class="grid grid-cols-6 gap-2">
                                            <button
                                                v-for="seat in show.seats.filter(s => s.row_id === row.id)"
                                                :key="seat.id"
                                                :disabled="seat.reservation || seat.ticket"
                                                :class="[
                                                    'p-2 rounded text-center text-sm',
                                                    seat.reservation || seat.ticket
                                                        ? 'bg-gray-300 cursor-not-allowed'
                                                        : 'bg-green-500 hover:bg-green-600 text-white',
                                                    form.seat_id === seat.id ? 'ring-2 ring-[#f53003]' : '',
                                                ]"
                                                @click="selectSeat(show.id, seat.id)"
                                            >
                                                {{ seat.seat_number }}
                                            </button>
                                        </div>
                                    </div>
                                </template>
                                <p v-if="error" class="text-red-500 mt-2">{{ error }}</p>
                                <Button
                                    v-if="form.seat_id"
                                    @click="reserveSeat"
                                    class="mt-4 w-full bg-[#f53003] text-white"
                                    :disabled="form.processing"
                                >
                                    Reserve Selected Seat
                                </Button>
                            </div>
                        </DialogContent>
                    </Dialog>
                </div>
            </div>
            <Link :href="route('concerts.index')" class="mt-6 inline-block text-[#f53003] underline">
                Back to Concerts
            </Link>
        </div>
    </AppSidebarLayout>
</template>
