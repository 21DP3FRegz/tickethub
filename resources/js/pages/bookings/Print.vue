<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import { onMounted, computed } from 'vue';
import { Button } from '@/components/ui/button';
import { Card, CardContent } from '@/components/ui/card';
import {
    Printer,
    ArrowLeft,
    Calendar,
    MapPin,
    User,
    Clock
} from 'lucide-vue-next';

const props = defineProps<{
    booking: {
        id: number;
        name: string;
        address: string;
        city: string;
        zip: string;
        country: string;
        email: string;
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

// Format date for display
const formatDate = (dateString: string) => {
    const date = new Date(dateString);
    return {
        day: date.getDate(),
        month: date.toLocaleString('default', { month: 'short' }),
        year: date.getFullYear(),
        time: date.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' }),
        full: date.toLocaleString()
    };
};

// Calculate grid columns based on ticket count
const gridColumns = computed(() => {
    const ticketCount = props.booking.tickets.length;
    if (ticketCount === 1) return 1;
    if (ticketCount === 2) return 2;
    if (ticketCount === 3) return 3;
    if (ticketCount >= 4) return 2; // 2x2 grid for 4+ tickets
    return 2;
});

// Calculate if we need page breaks
const needsPageBreak = computed(() => {
    return props.booking.tickets.length > 4;
});

// Print function
const handlePrint = () => {
    window.print();
};

// Auto-open print dialog when page loads
onMounted(() => {
    // Force light theme
    document.documentElement.classList.remove('dark');
    document.body.classList.remove('dark');

    // Small delay to ensure page is fully rendered
    setTimeout(() => {
        window.print();
    }, 500);
});
</script>

<template>
    <Head title="Print Tickets" />

    <!-- Force light theme wrapper -->
    <div class="light-theme-wrapper">
        <!-- Screen-only controls -->
        <div class="no-print fixed top-4 right-4 flex gap-2 z-50 bg-white p-2 rounded shadow">
            <Button @click="handlePrint" variant="default" size="sm">
                <Printer class="h-4 w-4 mr-2" />
                Print Again
            </Button>
            <Link :href="route('bookings.show', booking.id)">
                <Button variant="outline" size="sm">
                    <ArrowLeft class="h-4 w-4 mr-2" />
                    Back
                </Button>
            </Link>
        </div>

        <!-- Print Container -->
        <div class="print-container">
            <!-- Tickets Grid -->
            <div
                class="tickets-wrapper"
                :style="{
                    gridTemplateColumns: `repeat(${gridColumns}, 1fr)`,
                    gap: gridColumns === 1 ? '0' : (gridColumns === 3 ? '0.5rem' : '1rem')
                }"
            >
                <div
                    v-for="(ticket, index) in booking.tickets"
                    :key="ticket.id"
                    :class="[
                        'ticket-item',
                        { 'page-break': needsPageBreak && index > 0 && index % 4 === 0 }
                    ]"
                >
                    <!-- Fixed Card Structure -->
                    <div class="ticket-card bg-white border-2 border-gray-400 rounded-lg overflow-hidden">
                        <!-- Ticket Code Header - Fixed alignment -->
                        <div class="ticket-code-header bg-purple-600 text-white p-4 text-center">
                            <div class="text-xs uppercase tracking-wide mb-1 opacity-90">Ticket Code</div>
                            <div
                                :class="[
                                    'font-bold font-mono tracking-wider',
                                    gridColumns === 1 ? 'text-4xl' :
                                    gridColumns === 2 ? 'text-2xl' :
                                    'text-xl'
                                ]"
                            >
                                {{ ticket.code }}
                            </div>
                        </div>

                        <!-- Card Content -->
                        <div
                            :class="[
                                'ticket-content',
                                gridColumns === 1 ? 'p-8' :
                                gridColumns === 2 ? 'p-4' :
                                'p-3'
                            ]"
                        >
                            <!-- Artist & Venue -->
                            <div
                                :class="[
                                    'text-center',
                                    gridColumns === 1 ? 'mb-8' :
                                    gridColumns === 2 ? 'mb-4' :
                                    'mb-3'
                                ]"
                            >
                                <h2
                                    :class="[
                                        'font-bold text-gray-900',
                                        gridColumns === 1 ? 'text-3xl mb-3' :
                                        gridColumns === 2 ? 'text-xl mb-2' :
                                        'text-lg mb-1'
                                    ]"
                                >
                                    {{ ticket.show.concert.artist.name }}
                                </h2>
                                <p
                                    :class="[
                                        'text-gray-600 flex items-center justify-center',
                                        gridColumns === 1 ? 'text-xl' :
                                        gridColumns === 2 ? 'text-base' :
                                        'text-sm'
                                    ]"
                                >
                                    <MapPin :class="gridColumns === 1 ? 'h-6 w-6 mr-2' : 'h-4 w-4 mr-1'" />
                                    {{ ticket.show.concert.location?.name || 'Venue TBA' }}
                                </p>
                            </div>

                            <!-- Event Details -->
                            <div
                                :class="[
                                    'grid grid-cols-2',
                                    gridColumns === 1 ? 'gap-8 mb-8' :
                                    gridColumns === 2 ? 'gap-4 mb-4' :
                                    'gap-2 mb-3'
                                ]"
                            >
                                <div class="text-center">
                                    <div class="flex items-center justify-center mb-1">
                                        <Calendar :class="gridColumns === 1 ? 'h-6 w-6 mr-2' : 'h-4 w-4 mr-1'" class="text-purple-600" />
                                        <span
                                            :class="[
                                                'uppercase tracking-wide text-gray-600 font-semibold',
                                                gridColumns === 1 ? 'text-base' : 'text-xs'
                                            ]"
                                        >
                                            Date
                                        </span>
                                    </div>
                                    <div
                                        :class="[
                                            'font-bold text-gray-900',
                                            gridColumns === 1 ? 'text-2xl' :
                                            gridColumns === 2 ? 'text-lg' :
                                            'text-sm'
                                        ]"
                                    >
                                        {{ formatDate(ticket.show.start).month }} {{ formatDate(ticket.show.start).day }}, {{ formatDate(ticket.show.start).year }}
                                    </div>
                                </div>

                                <div class="text-center">
                                    <div class="flex items-center justify-center mb-1">
                                        <Clock :class="gridColumns === 1 ? 'h-6 w-6 mr-2' : 'h-4 w-4 mr-1'" class="text-purple-600" />
                                        <span
                                            :class="[
                                                'uppercase tracking-wide text-gray-600 font-semibold',
                                                gridColumns === 1 ? 'text-base' : 'text-xs'
                                            ]"
                                        >
                                            Time
                                        </span>
                                    </div>
                                    <div
                                        :class="[
                                            'font-bold text-gray-900',
                                            gridColumns === 1 ? 'text-2xl' :
                                            gridColumns === 2 ? 'text-lg' :
                                            'text-sm'
                                        ]"
                                    >
                                        {{ formatDate(ticket.show.start).time }}
                                    </div>
                                </div>
                            </div>

                            <!-- Seat Info -->
                            <div
                                :class="[
                                    'bg-gray-50 rounded-lg border border-gray-200',
                                    gridColumns === 1 ? 'p-6 mb-8' :
                                    gridColumns === 2 ? 'p-3 mb-4' :
                                    'p-2 mb-3'
                                ]"
                            >
                                <div
                                    :class="[
                                        'grid grid-cols-2 text-center',
                                        gridColumns === 1 ? 'gap-8' :
                                        gridColumns === 2 ? 'gap-4' :
                                        'gap-2'
                                    ]"
                                >
                                    <div>
                                        <div
                                            :class="[
                                                'uppercase tracking-wide text-gray-600 mb-1 font-semibold',
                                                gridColumns === 1 ? 'text-base mb-2' : 'text-xs'
                                            ]"
                                        >
                                            Row
                                        </div>
                                        <div
                                            :class="[
                                                'font-bold text-gray-900',
                                                gridColumns === 1 ? 'text-3xl' :
                                                gridColumns === 2 ? 'text-xl' :
                                                'text-lg'
                                            ]"
                                        >
                                            {{ ticket.seat.row.name }}
                                        </div>
                                    </div>
                                    <div>
                                        <div
                                            :class="[
                                                'uppercase tracking-wide text-gray-600 mb-1 font-semibold',
                                                gridColumns === 1 ? 'text-base mb-2' : 'text-xs'
                                            ]"
                                        >
                                            Seat
                                        </div>
                                        <div
                                            :class="[
                                                'font-bold text-gray-900',
                                                gridColumns === 1 ? 'text-3xl' :
                                                gridColumns === 2 ? 'text-xl' :
                                                'text-lg'
                                            ]"
                                        >
                                            {{ ticket.seat.seat_number }}
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Ticket Holder -->
                            <div
                                :class="[
                                    'text-center border-t border-gray-200',
                                    gridColumns === 1 ? 'pt-6' :
                                    gridColumns === 2 ? 'pt-3' :
                                    'pt-2'
                                ]"
                            >
                                <div class="flex items-center justify-center mb-1">
                                    <User :class="gridColumns === 1 ? 'h-6 w-6 mr-2' : 'h-4 w-4 mr-1'" class="text-purple-600" />
                                    <span
                                        :class="[
                                            'uppercase tracking-wide text-gray-600 font-semibold',
                                            gridColumns === 1 ? 'text-base' : 'text-xs'
                                        ]"
                                    >
                                        Ticket Holder
                                    </span>
                                </div>
                                <div
                                    :class="[
                                        'font-bold text-gray-900',
                                        gridColumns === 1 ? 'text-2xl' :
                                        gridColumns === 2 ? 'text-lg' :
                                        'text-base'
                                    ]"
                                >
                                    {{ ticket.name }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<style scoped>
/* Force light theme */
.light-theme-wrapper {
    color-scheme: light;
    background: white;
    color: black;
}

.light-theme-wrapper * {
    color-scheme: light;
}

.print-container {
    background: white;
    color: black;
    padding: 0;
    margin: 0;
}

.tickets-wrapper {
    display: grid;
    padding: 1rem;
    max-width: 100%;
    place-items: center;
}

.ticket-item {
    width: 100%;
    max-width: 100%;
}

.ticket-card {
    width: 100%;
    height: auto;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
}

.ticket-code-header {
    /* No border-radius here - let the parent handle it */
}

.ticket-content {
    background: white;
}

/* Screen view */
@media screen {
    .print-container {
        min-height: 100vh;
        padding: 2rem;
    }

    .tickets-wrapper {
        max-width: 1200px;
        margin: 0 auto;
    }
}

/* Print-specific styles */
@media print {
    @page {
        margin: 0.3in;
        size: A4;
        @top-left-corner { content: ""; }
        @top-left { content: ""; }
        @top-center { content: ""; }
        @top-right { content: ""; }
        @top-right-corner { content: ""; }
        @bottom-left-corner { content: ""; }
        @bottom-left { content: ""; }
        @bottom-center { content: ""; }
        @bottom-right { content: ""; }
        @bottom-right-corner { content: ""; }
    }

    * {
        color-scheme: light !important;
        color-adjust: exact !important;
        print-color-adjust: exact !important;
        -webkit-print-color-adjust: exact !important;
    }

    html, body {
        background: white !important;
        color: black !important;
        margin: 0 !important;
        padding: 0 !important;
    }

    .light-theme-wrapper {
        background: white !important;
        color: black !important;
    }

    .print-container {
        background: white !important;
        color: black !important;
        margin: 0 !important;
        padding: 0 !important;
        width: 100% !important;
        max-width: none !important;
    }

    .no-print {
        display: none !important;
    }

    .tickets-wrapper {
        display: grid !important;
        padding: 0.5rem !important;
        margin: 0 !important;
        width: 100% !important;
        max-width: none !important;
        place-items: stretch !important;
    }

    .ticket-item {
        width: 100% !important;
        max-width: none !important;
        margin: 0 !important;
        padding: 0 !important;
    }

    .ticket-card {
        background: white !important;
        border: 2px solid #374151 !important;
        width: 100% !important;
        max-width: none !important;
        height: auto !important;
        page-break-inside: avoid !important;
        break-inside: avoid !important;
        margin: 0 !important;
        box-shadow: none !important;
    }

    /* Page breaks for 4+ tickets */
    .ticket-item:nth-child(5),
    .ticket-item:nth-child(9),
    .ticket-item:nth-child(13) {
        page-break-before: always !important;
        break-before: page !important;
    }

    .ticket-code-header {
        background: #7c3aed !important;
        color: white !important;
        -webkit-print-color-adjust: exact !important;
        print-color-adjust: exact !important;
    }

    .ticket-content {
        background: white !important;
    }

    /* Color overrides for print */
    .bg-white { background: white !important; }
    .bg-gray-50 { background: #f9fafb !important; }
    .bg-purple-600 { background: #7c3aed !important; }

    .text-purple-600 { color: #7c3aed !important; }
    .text-gray-900 { color: #111827 !important; }
    .text-gray-600 { color: #4b5563 !important; }
    .text-white { color: white !important; }

    .border-gray-200 { border-color: #e5e7eb !important; }
    .border-gray-400 { border-color: #9ca3af !important; }
}

/* Mobile responsive */
@media (max-width: 768px) and not print {
    .tickets-wrapper {
        grid-template-columns: 1fr !important;
        gap: 1rem !important;
    }
}
</style>
