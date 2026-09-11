<script setup lang="ts">
const stats = [
    {
        title: 'Новые',
        value: 5,
    },
    {
        title: 'В ремонте',
        value: 8,
    },
    {
        title: 'Ждут запчасть',
        value: 3,
    },
    {
        title: 'Готовы',
        value: 4,
    },
]
</script>

<template>
    <div class="mt-6 grid grid-cols-4 gap-4">
        <div class="rounded-lg border border-slate-200 bg-white p-5"
             v-for="stat in stats"
             :key="stat.title">
                <span class="text-sm text-slate-600">
                    {{ stat.title }}
                </span>

            <strong class="mt-2 block text-3xl font-semibold">
                {{ stat.value }}
            </strong>
        </div>
    </div>
</template>

<style scoped>

</style>
