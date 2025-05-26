<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import AdminLayout from '@/layouts/AdminLayout.vue';
import { Button } from '@/components/ui/button';
import { ArrowLeft, Edit, Calendar, MapPin } from 'lucide-vue-next';

defineProps<{
    artist: {
        id: number;
        name: string;
        bio: string;
        image_url: string;
        created_at: string;
        updated_at: string;
        concerts: Array<{
            id: number;
            location: {
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
    <Head :title="`${artist.name} - Admin`" />

    <AdminLayout>
        <div class="mb-6">
            <div class="flex items-center justify-between mb-4">
                <Link :href="route('admin.artists.index')">
                    <Button variant="ghost" size="sm">
                        <ArrowLeft class="h-4 w-4 mr-2" />
                        Back to Artists
                    </Button>
                </Link>
                <Link :href="route('admin.artists.edit', artist.id)">
                    <Button>
                        <Edit class="h-4 w-4 mr-2" />
                        Edit Artist
                    </Button>
                </Link>
            </div>
            <h1 class="text-2xl font-bold text-foreground">{{ artist.name }}</h1>
            <p class="text-muted-foreground">Artist details and information</p>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Artist Info -->
            <div class="lg:col-span-2">
                <div class="bg-card rounded-lg border border-border shadow-sm p-6 mb-6">
                    <h2 class="text-lg font-semibold mb-4">Artist Information</h2>

                    <div class="space-y-4">
                        <div>
                            <label class="text-sm font-medium text-muted-foreground">Name</label>
                            <p class="text-foreground">{{ artist.name }}</p>
                        </div>

                        <div v-if="artist.bio">
                            <label class="text-sm font-medium text-muted-foreground">Biography</label>
                            <p class="text-foreground whitespace-pre-wrap">{{ artist.bio }}</p>
                        </div>

                        <div v-if="artist.image_url">
                            <label class="text-sm font-medium text-muted-foreground">Image URL</label>
                            <p class="text-foreground break-all">{{ artist.image_url }}</p>
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="text-sm font-medium text-muted-foreground">Created</label>
                                <p class="text-foreground">{{ formatDate(artist.created_at) }}</p>
                            </div>
                            <div>
                                <label class="text-sm font-medium text-muted-foreground">Last Updated</label>
                                <p class="text-foreground">{{ formatDate(artist.updated_at) }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Concerts -->
                <div class="bg-card rounded-lg border border-border shadow-sm p-6">
                    <h2 class="text-lg font-semibold mb-4">Concerts ({{ artist.concerts.length }})</h2>

                    <div v-if="artist.concerts.length > 0" class="space-y-4">
                        <div
                            v-for="concert in artist.concerts"
                            :key="concert.id"
                            class="p-4 bg-muted/30 rounded-lg border border-border"
                        >
                            <div class="flex items-center justify-between">
                                <div>
                                    <div class="flex items-center text-sm text-muted-foreground mb-1">
                                        <MapPin class="h-4 w-4 mr-1" />
                                        {{ concert.location.name }}
                                    </div>
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
                        <p class="text-muted-foreground">No concerts found for this artist.</p>
                    </div>
                </div>
            </div>

            <!-- Artist Image -->
            <div>
                <div class="bg-card rounded-lg border border-border shadow-sm p-6">
                    <h2 class="text-lg font-semibold mb-4">Artist Image</h2>

                    <div v-if="artist.image_url" class="aspect-square rounded-lg overflow-hidden bg-muted">
                        <img
                            :src="artist.image_url"
                            :alt="artist.name"
                            class="w-full h-full object-cover"
                            @error="$event.target.style.display = 'none'"
                        />
                    </div>

                    <div v-else class="aspect-square rounded-lg bg-muted flex items-center justify-center">
                        <div class="text-center">
                            <div class="w-16 h-16 rounded-full bg-primary/10 flex items-center justify-center mx-auto mb-2">
                                <span class="text-2xl font-bold text-primary">{{ artist.name.charAt(0) }}</span>
                            </div>
                            <p class="text-sm text-muted-foreground">No image available</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AdminLayout>
</template>
