<script setup lang="ts">
import { Head, Link, router } from '@inertiajs/vue3';
import AdminLayout from '@/layouts/AdminLayout.vue';
import { Button } from '@/components/ui/button';
import { Plus, Edit, Trash2, Eye, User } from 'lucide-vue-next';

defineProps<{
    artists: {
        data: Array<{
            id: number;
            name: string;
            bio: string;
            image_url: string;
            concerts_count: number;
            created_at: string;
        }>;
        links: any;
        meta: any;
    };
}>();

const deleteArtist = (id: number) => {
    if (confirm('Are you sure you want to delete this artist?')) {
        router.delete(route('admin.artists.destroy', id));
    }
};
</script>

<template>
    <Head title="Artists - Admin" />

    <AdminLayout>
        <div class="p-6">
            <div class="flex items-center justify-between mb-6">
                <div>
                    <h1 class="text-2xl font-bold text-foreground">Artists</h1>
                    <p class="text-muted-foreground">Manage artists in your system</p>
                </div>
                <Link :href="route('admin.artists.create')">
                    <Button class="bg-primary text-primary-foreground hover:bg-primary/90">
                        <Plus class="h-4 w-4 mr-2" />
                        Add Artist
                    </Button>
                </Link>
            </div>

            <div class="bg-card rounded-lg border border-border shadow-sm">
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead class="border-b border-border">
                        <tr>
                            <th class="text-left p-4 font-medium text-muted-foreground">Name</th>
                            <th class="text-left p-4 font-medium text-muted-foreground">Concerts</th>
                            <th class="text-left p-4 font-medium text-muted-foreground">Created</th>
                            <th class="text-right p-4 font-medium text-muted-foreground">Actions</th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr
                            v-for="artist in artists.data"
                            :key="artist.id"
                            class="border-b border-border last:border-0 hover:bg-muted/30"
                        >
                            <td class="p-4">
                                <div class="flex items-center">
                                    <div class="w-10 h-10 rounded-full bg-muted mr-3 flex items-center justify-center">
                                        <User class="h-5 w-5 text-muted-foreground" />
                                    </div>
                                    <div>
                                        <p class="font-medium text-foreground">{{ artist.name }}</p>
                                        <p v-if="artist.bio" class="text-sm text-muted-foreground truncate max-w-xs">{{ artist.bio }}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="p-4">
                                <span class="text-sm text-foreground">{{ artist.concerts_count }}</span>
                            </td>
                            <td class="p-4">
                                <span class="text-sm text-muted-foreground">{{ new Date(artist.created_at).toLocaleDateString() }}</span>
                            </td>
                            <td class="p-4">
                                <div class="flex items-center justify-end space-x-2">
                                    <Link :href="route('admin.artists.show', artist.id)">
                                        <Button variant="ghost" size="sm">
                                            <Eye class="h-4 w-4" />
                                        </Button>
                                    </Link>
                                    <Link :href="route('admin.artists.edit', artist.id)">
                                        <Button variant="ghost" size="sm">
                                            <Edit class="h-4 w-4" />
                                        </Button>
                                    </Link>
                                    <Button
                                        variant="ghost"
                                        size="sm"
                                        @click="deleteArtist(artist.id)"
                                        :disabled="artist.concerts_count > 0"
                                    >
                                        <Trash2 class="h-4 w-4" />
                                    </Button>
                                </div>
                            </td>
                        </tr>
                        </tbody>
                    </table>
                </div>

                <!-- Pagination would go here -->
                <div v-if="artists.data.length === 0" class="p-8 text-center">
                    <p class="text-muted-foreground">No artists found.</p>
                </div>
            </div>
        </div>
    </AdminLayout>
</template>
