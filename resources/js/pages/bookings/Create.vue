<script setup lang="ts">
import { Head, Link, useForm } from '@inertiajs/vue3';
import AppSidebarLayout from '@/layouts/app/AppSidebarLayout.vue';
import { Button } from '@/Components/ui/button';
import { Input } from '@/Components/ui/input';
import { Label } from '@/Components/ui/label';
import { ref, onMounted, onBeforeUnmount } from 'vue';
import { CalendarDays, MapPin, Clock, CreditCard, User, Home, MapPinned } from 'lucide-vue-next';

const props = defineProps<{
    reservation: {
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
        reserved_until: string;
    };
    userEmail: string;
}>();

const form = useForm({
    reservation_id: props.reservation.id,
    name: '',
    address: '',
    city: '',
    zip: '',
    country: '',
    email: props.userEmail || '', // Pre-fill with user's email but hide from UI
});

const submit = () => {
    form.post(route('bookings.store'), {
        onSuccess: () => form.reset(),
    });
};

// Format date for display
const formatDate = (dateString) => {
    return new Date(dateString).toLocaleString();
};

// Countdown timer for reservation expiration
const timeRemaining = ref('');
const countdownInterval = ref(null);

const updateCountdown = () => {
    const now = new Date();
    const expiry = new Date(props.reservation.reserved_until);
    const diffMs = expiry.getTime() - now.getTime();

    if (diffMs <= 0) {
        timeRemaining.value = 'Expired';
        clearInterval(countdownInterval.value);
        return;
    }

    const diffSecs = Math.floor(diffMs / 1000);
    const minutes = Math.floor(diffSecs / 60);
    const seconds = diffSecs % 60;

    timeRemaining.value = `${minutes}:${seconds < 10 ? '0' : ''}${seconds}`;
};

onMounted(() => {
    updateCountdown();
    countdownInterval.value = setInterval(updateCountdown, 1000);
});

onBeforeUnmount(() => {
    if (countdownInterval.value) {
        clearInterval(countdownInterval.value);
    }
});
</script>

<template>
    <Head title="Confirm Booking" />
    <AppSidebarLayout
        :breadcrumbs="[
            { title: 'Dashboard', href: '/dashboard' },
            { title: 'Confirm Booking' },
        ]"
    >
        <div class="p-4 max-w-2xl mx-auto">
            <h1 class="text-2xl font-bold mb-2">Complete Your Purchase</h1>
            <p class="text-muted-foreground mb-6">Please provide your details to confirm your booking.</p>

            <!-- Reservation Summary Card -->
            <div class="bg-card rounded-xl shadow-sm mb-6 overflow-hidden">
                <div class="bg-primary/10 p-4 border-b border-border">
                    <h2 class="text-lg font-medium flex items-center">
                        <CreditCard class="h-5 w-5 mr-2 text-primary" />
                        Reservation Summary
                    </h2>
                </div>

                <div class="p-4">
                    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                        <div>
                            <h3 class="font-medium text-lg">{{ reservation.show.concert.artist }}</h3>
                            <div class="flex items-center text-sm text-muted-foreground mt-2">
                                <CalendarDays class="h-4 w-4 mr-1" />
                                <span>{{ formatDate(reservation.show.start) }}</span>
                            </div>
                            <div class="flex items-center text-sm text-muted-foreground mt-1">
                                <MapPin class="h-4 w-4 mr-1" />
                                <span>Seat: {{ reservation.seat.seat_number }}</span>
                            </div>
                        </div>

                        <div class="bg-primary/5 p-3 rounded-lg flex flex-col items-center">
                            <div class="text-sm text-muted-foreground mb-1 flex items-center">
                                <Clock class="h-4 w-4 mr-1" />
                                Reservation expires in:
                            </div>
                            <div class="text-xl font-mono font-bold" :class="timeRemaining === 'Expired' ? 'text-destructive' : 'text-primary'">
                                {{ timeRemaining }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Booking Form -->
            <div class="bg-card rounded-xl shadow-sm overflow-hidden">
                <div class="bg-primary/10 p-4 border-b border-border">
                    <h2 class="text-lg font-medium flex items-center">
                        <User class="h-5 w-5 mr-2 text-primary" />
                        Booking Information
                    </h2>
                </div>

                <div class="p-4">
                    <form @submit.prevent="submit" class="space-y-6">
                        <!-- Personal Information Section -->
                        <div>
                            <h3 class="text-sm font-medium text-muted-foreground mb-3 flex items-center">
                                <User class="h-4 w-4 mr-1" />
                                Personal Information
                            </h3>
                            <div class="space-y-4">
                                <div>
                                    <Label for="name">Full Name</Label>
                                    <Input
                                        id="name"
                                        v-model="form.name"
                                        placeholder="Enter your full name"
                                        required
                                        class="mt-1"
                                    />
                                    <p v-if="form.errors.name" class="text-destructive text-sm mt-1">
                                        {{ form.errors.name }}
                                    </p>
                                </div>

                                <!-- Email field is hidden but still included in the form data -->
                                <input type="hidden" v-model="form.email" />
                            </div>
                        </div>

                        <!-- Address Information Section -->
                        <div>
                            <h3 class="text-sm font-medium text-muted-foreground mb-3 flex items-center">
                                <Home class="h-4 w-4 mr-1" />
                                Billing Address
                            </h3>
                            <div class="space-y-4">
                                <div>
                                    <Label for="address">Street Address</Label>
                                    <Input
                                        id="address"
                                        v-model="form.address"
                                        placeholder="Enter your street address"
                                        required
                                        class="mt-1"
                                    />
                                    <p v-if="form.errors.address" class="text-destructive text-sm mt-1">
                                        {{ form.errors.address }}
                                    </p>
                                </div>

                                <div>
                                    <Label for="country">Country</Label>
                                    <Input
                                        id="country"
                                        v-model="form.country"
                                        placeholder="Enter your country"
                                        required
                                        class="mt-1"
                                    />
                                    <p v-if="form.errors.country" class="text-destructive text-sm mt-1">
                                        {{ form.errors.country }}
                                    </p>
                                </div>

                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div>
                                        <Label for="city">City</Label>
                                        <Input
                                            id="city"
                                            v-model="form.city"
                                            placeholder="Enter your city"
                                            required
                                            class="mt-1"
                                        />
                                        <p v-if="form.errors.city" class="text-destructive text-sm mt-1">
                                            {{ form.errors.city }}
                                        </p>
                                    </div>

                                    <div>
                                        <Label for="zip">ZIP/Postal Code</Label>
                                        <Input
                                            id="zip"
                                            v-model="form.zip"
                                            placeholder="Enter your ZIP code"
                                            required
                                            class="mt-1"
                                        />
                                        <p v-if="form.errors.zip" class="text-destructive text-sm mt-1">
                                            {{ form.errors.zip }}
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Submit Button -->
                        <div class="pt-2">
                            <Button
                                type="submit"
                                class="w-full bg-primary text-primary-foreground hover:bg-primary/90"
                                :disabled="form.processing"
                            >
                                <CreditCard class="h-4 w-4 mr-2" />
                                Complete Purchase
                            </Button>

                            <div class="text-center mt-4">
                                <Link
                                    href="/dashboard"
                                    class="text-primary hover:underline text-sm"
                                >
                                    Return to Dashboard
                                </Link>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </AppSidebarLayout>
</template>
