<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import AdminLayout from '@/layouts/AdminLayout.vue';
import { Button } from '@/Components/ui/button';
import { ArrowLeft, Edit, MapPin, Calendar } from 'lucide-vue-next';

const props = defineProps<{
    location: {
        id: number;
        name: string;
        created_at: string;
        updated_at: string;
        concerts: Array<{
            id: number;
            artist: {
                name: string;
            };
            shows: Array<{
                start: string;
            }>;
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
</script>

<template>
    <Head :title="`${location.name} - Admin`" />

    <AdminLayout>
        <div class="mb-6">
            <div class="flex items-center justify-between mb-4">
                <Link :href="route('admin.locations.index')">
                    <Button variant="ghost" size="sm">
                        <ArrowLeft class="h-4 w-4 mr-2" />
                        Back to Locations
                    </Button>
                </Link>
                <Link :href="route('admin.locations.edit', location.id)">
                    <Button>
                        <Edit class="h-4 w-4 mr-2" />
                        Edit Location
                    </Button>
                </Link>
            </div>
            <h1 class="text-2xl font-bold text-foreground">{{ location.name }}</h1>
            <p class="text-muted-foreground">Location details and information</p>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Location Info -->
            <div class="lg:col-span-2">
                <div class="bg-card rounded-lg border border-border shadow-sm p-6 mb-6">
                    <h2 class="text-lg font-semibold mb-4">Location Information</h2>

                    <div class="space-y-4">
                        <div>
                            <label class="text-sm font-medium text-muted-foreground">Name</label>
                            <p class="text-foreground">{{ location.name }}</p>
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="text-sm font-medium text-muted-foreground">Created</label>
                                <p class="text-foreground">{{ formatDate(location.created_at) }}</p>
                            </div>
                            <div>
                                <label class="text-sm font-medium text-muted-foreground">Last Updated</label>
                                <p class="text-foreground">{{ formatDate(location.updated_at) }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Concerts -->
                <div class="bg-card rounded-lg border border-border shadow-sm p-6">
                    <h2 class="text-lg font-semibold mb-4">Concerts ({{ location.concerts.length }})</h2>

                    <div v-if="location.concerts.length > 0" class="space-y-4">
                        <div
                            v-for="concert in location.concerts"
                            :key="concert.id"
                            class="p-4 bg-muted/30 rounded-lg border border-border"
                        >
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="font-medium text-foreground">{{ concert.artist.name }}</p>
                                    <div class="flex items-center text-sm text-muted-foreground">
                                        <Calendar class="h-4 w-4 mr-1" />
                                        {{ concert.shows.length }} show{{ concert.shows.length !== 1 ? 's' : '' }}
                                    </div>
                                </div>
                                <Link :href="route('admin.concerts.show', concert.id)">
                                    <Button variant="outline" size="sm">
                                        View Details
                                    </Button>
                                </Link>
                            </div>
                        </div>
                    </div>

                    <div v-else class="text-center py-8">
                        <p class="text-muted-foreground">No concerts found for this location.</p>
                    </div>
                </div>
            </div>

            <!-- Location Stats -->
            <div>
                <div class="bg-card rounded-lg border border-border shadow-sm p-6">
                    <h2 class="text-lg font-semibold mb-4">Statistics</h2>

                    <div class="space-y-4">
                        <div class="flex items-center justify-between p-3 bg-muted/30 rounded-lg">
                            <div class="flex items-center">
                                <Calendar class="h-5 w-5 text-primary mr-2" />
                                <span class="text-sm font-medium">Total Concerts</span>
                            </div>
                            <span class="text-lg font-bold text-primary">{{ location.concerts.length }}</span>
                        </div>

                        <div class="flex items-center justify-between p-3 bg-muted/30 rounded-lg">
                            <div class="flex items-center">
                                <MapPin class="h-5 w-5 text-green-600 mr-2" />
                                <span class="text-sm font-medium">Total Shows</span>
                            </div>
                            <span class="text-lg font-bold text-green-600">
                {{ location.concerts.reduce((total, concert) => total + concert.shows.length, 0) }}
              </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AdminLayout>
</template>
