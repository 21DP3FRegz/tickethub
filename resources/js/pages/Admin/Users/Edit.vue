<script setup lang="ts">
import { Head, useForm, Link } from '@inertiajs/vue3';
import AdminLayout from '@/layouts/AdminLayout.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { RadioGroup, RadioGroupItem } from '@/components/ui/radio-group';
import { ArrowLeft } from 'lucide-vue-next';

const props = defineProps<{
    user: {
        id: number;
        name: string;
        email: string;
        roles: Array<{
            id: number;
            name: string;
        }>;
    };
    roles: Array<{
        id: number;
        name: string;
        description?: string;
    }>;
}>();

const form = useForm({
    name: props.user.name,
    email: props.user.email,
    password: '',
    password_confirmation: '',
    role: props.user.roles.length > 0 ? props.user.roles[0].id.toString() : '',
});

const submit = () => {
    // Convert the single role back to an array for the backend
    const formData = {
        ...form.data(),
        roles: form.role ? [parseInt(form.role)] : []
    };

    form.transform(() => formData).put(route('admin.users.update', props.user.id));
};

const getRoleColor = (roleName: string) => {
    switch (roleName) {
        case 'administrator':
            return 'bg-red-100 text-red-800';
        case 'customer':
            return 'bg-blue-100 text-blue-800';
        default:
            return 'bg-gray-100 text-gray-800';
    }
};
</script>

<template>
    <Head title="Edit User - Admin" />

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
            <h1 class="text-2xl font-bold text-foreground">Edit User</h1>
            <p class="text-muted-foreground">Update user information and roles</p>
        </div>

        <div class="max-w-2xl">
            <div class="bg-card rounded-lg border border-border shadow-sm p-6">
                <!-- Current User Display -->
                <div class="mb-6 p-4 bg-muted/30 rounded-lg border border-border">
                    <h3 class="text-sm font-medium text-muted-foreground mb-2">Current User</h3>
                    <div class="space-y-1">
                        <p class="text-foreground"><span class="font-medium">Name:</span> {{ user.name }}</p>
                        <p class="text-foreground"><span class="font-medium">Email:</span> {{ user.email }}</p>
                        <div class="flex items-center gap-2 mt-2">
                            <span class="text-sm font-medium">Current Role:</span>
                            <div class="flex flex-wrap gap-1">
                                <span
                                    v-for="role in user.roles"
                                    :key="role.id"
                                    :class="['px-2 py-1 rounded-full text-xs font-medium', getRoleColor(role.name)]"
                                >
                                    {{ role.name }}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>

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
                        <Label for="password">New Password</Label>
                        <Input
                            id="password"
                            v-model="form.password"
                            type="password"
                            class="mt-1"
                            placeholder="Leave blank to keep current password"
                        />
                        <p class="text-sm text-muted-foreground mt-1">
                            Leave blank to keep the current password
                        </p>
                        <p v-if="form.errors.password" class="text-destructive text-sm mt-1">
                            {{ form.errors.password }}
                        </p>
                    </div>

                    <div v-if="form.password">
                        <Label for="password_confirmation">Confirm New Password</Label>
                        <Input
                            id="password_confirmation"
                            v-model="form.password_confirmation"
                            type="password"
                            class="mt-1"
                            placeholder="Confirm new password"
                        />
                    </div>

                    <div>
                        <Label>Role</Label>
                        <RadioGroup v-model="form.role" class="mt-2">
                            <div v-for="role in roles" :key="role.id" class="flex items-center space-x-2">
                                <RadioGroupItem
                                    :value="role.id.toString()"
                                    :id="`role-${role.id}`"
                                />
                                <Label :for="`role-${role.id}`" class="text-sm font-normal cursor-pointer">
                                    <span class="font-medium">{{ role.name }}</span>
                                    <span v-if="role.description" class="text-muted-foreground"> - {{ role.description }}</span>
                                </Label>
                            </div>
                        </RadioGroup>
                        <p v-if="form.errors.roles || form.errors.role" class="text-destructive text-sm mt-1">
                            {{ form.errors.roles || form.errors.role }}
                        </p>
                    </div>

                    <div class="flex justify-end space-x-4">
                        <Link :href="route('admin.users.index')">
                            <Button variant="outline" type="button">
                                Cancel
                            </Button>
                        </Link>
                        <Button type="submit" :disabled="form.processing">
                            {{ form.processing ? 'Updating...' : 'Update User' }}
                        </Button>
                    </div>
                </form>
            </div>
        </div>
    </AdminLayout>
</template>
