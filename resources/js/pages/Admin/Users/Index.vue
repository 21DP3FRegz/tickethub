<script setup lang="ts">
import { Head, Link, router } from '@inertiajs/vue3';
import AdminLayout from '@/layouts/AdminLayout.vue';
import { Button } from '@/Components/ui/button';
import { Plus, Edit, Trash2, Eye, User, Mail, Shield } from 'lucide-vue-next';

const props = defineProps<{
    users: {
        data: Array<{
            id: number;
            name: string;
            email: string;
            roles: Array<{
                name: string;
            }>;
            bookings_count: number;
            reservations_count: number;
            created_at: string;
        }>;
        links: any;
        meta: any;
    };
}>();

const deleteUser = (id: number) => {
    if (confirm('Are you sure you want to delete this user?')) {
        router.delete(route('admin.users.destroy', id));
    }
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
    <Head title="Users - Admin" />

    <AdminLayout>
        <div class="flex items-center justify-between mb-6">
            <div>
                <h1 class="text-2xl font-bold text-foreground">Users</h1>
                <p class="text-muted-foreground">Manage system users and their roles</p>
            </div>
            <Link :href="route('admin.users.create')">
                <Button class="bg-primary text-primary-foreground hover:bg-primary/90">
                    <Plus class="h-4 w-4 mr-2" />
                    Add User
                </Button>
            </Link>
        </div>

        <div class="bg-card rounded-lg border border-border shadow-sm">
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="border-b border-border">
                    <tr>
                        <th class="text-left p-4 font-medium text-muted-foreground">User</th>
                        <th class="text-left p-4 font-medium text-muted-foreground">Roles</th>
                        <th class="text-left p-4 font-medium text-muted-foreground">Activity</th>
                        <th class="text-left p-4 font-medium text-muted-foreground">Joined</th>
                        <th class="text-right p-4 font-medium text-muted-foreground">Actions</th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr
                        v-for="user in users.data"
                        :key="user.id"
                        class="border-b border-border last:border-0 hover:bg-muted/30"
                    >
                        <td class="p-4">
                            <div class="flex items-center">
                                <div class="w-10 h-10 rounded-full bg-muted mr-3 flex items-center justify-center">
                                    <User class="h-5 w-5 text-muted-foreground" />
                                </div>
                                <div>
                                    <p class="font-medium text-foreground">{{ user.name }}</p>
                                    <div class="flex items-center text-sm text-muted-foreground">
                                        <Mail class="h-3 w-3 mr-1" />
                                        {{ user.email }}
                                    </div>
                                </div>
                            </div>
                        </td>
                        <td class="p-4">
                            <div class="flex flex-wrap gap-1">
                  <span
                      v-for="role in user.roles"
                      :key="role.name"
                      :class="['px-2 py-1 rounded-full text-xs font-medium', getRoleColor(role.name)]"
                  >
                    {{ role.name }}
                  </span>
                            </div>
                        </td>
                        <td class="p-4">
                            <div class="text-sm">
                                <p class="text-foreground">{{ user.bookings_count }} bookings</p>
                                <p class="text-muted-foreground">{{ user.reservations_count }} reservations</p>
                            </div>
                        </td>
                        <td class="p-4">
                            <span class="text-sm text-muted-foreground">{{ new Date(user.created_at).toLocaleDateString() }}</span>
                        </td>
                        <td class="p-4">
                            <div class="flex items-center justify-end space-x-2">
                                <Link :href="route('admin.users.show', user.id)">
                                    <Button variant="ghost" size="sm">
                                        <Eye class="h-4 w-4" />
                                    </Button>
                                </Link>
                                <Link :href="route('admin.users.edit', user.id)">
                                    <Button variant="ghost" size="sm">
                                        <Edit class="h-4 w-4" />
                                    </Button>
                                </Link>
                                <Button
                                    variant="ghost"
                                    size="sm"
                                    @click="deleteUser(user.id)"
                                    :disabled="user.bookings_count > 0 || user.reservations_count > 0"
                                >
                                    <Trash2 class="h-4 w-4" />
                                </Button>
                            </div>
                        </td>
                    </tr>
                    </tbody>
                </table>
            </div>

            <div v-if="users.data.length === 0" class="p-8 text-center">
                <p class="text-muted-foreground">No users found.</p>
            </div>
        </div>
    </AdminLayout>
</template>
