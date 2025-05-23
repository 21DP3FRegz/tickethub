<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';

defineProps({
    upcomingConcerts: Array,
    popularLocations: Array,
    stats: Object
});
</script>

<template>
    <Head title="Welcome">
        <link rel="preconnect" href="https://rsms.me/" />
        <link rel="stylesheet" href="https://rsms.me/inter/inter.css" />
    </Head>

    <div class="min-h-screen bg-background text-foreground">
        <!-- Header Navigation -->
        <header class="container mx-auto px-4 py-6">
            <nav class="flex items-center justify-between">
                <div class="text-2xl font-bold text-primary">TicketHub</div>
                <div class="flex items-center space-x-4">
                    <template v-if="$page.props.auth.user">
                        <Link :href="route('dashboard')" class="px-4 py-2 rounded-md bg-secondary text-secondary-foreground hover:bg-secondary/90">
                            Dashboard
                        </Link>
                    </template>
                    <template v-else>
                        <Link :href="route('login')" class="px-4 py-2 rounded-md text-foreground hover:text-primary transition-colors">
                            Log in
                        </Link>
                        <Link :href="route('register')" class="px-4 py-2 rounded-md bg-primary text-primary-foreground hover:bg-primary/90">
                            Register
                        </Link>
                    </template>
                </div>
            </nav>
        </header>

        <!-- Hero Section -->
        <section class="container mx-auto mt-4 px-4 py-12">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 items-center">
                <div class="space-y-6">
                    <h1 class="text-4xl md:text-5xl font-bold text-foreground">
                        Find your next <span class="text-primary">live experience</span>
                    </h1>
                    <p class="text-lg text-muted-foreground">
                        Discover and book tickets for the best concerts and events in your area.
                    </p>
                    <div class="flex flex-wrap gap-4">
                        <Link href="/concerts" class="px-6 py-3 rounded-md bg-primary text-primary-foreground hover:bg-primary/90">
                            Browse Events
                        </Link>
                    </div>
                </div>
                <div class="bg-card rounded-xl p-6 shadow-lg">
                    <h2 class="text-2xl font-bold mb-6 text-card-foreground">Featured Event</h2>
                    <div v-if="upcomingConcerts && upcomingConcerts.length > 0" class="space-y-4">
                        <div class="bg-accent/10 rounded-lg p-6">
                            <h3 class="text-xl font-bold text-accent">{{ upcomingConcerts[0].artist.name }}</h3>
                            <p class="text-muted-foreground">{{ upcomingConcerts[0].location_name }}</p>
                            <p class="text-sm text-muted-foreground mt-2">{{ upcomingConcerts[0].next_show_date }}</p>
                            <Link :href="route('concerts.show', upcomingConcerts[0].id)" class="mt-4 inline-block w-full text-center rounded-md bg-accent text-accent-foreground px-5 py-2 hover:bg-accent/90">
                                Get Tickets
                            </Link>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Upcoming Concerts Section -->
        <section class="container mx-auto my-8 px-4 py-12">
            <h2 class="text-2xl font-bold mb-8">Upcoming Concerts</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                <div v-for="concert in upcomingConcerts" :key="concert.id" class="bg-card rounded-lg overflow-hidden shadow-md hover:shadow-lg transition-shadow">
                    <div class="p-6">
                        <h3 class="text-xl font-bold text-card-foreground">{{ concert.artist.name }}</h3>
                        <p class="text-muted-foreground">{{ concert.location_name }}</p>
                        <div class="flex justify-between items-center mt-4">
                            <span class="text-sm text-muted-foreground">{{ concert.next_show_date }}</span>
                            <Link :href="route('concerts.show', concert.id)" class="text-primary hover:text-primary/90">
                                View Details
                            </Link>
                        </div>
                    </div>
                </div>
            </div>
            <div class="mt-8 text-center">
                <Link href="/concerts" class="inline-block px-6 py-3 rounded-md bg-secondary text-secondary-foreground hover:bg-secondary/90">
                    View All Concerts
                </Link>
            </div>
        </section>

        <!-- Footer -->
        <footer class="bg-card border-t border-border py-8">
            <div class="container mx-auto px-4">
                <div class="flex flex-col items-center justify-center text-center">
                    <h3 class="text-lg font-bold mb-2">TicketHub</h3>
                    <p class="text-muted-foreground mb-4">Your gateway to amazing live performances</p>
                    <div class="mt-4 pt-4 border-t border-border text-muted-foreground w-full max-w-md">
                        <p>&copy; {{ new Date().getFullYear() }} TicketHub. All rights reserved.</p>
                    </div>
                </div>
            </div>
        </footer>
    </div>
</template>
