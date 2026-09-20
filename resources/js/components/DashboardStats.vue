<script setup lang="ts">
import {formatPrice} from "../utils/formatPrice";

interface DashboardStatistics {
    in_repair: number;
    ready: number;
    new_this_month: number;
    revenue_this_month: string;
}

defineProps<{
    statistics: DashboardStatistics;
}>();

const stats = [
    {
        key: 'new_this_month',
        title: 'Новые за месяц',
    },
    {
        key: 'in_repair',
        title: 'В ремонте',
    },
    {
        key: 'ready',
        title: 'Готовы',
    },
] as const;
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
                {{ statistics[stat.key] }}
            </strong>
        </div>

        <div class="rounded-lg border border-slate-200 bg-white p-5">
            <span class="text-sm text-slate-600">
                Выручка за месяц
            </span>

            <strong class="mt-2 block text-3xl font-semibold">
                {{ formatPrice(statistics.revenue_this_month) }}
            </strong>
        </div>
    </div>
</template>
