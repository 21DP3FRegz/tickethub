<script setup lang="ts">
import { computed } from 'vue';

const props = defineProps<{
    seat: {
        id: number;
        seat_number: string;
        row_id: number;
        reservation: null | { id: number; reserved_until?: string };
        ticket: null | { id: number };
        status?: string; // Add optional status property
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
        return 'bg-primary/70 ring-2 ring-primary ring-offset-1 text-primary-foreground';
    }

    switch (seatStatus.value) {
        case 'booked':
            return 'bg-muted-foreground/30 cursor-not-allowed';
        case 'reserved':
            return 'bg-yellow-400/70 cursor-not-allowed text-black';
        case 'available':
            return 'bg-primary hover:bg-primary/90 text-primary-foreground';
        default:
            return '';
    }
});
</script>

<template>
    <div
        class="w-7 h-7 flex items-center justify-center rounded text-xs"
        :class="statusClasses"
        :title="seatStatus"
    >
        <slot>{{ seat.seat_number }}</slot>
    </div>
</template>
