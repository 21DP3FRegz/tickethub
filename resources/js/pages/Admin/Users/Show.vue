<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import AdminLayout from '@/layouts/AdminLayout.vue';
import { Button } from '@/Components/ui/button';
import { ArrowLeft, Edit, User, Mail, Shield, Calendar, Ticket, Clock } from 'lucide-vue-next';

const props = defineProps<{
    user: {
        id: number;
        name: string;
        email: string;
        email_verified_at: string;
        created_at: string;
        updated_at: string;
        roles: Array<{
            id: number;
            name: string;
            description: string;
        }>;
        bookings: Array<{
            id: number;
            created_at: string;
            tickets: Array<{
                show: {
                    concert: {
                        artist: {
                            name: string;
                        };
                    };
                };
            }>;
        }>;
        reservations: Array<{
            id: number;
            created_at: string;
            reserved_until: string;
            show: {
                concert: {
                    artist: {
                        name: string;
                    };
                };
            };
        }>;
    };
}>();

const formatDate = (dateString: string) => {
    return new Date(dateString).toLocaleDateString('en-US', {
        year: 'numeric',
        month: 'long',
        day: 'numeric',
        hour: '2-digit',
        minute: '2-digit'
    });
};

const formatShortDate = (dateString: string) => {
    return new Date(dateString).toLocaleDateString('en-US', {
        year: 'numeric',
        month: 'short',
        day: 'numeric'
    });
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

const getTotalTickets = () => {
    return props.user.bookings.reduce((total, booking) => total + booking.tickets.length, 0);
};

const getActiveReservations = () => {
    const now = new Date();
    return props.user.reservations.filter(reservation =>
        new Date(reservation.reserved_until) > now
    ).length;
};
</script>

<template>
    <Head :title="`${user.name} - Admin`" />

    <AdminLayout>
        <div class="mb-6">
            <div class="flex items-center justify-between mb-4">
                <Link :href="route('admin.users.index')">
                    <Button variant="ghost" size="sm">
                        <ArrowLeft class="h-4 w-4 mr-2" />
                        Back to Users
                    </Button>
                </Link>
                <Link :href="route('admin.users.edit', user.id)">
                    <Button>
                        <Edit class="h-4 w-4 mr-2" />
                        Edit User
                    </Button>
                </Link>
            </div>
            <h1 class="text-2xl font-bold text-foreground">{{ user.name }}</h1>
            <p class="text-muted-foreground">User profile and activity details</p>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- User Details -->
            <div class="lg:col-span-2 space-y-6">
                <!-- Basic Information -->
                <div class="bg-card rounded-lg border border-border shadow-sm p-6">
                    <h2 class="text-lg font-semibold mb-4 flex items-center">
                        <User class="h-5 w-5 mr-2 text-primary" />
                        User Information
                    </h2>

                    <div class="space-y-4">
                        <div>
                            <label class="text-sm font-medium text-muted-foreground">Full Name</label>
                            <p class="text-foreground">{{ user.name }}</p>
                        </div>

                        <div>
                            <label class="text-sm font-medium text-muted-foreground">Email Address</label>
                            <div class="flex items-center">
                                <Mail class="h-4 w-4 mr-2 text-muted-foreground" />
                                <p class="text-foreground">{{ user.email }}</p>
                            </div>
                        </div>

                        <div>
                            <label class="text-sm font-medium text-muted-foreground">Email Verification</label>
                            <div class="flex items-center">
                                <div :class="[
                  'w-2 h-2 rounded-full mr-2',
                  user.email_verified_at ? 'bg-green-500' : 'bg-red-500'
                ]"></div>
                                <p class="text-foreground">
                                    {{ user.email_verified_at ? `Verified on ${formatShortDate(user.email_verified_at)}` : 'Not verified' }}
                                </p>
                            </div>
                        </div>

                        <div>
                            <label class="text-sm font-medium text-muted-foreground">Roles</label>
                            <div class="flex flex-wrap gap-2 mt-1">
                <span
                    v-for="role in user.roles"
                    :key="role.id"
                    :class="['px-3 py-1 rounded-full text-sm font-medium flex items-center', getRoleColor(role.name)]"
                >
                  <Shield class="h-3 w-3 mr-1" />
                  {{ role.name }}
                </span>
                            </div>
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="text-sm font-medium text-muted-foreground">Member Since</label>
                                <p class="text-foreground">{{ formatDate(user.created_at) }}</p>
                            </div>
                            <div>
                                <label class="text-sm font-medium text-muted-foreground">Last Updated</label>
                                <p class="text-foreground">{{ formatDate(user.updated_at) }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Bookings -->
                <div class="bg-card rounded-lg border border-border shadow-sm p-6">
                    <h2 class="text-lg font-semibold mb-4 flex items-center">
                        <Ticket class="h-5 w-5 mr-2 text-primary" />
                        Bookings ({{ user.bookings.length }})
                    </h2>

                    <div v-if="user.bookings.length > 0" class="space-y-4">
                        <div
                            v-for="booking in user.bookings"
                            :key="booking.id"
                            class="p-4 bg-muted/30 rounded-lg border border-border"
                        >
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="font-medium text-foreground">
                                        {{ booking.tickets[0]?.show?.concert?.artist?.name || 'Unknown Artist' }}
                                    </p>
                                    <div class="flex items-center text-sm text-muted-foreground">
                                        <Calendar class="h-4 w-4 mr-1" />
                                        {{ formatShortDate(booking.created_at) }}
                                    </div>
                                </div>
                                <div class="text-right">
                                    <p class="text-sm font-medium text-foreground">{{ booking.tickets.length }} ticket{{ booking.tickets.length !== 1 ? 's' : '' }}</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div v-else class="text-center py-8">
                        <p class="text-muted-foreground">No bookings found for this user.</p>
                    </div>
                </div>

                <!-- Reservations -->
                <div class="bg-card rounded-lg border border-border shadow-sm p-6">
                    <h2 class="text-lg font-semibold mb-4 flex items-center">
                        <Clock class="h-5 w-5 mr-2 text-primary" />
                        Reservations ({{ user.reservations.length }})
                    </h2>

                    <div v-if="user.reservations.length > 0" class="space-y-4">
                        <div
                            v-for="reservation in user.reservations"
                            :key="reservation.id"
                            class="p-4 bg-muted/30 rounded-lg border border-border"
                        >
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="font-medium text-foreground">
                                        {{ reservation.show?.concert?.artist?.name || 'Unknown Artist' }}
                                    </p>
                                    <div class="flex items-center text-sm text-muted-foreground">
                                        <Calendar class="h-4 w-4 mr-1" />
                                        {{ formatShortDate(reservation.created_at) }}
                                    </div>
                                </div>
                                <div class="text-right">
                                    <div :class="[
                    'px-2 py-1 rounded-full text-xs font-medium',
                    new Date(reservation.reserved_until) > new Date()
                      ? 'bg-yellow-100 text-yellow-800'
                      : 'bg-gray-100 text-gray-800'
                  ]">
                                        {{ new Date(reservation.reserved_until) > new Date() ? 'Active' : 'Expired' }}
                                    </div>
                                    <p class="text-xs text-muted-foreground mt-1">
                                        Until {{ formatShortDate(reservation.reserved_until) }}
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div v-else class="text-center py-8">
                        <p class="text-muted-foreground">No reservations found for this user.</p>
                    </div>
                </div>
            </div>

            <!-- Sidebar -->
            <div class="space-y-6">
                <!-- User Avatar -->
                <div class="bg-card rounded-lg border border-border shadow-sm p-6">
                    <h2 class="text-lg font-semibold mb-4">Profile</h2>

                    <div class="text-center">
                        <div class="w-24 h-24 rounded-full bg-muted mx-auto mb-4 flex items-center justify-center">
                            <span class="text-3xl font-bold text-primary">{{ user.name.charAt(0).toUpperCase() }}</span>
                        </div>
                        <p class="font-medium text-foreground">{{ user.name }}</p>
                        <p class="text-sm text-muted-foreground">{{ user.email }}</p>
                    </div>
                </div>

                <!-- Statistics -->
                <div class="bg-card rounded-lg border border-border shadow-sm p-6">
                    <h2 class="text-lg font-semibold mb-4">Activity Statistics</h2>

                    <div class="space-y-4">
                        <div class="flex items-center justify-between p-3 bg-muted/30 rounded-lg">
                            <div class="flex items-center">
                                <Ticket class="h-5 w-5 text-green-600 mr-2" />
                                <span class="text-sm font-medium">Total Bookings</span>
                            </div>
                            <span class="text-lg font-bold text-green-600">{{ user.bookings.length }}</span>
                        </div>

                        <div class="flex items-center justify-between p-3 bg-muted/30 rounded-lg">
                            <div class="flex items-center">
                                <Ticket class="h-5 w-5 text-blue-600 mr-2" />
                                <span class="text-sm font-medium">Total Tickets</span>
                            </div>
                            <span class="text-lg font-bold text-blue-600">{{ getTotalTickets() }}</span>
                        </div>

                        <div class="flex items-center justify-between p-3 bg-muted/30 rounded-lg">
                            <div class="flex items-center">
                                <Clock class="h-5 w-5 text-yellow-600 mr-2" />
                                <span class="text-sm font-medium">Active Reservations</span>
                            </div>
                            <span class="text-lg font-bold text-yellow-600">{{ getActiveReservations() }}</span>
                        </div>

                        <div class="flex items-center justify-between p-3 bg-muted/30 rounded-lg">
                            <div class="flex items-center">
                                <Clock class="h-5 w-5 text-orange-600 mr-2" />
                                <span class="text-sm font-medium">Total Reservations</span>
                            </div>
                            <span class="text-lg font-bold text-orange-600">{{ user.reservations.length }}</span>
                        </div>
                    </div>
                </div>

                <!-- Role Information -->
                <div class="bg-card rounded-lg border border-border shadow-sm p-6">
                    <h2 class="text-lg font-semibold mb-4">Role Details</h2>

                    <div class="space-y-3">
                        <div v-for="role in user.roles" :key="role.id" class="p-3 bg-muted/30 rounded-lg">
                            <div class="flex items-center mb-1">
                                <Shield class="h-4 w-4 mr-2 text-primary" />
                                <span class="font-medium text-foreground">{{ role.name }}</span>
                            </div>
                            <p v-if="role.description" class="text-sm text-muted-foreground">{{ role.description }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AdminLayout>
</template>
