<script setup lang="ts">
import { Head, Link, useForm, router, usePage } from '@inertiajs/vue3';
import AppSidebarLayout from '@/layouts/app/AppSidebarLayout.vue';
import { Button } from '@/Components/ui/button';
import {
    CalendarDays, MapPin, Ticket, User, Clock, CreditCard,
    Printer, QrCode, ArrowLeft, AlertCircle
} from 'lucide-vue-next';
import { ref, onMounted } from 'vue';
import {
    AlertDialog,
    AlertDialogAction,
    AlertDialogCancel,
    AlertDialogContent,
    AlertDialogDescription,
    AlertDialogFooter,
    AlertDialogHeader,
    AlertDialogTitle,
    AlertDialogTrigger,
} from "@/Components/ui/alert-dialog";

const props = defineProps<{
    booking: {
        id: number;
        name: string;
        address: string;
        city: string;
        zip: string;
        country: string;
        created_at: string;
        tickets: Array<{
            id: number;
            code: string;
            name: string;
            show: {
                id: number;
                start: string;
                end: string;
                concert: {
                    artist: {
                        name: string;
                    }
                    location: {
                        name: string;
                    }
                }
            };
            seat: {
                seat_number: string;
                row: {
                    name: string;
                }
            };
        }>;
    };
}>();

const page = usePage();
const form = useForm({});

const cancelBooking = () => {
    router.delete(route('bookings.destroy', props.booking.id), {
        onSuccess: () => {
            form.reset();
            router.visit('/bookings');
        },
    });
};

// Format date for display
const formatDate = (dateString) => {
    const date = new Date(dateString);
    return {
        day: date.getDate(),
        month: date.toLocaleString('default', { month: 'short' }),
        year: date.getFullYear(),
        time: date.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' }),
        full: date.toLocaleString()
    };
};

// Function to print ticket
const printTicket = (ticketId) => {
    // In a real implementation, this would open a print-friendly version
    window.print();
};

// Get ticket ID from URL query parameter
const getTicketIdFromUrl = () => {
    const urlParams = new URLSearchParams(window.location.search);
    const ticketParam = urlParams.get('ticket');
    return ticketParam ? parseInt(ticketParam, 10) : null;
};

// Active ticket for detailed view
const activeTicketId = ref(null);

// Initialize the active ticket ID
const initializeActiveTicket = () => {
    const urlTicketId = getTicketIdFromUrl();

    // If we have a ticket ID in the URL and it exists in our booking tickets
    if (urlTicketId && props.booking.tickets.some(ticket => ticket.id === urlTicketId)) {
        activeTicketId.value = urlTicketId;
    } else {
        // Default to first ticket if no valid ticket ID in URL
        activeTicketId.value = props.booking.tickets.length > 0 ? props.booking.tickets[0].id : null;
    }
};

// Set up watchers and initialization
onMounted(() => {
    initializeActiveTicket();

    // Add popstate event listener to handle browser back/forward navigation
    window.addEventListener('popstate', () => {
        initializeActiveTicket();
    });
});

const setActiveTicket = (ticketId) => {
    activeTicketId.value = ticketId;

    // Update URL to reflect the selected ticket without full page reload
    const url = new URL(window.location.href);
    url.searchParams.set('ticket', ticketId.toString());
    window.history.pushState({}, '', url.toString());
};

const getActiveTicket = () => {
    return props.booking.tickets.find(ticket => ticket.id === activeTicketId.value);
};

// Alert dialog state
const isAlertOpen = ref(false);
</script>

<template>
    <Head title="Booking Details" />
    <AppSidebarLayout :breadcrumbs="[
        { title: 'Bookings', href: '/bookings' },
        { title: 'Booking Details' }
    ]">
        <!-- Header Section -->
        <div class="mb-8 p-4">
            <h1 class="text-2xl font-bold mb-2">Booking Details</h1>
            <p class="text-muted-foreground">View and manage your booking information and tickets.</p>
        </div>

        <!-- Action Buttons - Repositioned to top of content -->
        <div class="px-4 mb-6 flex justify-between items-center">
            <Link href="/bookings" class="text-primary hover:underline flex items-center">
                <ArrowLeft class="h-4 w-4 mr-1" />
                Back to All Bookings
            </Link>

            <AlertDialog>
                <AlertDialogTrigger as-child>
                    <Button
                        variant="outline"
                        class="border-destructive text-destructive hover:bg-destructive/10"
                        :disabled="form.processing"
                    >
                        Cancel Booking
                    </Button>
                </AlertDialogTrigger>
                <AlertDialogContent class="sm:max-w-[425px]">
                    <AlertDialogHeader>
                        <AlertDialogTitle class="flex items-center">
                            <AlertCircle class="h-5 w-5 mr-2 text-destructive" />
                            Cancel Booking
                        </AlertDialogTitle>
                        <AlertDialogDescription>
                            Are you sure you want to cancel this booking? This action cannot be undone and all tickets will be invalidated.
                        </AlertDialogDescription>
                    </AlertDialogHeader>
                    <AlertDialogFooter>
                        <AlertDialogCancel>Keep Booking</AlertDialogCancel>
                        <AlertDialogAction
                            @click="cancelBooking"
                            class="bg-destructive text-destructive-foreground hover:bg-destructive/90"
                        >
                            Cancel Booking
                        </AlertDialogAction>
                    </AlertDialogFooter>
                </AlertDialogContent>
            </AlertDialog>
        </div>

        <div class="p-4">
            <!-- Booking Information Card -->
            <div class="bg-card rounded-xl shadow-sm mb-6 overflow-hidden">
                <div class="bg-primary/10 p-4 border-b border-border">
                    <h2 class="text-lg font-medium flex items-center">
                        <CreditCard class="h-5 w-5 mr-2 text-primary" />
                        Booking Information
                    </h2>
                </div>

                <div class="p-4">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Customer Information -->
                        <div>
                            <h3 class="text-sm font-medium text-muted-foreground mb-3 flex items-center">
                                <User class="h-4 w-4 mr-1" />
                                Customer Details
                            </h3>
                            <div class="space-y-2">
                                <p class="font-medium">{{ booking.name }}</p>
                                <p class="text-sm text-muted-foreground">{{ booking.address }}</p>
                                <p class="text-sm text-muted-foreground">{{ booking.city }}, {{ booking.zip }}</p>
                                <p class="text-sm text-muted-foreground">{{ booking.country }}</p>
                            </div>
                        </div>

                        <!-- Booking Details -->
                        <div>
                            <h3 class="text-sm font-medium text-muted-foreground mb-3 flex items-center">
                                <Clock class="h-4 w-4 mr-1" />
                                Booking Details
                            </h3>
                            <div class="space-y-2">
                                <p class="text-sm">
                                    <span class="font-medium">Booking ID:</span>
                                    <span class="font-mono">{{ booking.id }}</span>
                                </p>
                                <p class="text-sm">
                                    <span class="font-medium">Date:</span>
                                    {{ formatDate(booking.created_at).full }}
                                </p>
                                <p class="text-sm">
                                    <span class="font-medium">Number of Tickets:</span>
                                    {{ booking.tickets.length }}
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Tickets Section -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Ticket List -->
                <div class="lg:col-span-1">
                    <div class="bg-card rounded-xl shadow-sm overflow-hidden h-full">
                        <div class="bg-primary/10 p-4 border-b border-border">
                            <h2 class="text-lg font-medium flex items-center">
                                <Ticket class="h-5 w-5 mr-2 text-primary" />
                                Your Tickets
                            </h2>
                        </div>

                        <div class="p-2">
                            <div class="space-y-2">
                                <button
                                    v-for="ticket in booking.tickets"
                                    :key="ticket.id"
                                    @click="setActiveTicket(ticket.id)"
                                    class="w-full text-left p-3 rounded-lg transition-colors"
                                    :class="activeTicketId === ticket.id ?
                                        'bg-primary/10 border border-primary/30' :
                                        'hover:bg-muted/50 border border-transparent'"
                                >
                                    <div class="font-medium truncate">{{ ticket.show.concert.artist.name }}</div>
                                    <div class="flex items-center text-xs text-muted-foreground mt-1">
                                        <CalendarDays class="h-3 w-3 mr-1" />
                                        <span>{{ formatDate(ticket.show.start).month }} {{ formatDate(ticket.show.start).day }}, {{ formatDate(ticket.show.start).time }}</span>
                                    </div>
                                    <div class="flex items-center text-xs text-muted-foreground mt-1">
                                        <MapPin class="h-3 w-3 mr-1" />
                                        <span>Seat: {{ ticket.seat.seat_number }}</span>
                                    </div>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Ticket Details -->
                <div class="lg:col-span-2">
                    <div v-if="getActiveTicket()" class="bg-card rounded-xl shadow-sm overflow-hidden">
                        <div class="bg-primary/10 p-4 border-b border-border flex justify-between items-center">
                            <h2 class="text-lg font-medium flex items-center">
                                <Ticket class="h-5 w-5 mr-2 text-primary" />
                                Ticket Details
                            </h2>
                            <div class="flex gap-2">
                                <Button
                                    variant="outline"
                                    size="sm"
                                    @click="printTicket(getActiveTicket().id)"
                                    class="flex items-center"
                                >
                                    <Printer class="h-4 w-4 mr-1" />
                                    Print
                                </Button>
                            </div>
                        </div>

                        <div class="p-4">
                            <div class="bg-muted/30 rounded-lg p-6 border border-border">
                                <!-- Ticket Header -->
                                <div class="flex justify-between items-start mb-6">
                                    <div>
                                        <h3 class="text-xl font-bold">{{ getActiveTicket().show.concert.artist?.name }}</h3>
                                        <p class="text-muted-foreground">{{ getActiveTicket().show.concert.location?.name || 'Venue' }}</p>
                                    </div>
                                    <div class="bg-primary/10 px-3 py-1 rounded-full text-primary text-sm font-medium">
                                        Confirmed
                                    </div>
                                </div>

                                <!-- Ticket Details -->
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                                    <div>
                                        <h4 class="text-sm font-medium text-muted-foreground mb-2">Event Details</h4>
                                        <div class="space-y-2">
                                            <div class="flex items-center">
                                                <CalendarDays class="h-4 w-4 mr-2 text-primary" />
                                                <span>{{ formatDate(getActiveTicket().show.start).full }}</span>
                                            </div>
                                            <div class="flex items-center">
                                                <Clock class="h-4 w-4 mr-2 text-primary" />
                                                <span>{{ formatDate(getActiveTicket().show.start).time }} - {{ formatDate(getActiveTicket().show.end).time }}</span>
                                            </div>
                                            <div class="flex items-center">
                                                <MapPin class="h-4 w-4 mr-2 text-primary" />
                                                <span>{{ getActiveTicket().show.concert.location?.name || 'Venue' }}</span>
                                            </div>
                                        </div>
                                    </div>

                                    <div>
                                        <h4 class="text-sm font-medium text-muted-foreground mb-2">Seat Information</h4>
                                        <div class="space-y-2">
                                            <div class="flex items-center">
                                                <User class="h-4 w-4 mr-2 text-primary" />
                                                <span>{{ booking.name }}</span>
                                            </div>
                                            <div class="flex items-center">
                                                <MapPin class="h-4 w-4 mr-2 text-primary" />
                                                <span>Row: {{ getActiveTicket().seat.row?.name || 'N/A' }}, Seat: {{ getActiveTicket().seat.seat_number }}</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Ticket Code -->
                                <div class="border-t border-border pt-6">
                                    <h4 class="text-sm font-medium text-muted-foreground mb-2 flex items-center">
                                        <QrCode class="h-4 w-4 mr-1" />
                                        Ticket Code
                                    </h4>
                                    <div class="bg-secondary/50 p-4 rounded-md font-mono text-center text-lg select-all">
                                        {{ getActiveTicket().code }}
                                    </div>
                                    <div class="text-center text-xs text-muted-foreground mt-2">
                                        Present this code at the venue entrance
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div v-else class="bg-card rounded-xl shadow-sm p-8 text-center">
                        <div class="inline-flex items-center justify-center w-12 h-12 rounded-full bg-muted mb-4">
                            <Ticket class="h-6 w-6 text-muted-foreground" />
                        </div>
                        <h3 class="text-lg font-medium mb-1">No Ticket Selected</h3>
                        <p class="text-muted-foreground mb-4">Please select a ticket from the list to view details.</p>
                    </div>
                </div>
            </div>
        </div>
    </AppSidebarLayout>
</template>
