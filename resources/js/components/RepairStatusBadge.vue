<script setup lang="ts">
import {repairStatusLabels, repairStatusStyles} from "../utils/repairStatusLabels";

defineProps<{
    status: string;
}>();
</script>

<template>
    <span
        class="inline-flex items-center rounded-full px-2.5 py-1 text-xs font-medium"
        :class="repairStatusStyles[status] ?? 'bg-gray-100 text-gray-700'"
    >
        {{ repairStatusLabels[status] ?? status }}
    </span>
</template>
