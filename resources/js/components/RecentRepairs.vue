<script setup lang="ts">
const items = []
</script>

<template>
    <section class="mt-8">
        <h2 class="text-lg font-semibold text-slate-600">
            Последние ремонты
        </h2>

        <div v-if="items.length > 0">w</div>

        <p class="mt-2 text-slate-600" v-else>
            Здесь позже появится список последних ремонтов.
        </p>
    </section>
</template>

<style scoped>

</style>
