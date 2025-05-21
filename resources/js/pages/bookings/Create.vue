<script setup lang="ts">
import { Head, Link, useForm } from '@inertiajs/vue3';
import AppSidebarLayout from '@/layouts/app/AppSidebarLayout.vue';
import { Button } from '@/Components/ui/button';
import { Input } from '@/Components/ui/input';
import { Label } from '@/Components/ui/label';

const props = defineProps<{
    reservation?: {
        id: number;
        show: {
            id: number;
            start: string;
            concert: {
                artist: string;
            }
        };
        seat: {
            seat_number: string;
        };
    };
    userEmail: string;
}>();

const form = useForm({
    reservation_id: props.reservation ? props.reservation.id : null,
    name: '',
    address: '',
    city: '',
    zip: '',
    country: '',
    email: props.userEmail || '', // Pre-fill with user's email
});

const submit = () => {
    form.post(route('bookings.store'), {
        onSuccess: () => form.reset(),
    });
};
</script>

<template>
    <Head title="Confirm Booking" />
    <AppSidebarLayout
        :breadcrumbs="[
      { title: 'Reservations', href: '/reservations' },
      { title: 'Confirm Booking' },
    ]"
    >
        <div class="p-6 max-w-lg mx-auto">
            <h1 class="text-2xl font-bold mb-4">Confirm Your Booking</h1>
            <p class="mb-4">
                Booking for {{ reservation.show.concert.artist }} on
                {{ new Date(reservation.show.start).toLocaleString() }}, Seat:
                {{ reservation.seat.seat_number }}
            </p>
            <form @submit.prevent="submit" class="grid gap-4">
                <div>
                    <Label for="name">Full Name</Label>
                    <Input id="name" v-model="form.name" required />
                    <p v-if="form.errors.name" class="text-red-500 text-sm">
                        {{ form.errors.name }}
                    </p>
                </div>
                <div>
                    <Label for="address">Address</Label>
                    <Input id="address" v-model="form.address" required />
                    <p v-if="form.errors.address" class="text-red-500 text-sm">
                        {{ form.errors.address }}
                    </p>
                </div>
                <div>
                    <Label for="city">City</Label>
                    <Input id="city" v-model="form.city" required />
                    <p v-if="form.errors.city" class="text-red-500 text-sm">
                        {{ form.errors.city }}
                    </p>
                </div>
                <div>
                    <Label for="zip">ZIP Code</Label>
                    <Input id="zip" v-model="form.zip" required />
                    <p v-if="form.errors.zip" class="text-red-500 text-sm">
                        {{ form.errors.zip }}
                    </p>
                </div>
                <div>
                    <Label for="country">Country</Label>
                    <Input id="country" v-model="form.country" required />
                    <p v-if="form.errors.country" class="text-red-500 text-sm">
                        {{ form.errors.country }}
                    </p>
                </div>
                <div>
                    <Label for="email">Email</Label>
                    <Input id="email" v-model="form.email" type="email" required />
                    <p v-if="form.errors.email" class="text-red-500 text-sm">
                        {{ form.errors.email }}
                    </p>
                </div>
                <Button
                    type="submit"
                    class="bg-[#f53003] text-white"
                    :disabled="form.processing"
                >
                    Confirm Booking
                </Button>
            </form>
            <Link
                :href="route('reservations.index')"
                class="mt-4 inline-block text-[#f53003] underline"
            >
                Back to Reservations
            </Link>
        </div>
    </AppSidebarLayout>
</template>
