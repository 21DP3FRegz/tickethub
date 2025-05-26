<script setup lang="ts">
import { Head, useForm, Link } from '@inertiajs/vue3';
import AdminLayout from '@/layouts/AdminLayout.vue';
import { Button } from '@/components/ui/button';
import { Label } from '@/components/ui/label';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';
import { ArrowLeft } from 'lucide-vue-next';

const props = defineProps<{
    concert: {
        id: number;
        artist_id: number;
        location_id: number;
        artist: {
            name: string;
        };
        location: {
            name: string;
        };
    };
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
    artist_id: props.concert.artist_id.toString(),
    location_id: props.concert.location_id.toString(),
});

const submit = () => {
    form.put(route('admin.concerts.update', props.concert.id));
};
</script>

<template>
    <Head title="Edit Concert - Admin" />

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
            <h1 class="text-2xl font-bold text-foreground">Edit Concert</h1>
            <p class="text-muted-foreground">Update concert artist and venue assignment</p>
        </div>

        <div class="max-w-2xl">
            <div class="bg-card rounded-lg border border-border shadow-sm p-6">
                <!-- Current Assignment Display -->
                <div class="mb-6 p-4 bg-muted/30 rounded-lg border border-border">
                    <h3 class="text-sm font-medium text-muted-foreground mb-2">Current Assignment</h3>
                    <div class="space-y-1">
                        <p class="text-foreground"><span class="font-medium">Artist:</span> {{ concert.artist.name }}</p>
                        <p class="text-foreground"><span class="font-medium">Location:</span> {{ concert.location.name }}</p>
                    </div>
                </div>

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
                            {{ form.processing ? 'Updating...' : 'Update Concert' }}
                        </Button>
                    </div>
                </form>
            </div>
        </div>
    </AdminLayout>
</template>
