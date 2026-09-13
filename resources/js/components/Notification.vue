<script setup>
import {useNotification} from "../composables/useNotification.js";

const {
    message,
    visible,
    type
} = useNotification();
</script>

<template>
    <div
        v-if="visible"
        class="fixed right-6 bottom-6 z-50 rounded-lg px-4 py-3 text-sm text-white shadow-lg"
        :class="type === 'success' ? 'bg-green-600' : 'bg-red-600'"
    >
        {{ message }}
    </div>
</template>
