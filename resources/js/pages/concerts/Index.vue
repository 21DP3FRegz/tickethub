<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import AppHeaderLayout from '@/layouts/app/AppHeaderLayout.vue';

defineProps<{
    concerts: Array<{
        id: number;
        artist: string;
        location: { name: string };
        shows: Array<{ start: string }>;
    }>;
    filters: { location?: string; date?: string };
}>();
</script>

<template>
    <Head title="Concerts" />
    <AppHeaderLayout>
        <div class="p-6">
            <h1 class="text-2xl font-bold mb-4">Upcoming Concerts</h1>
            <!-- Filter Form -->
            <form class="mb-6">
                <input v-model="filters.location" placeholder="Filter by location" class="border p-2 mr-2" />
                <input v-model="filters.date" type="date" class="border p-2 mr-2" />
                <button type="submit" class="bg-[#f53003] text-white p-2">Apply</button>
            </form>
            <!-- Concert List -->
            <ul class="grid gap-4">
                <li v-for="concert in concerts" :key="concert.id" class="border p-4 rounded">
                    <Link :href="route('concerts.show', concert.id)" class="text-[#f53003]">
                        {{ concert.artist }} at {{ concert.location.name }}
                        ({{ concert.shows[0]?.start || 'No shows' }})
                    </Link>
                </li>
            </ul>
        </div>
    </AppHeaderLayout>
</template>
