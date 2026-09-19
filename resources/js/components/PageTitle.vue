<script setup lang="ts">
withDefaults(defineProps<{
    title: string,
    subtitle?: string,
    tag?: 'h1' | 'h2' | 'h3' | 'h4' | 'h5' | 'h6'
}>(), {
    tag: 'h1'
})
</script>

<template>
    <div>
        <component :is="tag" class="text-2xl font-semibold text-slate-600">{{ title }}</component>
        <p v-if="subtitle" class="mt-1 text-sm text-slate-600">{{ subtitle }}</p>
    </div>
</template>
