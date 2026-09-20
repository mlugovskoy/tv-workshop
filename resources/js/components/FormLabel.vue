<script setup lang="ts">
defineProps<{
    text: string;
    required?: boolean;
}>();
</script>

<template>
    <label class="block text-sm font-medium text-gray-700">
        {{ text }}
        <span v-if="required" class="text-red-500">*</span>
    </label>
</template>
