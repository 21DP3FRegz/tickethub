<script setup lang="ts">
import { Head, Link, router } from '@inertiajs/vue3';
import AdminLayout from '@/layouts/AdminLayout.vue';
import { Button } from '@/components/ui/button';
import { Plus, Edit, Trash2, Eye, MapPin } from 'lucide-vue-next';

defineProps<{
    locations: {
        data: Array<{
            id: number;
            name: string;
            concerts_count: number;
            created_at: string;
        }>;
        links: any;
        meta: any;
    };
}>();

const deleteLocation = (id: number) => {
    if (confirm('Are you sure you want to delete this location?')) {
        router.delete(route('admin.locations.destroy', id));
    }
};
</script>

<template>
    <Head title="Locations - Admin" />

    <AdminLayout>
        <div class="flex items-center justify-between mb-6">
            <div>
                <h1 class="text-2xl font-bold text-foreground">Locations</h1>
                <p class="text-muted-foreground">Manage venues and locations</p>
            </div>
            <Link :href="route('admin.locations.create')">
                <Button class="bg-primary text-primary-foreground hover:bg-primary/90">
                    <Plus class="h-4 w-4 mr-2" />
                    Add Location
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
                        v-for="location in locations.data"
                        :key="location.id"
                        class="border-b border-border last:border-0 hover:bg-muted/30"
                    >
                        <td class="p-4">
                            <div class="flex items-center">
                                <div class="w-10 h-10 rounded-full bg-muted mr-3 flex items-center justify-center">
                                    <MapPin class="h-5 w-5 text-muted-foreground" />
                                </div>
                                <div>
                                    <p class="font-medium text-foreground">{{ location.name }}</p>
                                </div>
                            </div>
                        </td>
                        <td class="p-4">
                            <span class="text-sm text-foreground">{{ location.concerts_count }}</span>
                        </td>
                        <td class="p-4">
                            <span class="text-sm text-muted-foreground">{{ new Date(location.created_at).toLocaleDateString() }}</span>
                        </td>
                        <td class="p-4">
                            <div class="flex items-center justify-end space-x-2">
                                <Link :href="route('admin.locations.show', location.id)">
                                    <Button variant="ghost" size="sm">
                                        <Eye class="h-4 w-4" />
                                    </Button>
                                </Link>
                                <Link :href="route('admin.locations.edit', location.id)">
                                    <Button variant="ghost" size="sm">
                                        <Edit class="h-4 w-4" />
                                    </Button>
                                </Link>
                                <Button
                                    variant="ghost"
                                    size="sm"
                                    @click="deleteLocation(location.id)"
                                    :disabled="location.concerts_count > 0"
                                >
                                    <Trash2 class="h-4 w-4" />
                                </Button>
                            </div>
                        </td>
                    </tr>
                    </tbody>
                </table>
            </div>

            <div v-if="locations.data.length === 0" class="p-8 text-center">
                <p class="text-muted-foreground">No locations found.</p>
            </div>
        </div>
    </AdminLayout>
</template>
