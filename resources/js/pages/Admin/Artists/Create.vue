<script setup lang="ts">
import { Head, Link, useForm } from '@inertiajs/vue3';
import AdminLayout from '@/layouts/AdminLayout.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Textarea } from '@/components/ui/textarea';
import { ArrowLeft } from 'lucide-vue-next';

const form = useForm({
    name: '',
    bio: '',
    image_url: '',
});

const submit = () => {
    form.post(route('admin.artists.store'));
};
</script>

<template>
    <Head title="Create Artist - Admin" />

    <AdminLayout>
        <div class="p-6">
            <div class="mb-6">
                <div class="flex items-center mb-4">
                    <Link :href="route('admin.artists.index')" class="mr-4">
                        <Button variant="ghost" size="sm">
                            <ArrowLeft class="h-4 w-4 mr-2" />
                            Back to Artists
                        </Button>
                    </Link>
                </div>
                <h1 class="text-2xl font-bold text-foreground">Create Artist</h1>
                <p class="text-muted-foreground">Add a new artist to your system</p>
            </div>

            <div class="max-w-2xl">
                <div class="bg-card rounded-lg border border-border shadow-sm p-6">
                    <form @submit.prevent="submit" class="space-y-6">
                        <div>
                            <Label for="name">Artist Name</Label>
                            <Input
                                id="name"
                                v-model="form.name"
                                type="text"
                                required
                                class="mt-1"
                                placeholder="Enter artist name"
                            />
                            <p v-if="form.errors.name" class="text-destructive text-sm mt-1">
                                {{ form.errors.name }}
                            </p>
                        </div>

                        <div>
                            <Label for="bio">Biography</Label>
                            <Textarea
                                id="bio"
                                v-model="form.bio"
                                class="mt-1"
                                rows="4"
                                placeholder="Enter artist biography (optional)"
                            />
                            <p v-if="form.errors.bio" class="text-destructive text-sm mt-1">
                                {{ form.errors.bio }}
                            </p>
                        </div>

                        <div>
                            <Label for="image_url">Image URL</Label>
                            <Input
                                id="image_url"
                                v-model="form.image_url"
                                type="url"
                                class="mt-1"
                                placeholder="https://example.com/image.jpg (optional)"
                            />
                            <p v-if="form.errors.image_url" class="text-destructive text-sm mt-1">
                                {{ form.errors.image_url }}
                            </p>
                        </div>

                        <div class="flex justify-end space-x-4">
                            <Link :href="route('admin.artists.index')">
                                <Button variant="outline" type="button">
                                    Cancel
                                </Button>
                            </Link>
                            <Button type="submit" :disabled="form.processing">
                                {{ form.processing ? 'Creating...' : 'Create Artist' }}
                            </Button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </AdminLayout>
</template>
