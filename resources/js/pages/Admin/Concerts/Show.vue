<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import AdminLayout from '@/layouts/AdminLayout.vue';
import { Button } from '@/Components/ui/button';
import { ArrowLeft, Edit, Music, MapPin, Calendar, Users, Ticket, Clock } from 'lucide-vue-next';

const props = defineProps<{
    concert: {
        id: number;
        artist: {
            name: string;
            bio: string;
            image_url: string;
        };
        location: {
            name: string;
            address: string;
            city: string;
            capacity: number;
        };
        created_at: string;
        updated_at: string;
        shows: Array<{
            id: number;
            start: string;
            end: string;
            tickets: Array<any>;
            reservations: Array<any>;
        }>;
    };
}>();

const formatDate = (dateString: string) => {
    return new Date(dateString).toLocaleDateString('en-US', {
        year: 'numeric',
        month: 'long',
        day: 'numeric',
        hour: '2-digit',
        minute: '2-digit'
    });
};

const formatShowDate = (dateString: string) => {
    return new Date(dateString).toLocaleDateString('en-US', {
        weekday: 'short',
        year: 'numeric',
        month: 'short',
        day: 'numeric',
        hour: '2-digit',
        minute: '2-digit'
    });
};

const getShowDuration = (start: string, end: string) => {
    if (!end) return null;
    const startTime = new Date(start);
    const endTime = new Date(end);
    const durationMs = endTime.getTime() - startTime.getTime();
    return Math.round(durationMs / (1000 * 60)); // Convert to minutes
};

const getTotalTicketsSold = () => {
    return props.concert.shows.reduce((total, show) => total + show.tickets.length, 0);
};

const getTotalReservations = () => {
    return props.concert.shows.reduce((total, show) => total + show.reservations.length, 0);
};
</script>

<template>
    <Head :title="`${concert.artist.name} at ${concert.location.name} - Admin`" />

    <AdminLayout>
        <div class="mb-6">
            <div class="flex items-center justify-between mb-4">
                <Link :href="route('admin.concerts.index')">
                    <Button variant="ghost" size="sm">
                        <ArrowLeft class="h-4 w-4 mr-2" />
                        Back to Concerts
                    </Button>
                </Link>
                <Link :href="route('admin.concerts.edit', concert.id)">
                    <Button>
                        <Edit class="h-4 w-4 mr-2" />
                        Edit Concert
                    </Button>
                </Link>
            </div>
            <h1 class="text-2xl font-bold text-foreground">{{ concert.artist.name }}</h1>
            <p class="text-muted-foreground">Concert at {{ concert.location.name }}</p>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Concert Details -->
            <div class="lg:col-span-2 space-y-6">
                <!-- Artist Information -->
                <div class="bg-card rounded-lg border border-border shadow-sm p-6">
                    <h2 class="text-lg font-semibold mb-4 flex items-center">
                        <Music class="h-5 w-5 mr-2 text-primary" />
                        Artist Information
                    </h2>

                    <div class="space-y-4">
                        <div>
                            <label class="text-sm font-medium text-muted-foreground">Artist Name</label>
                            <p class="text-foreground">{{ concert.artist.name }}</p>
                        </div>

                        <div v-if="concert.artist.bio">
                            <label class="text-sm font-medium text-muted-foreground">Biography</label>
                            <p class="text-foreground whitespace-pre-wrap">{{ concert.artist.bio }}</p>
                        </div>
                    </div>
                </div>

                <!-- Location Information -->
                <div class="bg-card rounded-lg border border-border shadow-sm p-6">
                    <h2 class="text-lg font-semibold mb-4 flex items-center">
                        <MapPin class="h-5 w-5 mr-2 text-primary" />
                        Venue Information
                    </h2>

                    <div class="space-y-4">
                        <div>
                            <label class="text-sm font-medium text-muted-foreground">Venue Name</label>
                            <p class="text-foreground">{{ concert.location.name }}</p>
                        </div>

                        <div v-if="concert.location.address">
                            <label class="text-sm font-medium text-muted-foreground">Address</label>
                            <p class="text-foreground">{{ concert.location.address }}</p>
                        </div>

                        <div v-if="concert.location.city">
                            <label class="text-sm font-medium text-muted-foreground">City</label>
                            <p class="text-foreground">{{ concert.location.city }}</p>
                        </div>

                        <div v-if="concert.location.capacity">
                            <label class="text-sm font-medium text-muted-foreground">Capacity</label>
                            <div class="flex items-center">
                                <Users class="h-4 w-4 mr-2 text-muted-foreground" />
                                <p class="text-foreground">{{ concert.location.capacity.toLocaleString() }} people</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Shows -->
                <div class="bg-card rounded-lg border border-border shadow-sm p-6">
                    <h2 class="text-lg font-semibold mb-4 flex items-center">
                        <Calendar class="h-5 w-5 mr-2 text-primary" />
                        Shows ({{ concert.shows.length }})
                    </h2>

                    <div v-if="concert.shows.length > 0" class="space-y-4">
                        <div
                            v-for="show in concert.shows"
                            :key="show.id"
                            class="p-4 bg-muted/30 rounded-lg border border-border"
                        >
                            <div class="flex items-center justify-between mb-3">
                                <div>
                                    <p class="font-medium text-foreground">{{ formatShowDate(show.start) }}</p>
                                    <div v-if="show.end" class="flex items-center text-sm text-muted-foreground">
                                        <Clock class="h-4 w-4 mr-1" />
                                        {{ getShowDuration(show.start, show.end) }} minutes
                                    </div>
                                </div>
                                <div class="text-right">
                                    <div class="flex items-center text-sm text-foreground">
                                        <Ticket class="h-4 w-4 mr-1" />
                                        {{ show.tickets.length }} tickets sold
                                    </div>
                                    <div v-if="show.reservations.length > 0" class="text-sm text-muted-foreground">
                                        {{ show.reservations.length }} active reservations
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div v-else class="text-center py-8">
                        <p class="text-muted-foreground">No shows scheduled for this concert.</p>
                    </div>
                </div>

                <!-- Concert Metadata -->
                <div class="bg-card rounded-lg border border-border shadow-sm p-6">
                    <h2 class="text-lg font-semibold mb-4">Concert Details</h2>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="text-sm font-medium text-muted-foreground">Created</label>
                            <p class="text-foreground">{{ formatDate(concert.created_at) }}</p>
                        </div>
                        <div>
                            <label class="text-sm font-medium text-muted-foreground">Last Updated</label>
                            <p class="text-foreground">{{ formatDate(concert.updated_at) }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Sidebar -->
            <div class="space-y-6">
                <!-- Artist Image -->
                <div class="bg-card rounded-lg border border-border shadow-sm p-6">
                    <h2 class="text-lg font-semibold mb-4">Artist Image</h2>

                    <div v-if="concert.artist.image_url" class="aspect-square rounded-lg overflow-hidden bg-muted">
                        <img
                            :src="concert.artist.image_url"
                            :alt="concert.artist.name"
                            class="w-full h-full object-cover"
                            @error="$event.target.style.display = 'none'"
                        />
                    </div>

                    <div v-else class="aspect-square rounded-lg bg-muted flex items-center justify-center">
                        <div class="text-center">
                            <div class="w-16 h-16 rounded-full bg-primary/10 flex items-center justify-center mx-auto mb-2">
                                <span class="text-2xl font-bold text-primary">{{ concert.artist.name.charAt(0) }}</span>
                            </div>
                            <p class="text-sm text-muted-foreground">No image available</p>
                        </div>
                    </div>
                </div>

                <!-- Statistics -->
                <div class="bg-card rounded-lg border border-border shadow-sm p-6">
                    <h2 class="text-lg font-semibold mb-4">Statistics</h2>

                    <div class="space-y-4">
                        <div class="flex items-center justify-between p-3 bg-muted/30 rounded-lg">
                            <div class="flex items-center">
                                <Calendar class="h-5 w-5 text-primary mr-2" />
                                <span class="text-sm font-medium">Total Shows</span>
                            </div>
                            <span class="text-lg font-bold text-primary">{{ concert.shows.length }}</span>
                        </div>

                        <div class="flex items-center justify-between p-3 bg-muted/30 rounded-lg">
                            <div class="flex items-center">
                                <Ticket class="h-5 w-5 text-green-600 mr-2" />
                                <span class="text-sm font-medium">Tickets Sold</span>
                            </div>
                            <span class="text-lg font-bold text-green-600">{{ getTotalTicketsSold() }}</span>
                        </div>

                        <div class="flex items-center justify-between p-3 bg-muted/30 rounded-lg">
                            <div class="flex items-center">
                                <Clock class="h-5 w-5 text-yellow-600 mr-2" />
                                <span class="text-sm font-medium">Active Reservations</span>
                            </div>
                            <span class="text-lg font-bold text-yellow-600">{{ getTotalReservations() }}</span>
                        </div>

                        <div v-if="concert.location.capacity" class="flex items-center justify-between p-3 bg-muted/30 rounded-lg">
                            <div class="flex items-center">
                                <Users class="h-5 w-5 text-blue-600 mr-2" />
                                <span class="text-sm font-medium">Venue Capacity</span>
                            </div>
                            <span class="text-lg font-bold text-blue-600">{{ concert.location.capacity.toLocaleString() }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AdminLayout>
</template>
