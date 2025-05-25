<script setup lang="ts">
import { computed } from 'vue';

const props = defineProps<{
    seat: {
        id: number;
        seat_number: string;
        row_id: number;
        reservation: null | { id: number };
        ticket: null | { id: number };
        status?: string;
    };
    isSelected: boolean;
}>();

const seatStatus = computed(() => {
    // Use explicit status if available
    if (props.seat.status) {
        return props.seat.status;
    }

    // Fallback to computed status
    if (props.seat.ticket) {
        return 'booked';
    }

    if (props.seat.reservation) {
        // Check if we have reserved_until information
        if (props.seat.reservation.reserved_until) {
            const reservedUntil = new Date(props.seat.reservation.reserved_until);
            if (reservedUntil >= new Date()) {
                return 'reserved';
            }
            // Expired reservation
            return 'available';
        }
        return 'reserved';
    }

    return 'available';
});

const statusClasses = computed(() => {
    if (props.isSelected) {
        return 'bg-primary/80 ring-2 ring-primary ring-offset-1 text-primary-foreground';
    }

    switch (seatStatus.value) {
        case 'booked':
            return 'bg-muted-foreground/30 cursor-not-allowed';
        case 'your-booking':
            return 'bg-accent text-accent-foreground cursor-not-allowed ring-1 ring-accent/50';
        case 'reserved':
            return 'bg-yellow-300 text-secondary-foreground cursor-not-allowed';
        case 'your-reservation':
            return 'bg-secondary ring-1 ring-secondary-foreground cursor-not-allowed text-secondary-foreground';
        case 'available':
            return 'bg-primary hover:bg-primary/90 text-primary-foreground transition-colors';
        default:
            return '';
    }
});

const statusTitle = computed(() => {
    switch (seatStatus.value) {
        case 'booked':
            return 'Booked by someone else';
        case 'your-booking':
            return 'Your booked seat';
        case 'reserved':
            return 'Reserved by someone else';
        case 'your-reservation':
            return 'Your reserved seat';
        case 'available':
            return 'Available';
        default:
            return seatStatus.value;
    }
});
</script>

<template>
    <div
        class="w-7 h-7 flex items-center justify-center rounded text-xs font-medium shadow-sm"
        :class="statusClasses"
        :title="statusTitle"
    >
        <slot>{{ seat.seat_number }}</slot>
    </div>
</template>
