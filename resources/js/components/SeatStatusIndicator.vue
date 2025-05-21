<script setup lang="ts">
import { computed } from 'vue';

const props = defineProps<{
    seat: {
        id: number;
        seat_number: string;
        row_id: number;
        reservation: null | { id: number };
        ticket: null | { id: number };
    };
    isSelected: boolean;
}>();

const seatStatus = computed(() => {
    if (props.seat.ticket) {
        return 'booked';
    }
    if (props.seat.reservation) {
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
        case 'reserved':
            return 'bg-muted-foreground/30 cursor-not-allowed';
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
