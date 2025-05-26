<script setup lang="ts">
import { Head, Link, router } from '@inertiajs/vue3';
import AdminLayout from '@/layouts/AdminLayout.vue';
import { Button } from '@/components/ui/button';
import { Plus, Edit, Trash2, Eye, Music, MapPin, Calendar } from 'lucide-vue-next';

defineProps<{
    concerts: {
        data: Array<{
            id: number;
            artist: {
                name: string;
            };
            location: {
                name: string;
            };
            shows_count: number;
            created_at: string;
        }>;
        links: any;
        meta: any;
    };
}>();

const deleteConcert = (id: number) => {
    if (confirm('Are you sure you want to delete this concert?')) {
        router.delete(route('admin.concerts.destroy', id));
    }
};
</script>

<template>
    <Head title="Concerts - Admin" />

    <AdminLayout>
        <div class="flex items-center justify-between mb-6">
            <div>
                <h1 class="text-2xl font-bold text-foreground">Concerts</h1>
                <p class="text-muted-foreground">Manage concerts and performances</p>
            </div>
            <Link :href="route('admin.concerts.create')">
                <Button class="bg-primary text-primary-foreground hover:bg-primary/90">
                    <Plus class="h-4 w-4 mr-2" />
                    Add Concert
                </Button>
            </Link>
        </div>

        <div class="bg-card rounded-lg border border-border shadow-sm">
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="border-b border-border">
                    <tr>
                        <th class="text-left p-4 font-medium text-muted-foreground">Artist</th>
                        <th class="text-left p-4 font-medium text-muted-foreground">Location</th>
                        <th class="text-left p-4 font-medium text-muted-foreground">Shows</th>
                        <th class="text-left p-4 font-medium text-muted-foreground">Created</th>
                        <th class="text-right p-4 font-medium text-muted-foreground">Actions</th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr
                        v-for="concert in concerts.data"
                        :key="concert.id"
                        class="border-b border-border last:border-0 hover:bg-muted/30"
                    >
                        <td class="p-4">
                            <div class="flex items-center">
                                <div class="w-10 h-10 rounded-full bg-muted mr-3 flex items-center justify-center">
                                    <Music class="h-5 w-5 text-muted-foreground" />
                                </div>
                                <div>
                                    <p class="font-medium text-foreground">{{ concert.artist.name }}</p>
                                </div>
                            </div>
                        </td>
                        <td class="p-4">
                            <div class="flex items-center">
                                <MapPin class="h-4 w-4 mr-2 text-muted-foreground" />
                                <span class="text-sm text-foreground">{{ concert.location.name }}</span>
                            </div>
                        </td>
                        <td class="p-4">
                            <div class="flex items-center">
                                <Calendar class="h-4 w-4 mr-2 text-muted-foreground" />
                                <span class="text-sm text-foreground">{{ concert.shows_count }}</span>
                            </div>
                        </td>
                        <td class="p-4">
                            <span class="text-sm text-muted-foreground">{{ new Date(concert.created_at).toLocaleDateString() }}</span>
                        </td>
                        <td class="p-4">
                            <div class="flex items-center justify-end space-x-2">
                                <Link :href="route('admin.concerts.show', concert.id)">
                                    <Button variant="ghost" size="sm">
                                        <Eye class="h-4 w-4" />
                                    </Button>
                                </Link>
                                <Link :href="route('admin.concerts.edit', concert.id)">
                                    <Button variant="ghost" size="sm">
                                        <Edit class="h-4 w-4" />
                                    </Button>
                                </Link>
                                <Button
                                    variant="ghost"
                                    size="sm"
                                    @click="deleteConcert(concert.id)"
                                    :disabled="concert.shows_count > 0"
                                >
                                    <Trash2 class="h-4 w-4" />
                                </Button>
                            </div>
                        </td>
                    </tr>
                    </tbody>
                </table>
            </div>

            <div v-if="concerts.data.length === 0" class="p-8 text-center">
                <p class="text-muted-foreground">No concerts found.</p>
            </div>
        </div>
    </AdminLayout>
</template>
