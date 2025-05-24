<script setup lang="ts">
import { Head, useForm, Link } from '@inertiajs/vue3';
import AdminLayout from '@/layouts/AdminLayout.vue';
import { Button } from '@/Components/ui/button';
import { Input } from '@/Components/ui/input';
import { Label } from '@/Components/ui/label';
import { Checkbox } from '@/Components/ui/checkbox';
import { ArrowLeft } from 'lucide-vue-next';

const props = defineProps<{
    roles: Array<{
        id: number;
        name: string;
        description: string;
    }>;
}>();

const form = useForm({
    name: '',
    email: '',
    password: '',
    password_confirmation: '',
    roles: [],
});

const submit = () => {
    form.post(route('admin.users.store'));
};

const toggleRole = (roleId: number) => {
    const index = form.roles.indexOf(roleId);
    if (index > -1) {
        form.roles.splice(index, 1);
    } else {
        form.roles.push(roleId);
    }
};
</script>

<template>
    <Head title="Create User - Admin" />

    <AdminLayout>
        <div class="mb-6">
            <div class="flex items-center mb-4">
                <Link :href="route('admin.users.index')" class="mr-4">
                    <Button variant="ghost" size="sm">
                        <ArrowLeft class="h-4 w-4 mr-2" />
                        Back to Users
                    </Button>
                </Link>
            </div>
            <h1 class="text-2xl font-bold text-foreground">Create User</h1>
            <p class="text-muted-foreground">Add a new user to the system</p>
        </div>

        <div class="max-w-2xl">
            <div class="bg-card rounded-lg border border-border shadow-sm p-6">
                <form @submit.prevent="submit" class="space-y-6">
                    <div>
                        <Label for="name">Full Name</Label>
                        <Input
                            id="name"
                            v-model="form.name"
                            type="text"
                            required
                            class="mt-1"
                            placeholder="Enter full name"
                        />
                        <p v-if="form.errors.name" class="text-destructive text-sm mt-1">
                            {{ form.errors.name }}
                        </p>
                    </div>

                    <div>
                        <Label for="email">Email Address</Label>
                        <Input
                            id="email"
                            v-model="form.email"
                            type="email"
                            required
                            class="mt-1"
                            placeholder="Enter email address"
                        />
                        <p v-if="form.errors.email" class="text-destructive text-sm mt-1">
                            {{ form.errors.email }}
                        </p>
                    </div>

                    <div>
                        <Label for="password">Password</Label>
                        <Input
                            id="password"
                            v-model="form.password"
                            type="password"
                            required
                            class="mt-1"
                            placeholder="Enter password"
                        />
                        <p v-if="form.errors.password" class="text-destructive text-sm mt-1">
                            {{ form.errors.password }}
                        </p>
                    </div>

                    <div>
                        <Label for="password_confirmation">Confirm Password</Label>
                        <Input
                            id="password_confirmation"
                            v-model="form.password_confirmation"
                            type="password"
                            required
                            class="mt-1"
                            placeholder="Confirm password"
                        />
                    </div>

                    <div>
                        <Label>Roles</Label>
                        <div class="mt-2 space-y-2">
                            <div v-for="role in roles" :key="role.id" class="flex items-center space-x-2">
                                <Checkbox
                                    :id="`role-${role.id}`"
                                    :checked="form.roles.includes(role.id)"
                                    @update:checked="toggleRole(role.id)"
                                />
                                <Label :for="`role-${role.id}`" class="text-sm font-normal">
                                    <span class="font-medium">{{ role.name }}</span>
                                    <span v-if="role.description" class="text-muted-foreground"> - {{ role.description }}</span>
                                </Label>
                            </div>
                        </div>
                        <p v-if="form.errors.roles" class="text-destructive text-sm mt-1">
                            {{ form.errors.roles }}
                        </p>
                    </div>

                    <div class="flex justify-end space-x-4">
                        <Link :href="route('admin.users.index')">
                            <Button variant="outline" type="button">
                                Cancel
                            </Button>
                        </Link>
                        <Button type="submit" :disabled="form.processing">
                            {{ form.processing ? 'Creating...' : 'Create User' }}
                        </Button>
                    </div>
                </form>
            </div>
        </div>
    </AdminLayout>
</template>
