<script setup lang="ts">
import { Head, useForm, Link } from '@inertiajs/vue3';
import AdminLayout from '@/layouts/AdminLayout.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { ArrowLeft } from 'lucide-vue-next';

const props = defineProps<{
    location: {
        id: number;
        name: string;
    };
}>();

const form = useForm({
    name: props.location.name,
});

const submit = () => {
    form.put(route('admin.locations.update', props.location.id));
};
</script>

<template>
    <Head title="Edit Location - Admin" />

    <AdminLayout>
        <div class="mb-6">
            <div class="flex items-center mb-4">
                <Link :href="route('admin.locations.index')" class="mr-4">
                    <Button variant="ghost" size="sm">
                        <ArrowLeft class="h-4 w-4 mr-2" />
                        Back to Locations
                    </Button>
                </Link>
            </div>
            <h1 class="text-2xl font-bold text-foreground">Edit Location</h1>
            <p class="text-muted-foreground">Update location information</p>
        </div>

        <div class="max-w-2xl">
            <div class="bg-card rounded-lg border border-border shadow-sm p-6">
                <form @submit.prevent="submit" class="space-y-6">
                    <div>
                        <Label for="name">Location Name</Label>
                        <Input
                            id="name"
                            v-model="form.name"
                            type="text"
                            required
                            class="mt-1"
                            placeholder="Enter location name"
                        />
                        <p v-if="form.errors.name" class="text-destructive text-sm mt-1">
                            {{ form.errors.name }}
                        </p>
                    </div>

                    <div class="flex justify-end space-x-4">
                        <Link :href="route('admin.locations.index')">
                            <Button variant="outline" type="button">
                                Cancel
                            </Button>
                        </Link>
                        <Button type="submit" :disabled="form.processing">
                            {{ form.processing ? 'Updating...' : 'Update Location' }}
                        </Button>
                    </div>
                </form>
            </div>
        </div>
    </AdminLayout>
</template>
