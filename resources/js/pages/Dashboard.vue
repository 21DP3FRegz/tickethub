<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head, Link } from '@inertiajs/vue3';

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Dashboard',
        href: '/dashboard',
    },
];

const reservations = [
    { id: 1, concert: 'Artist 1', show: '2025-06-01 19:00', seat: 'A1' },
]; // Placeholder
const bookings = [
    { id: 1, concert: 'Artist 1', show: '2025-06-01 19:00', seat: 'A1', code: 'TICKET123' },
]; // Placeholder
</script>

<template>
    <Head title="Dashboard" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-4 rounded-xl p-4">
            <h2 class="text-lg font-medium">Your Reservations</h2>
            <ul class="grid auto-rows-min gap-4 md:grid-cols-2">
                <li v-for="reservation in reservations" :key="reservation.id" class="rounded-xl border border-sidebar-border/70 p-4 dark:border-sidebar-border">
                    <p>{{ reservation.concert }} - {{ reservation.show }}</p>
                    <p>Seat: {{ reservation.seat }}</p>
                    <Link :href="route('reservations.index')" class="text-[#f53003] underline">Manage</Link>
                </li>
            </ul>
            <h2 class="text-lg font-medium">Your Tickets</h2>
            <ul class="grid auto-rows-min gap-4 md:grid-cols-2">
                <li v-for="booking in bookings" :key="booking.id" class="rounded-xl border border-sidebar-border/70 p-4 dark:border-sidebar-border">
                    <p>{{ booking.concert }} - {{ booking.show }}</p>
                    <p>Seat: {{ booking.seat }}</p>
                    <p>Ticket Code: {{ booking.code }}</p>
                    <Link :href="route('bookings.index')" class="text-[#f53003] underline">View Details</Link>
                </li>
            </ul>
        </div>
    </AppLayout>
</template>
