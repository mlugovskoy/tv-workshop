<script setup lang="ts">
defineProps<{
    text: string
    isDisabled?: boolean
}>()
</script>

<template>
    <button
        class="rounded-md cursor-pointer bg-blue-600 px-4 py-2 text-sm font-medium text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 disabled:opacity-40 disabled:cursor-default disabled:hover:bg-blue-600"
        :disabled="isDisabled"
    >
        {{ text }}
    </button>
</template>
