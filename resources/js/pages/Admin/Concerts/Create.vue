<script setup lang="ts">
import { Head, useForm, Link } from '@inertiajs/vue3';
import AdminLayout from '@/layouts/AdminLayout.vue';
import { Button } from '@/components/ui/button';
import { Label } from '@/components/ui/label';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';
import { ArrowLeft } from 'lucide-vue-next';

defineProps<{
    artists: Array<{
        id: number;
        name: string;
    }>;
    locations: Array<{
        id: number;
        name: string;
    }>;
}>();

const form = useForm({
    artist_id: '',
    location_id: '',
});

const submit = () => {
    form.post(route('admin.concerts.store'));
};
</script>

<template>
    <Head title="Create Concert - Admin" />

    <AdminLayout>
        <div class="mb-6">
            <div class="flex items-center mb-4">
                <Link :href="route('admin.concerts.index')" class="mr-4">
                    <Button variant="ghost" size="sm">
                        <ArrowLeft class="h-4 w-4 mr-2" />
                        Back to Concerts
                    </Button>
                </Link>
            </div>
            <h1 class="text-2xl font-bold text-foreground">Create Concert</h1>
            <p class="text-muted-foreground">Link an artist to a venue for a new concert</p>
        </div>

        <div class="max-w-2xl">
            <div class="bg-card rounded-lg border border-border shadow-sm p-6">
                <form @submit.prevent="submit" class="space-y-6">
                    <div>
                        <Label for="artist_id">Artist</Label>
                        <Select v-model="form.artist_id" required>
                            <SelectTrigger class="mt-1">
                                <SelectValue placeholder="Select an artist" />
                            </SelectTrigger>
                            <SelectContent>
                                <SelectItem v-for="artist in artists" :key="artist.id" :value="artist.id.toString()">
                                    {{ artist.name }}
                                </SelectItem>
                            </SelectContent>
                        </Select>
                        <p v-if="form.errors.artist_id" class="text-destructive text-sm mt-1">
                            {{ form.errors.artist_id }}
                        </p>
                    </div>

                    <div>
                        <Label for="location_id">Location</Label>
                        <Select v-model="form.location_id" required>
                            <SelectTrigger class="mt-1">
                                <SelectValue placeholder="Select a location" />
                            </SelectTrigger>
                            <SelectContent>
                                <SelectItem v-for="location in locations" :key="location.id" :value="location.id.toString()">
                                    {{ location.name }}
                                </SelectItem>
                            </SelectContent>
                        </Select>
                        <p v-if="form.errors.location_id" class="text-destructive text-sm mt-1">
                            {{ form.errors.location_id }}
                        </p>
                    </div>

                    <div class="flex justify-end space-x-4">
                        <Link :href="route('admin.concerts.index')">
                            <Button variant="outline" type="button">
                                Cancel
                            </Button>
                        </Link>
                        <Button type="submit" :disabled="form.processing">
                            {{ form.processing ? 'Creating...' : 'Create Concert' }}
                        </Button>
                    </div>
                </form>
            </div>
        </div>
    </AdminLayout>
</template>
