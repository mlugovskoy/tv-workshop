<script setup lang="ts">
import {useRouter} from "vue-router";
import {repairStatusLabels} from "../utils/repairStatusLabels";
import {formatDate} from "../utils/formatDate";
import DefaultTable from "./DefaultTable.vue";

interface RecentRepair {
    id: number;
    client: {
        id: number;
        name: string;
    };
    device: {
        id: number;
        brand: string;
        model: string;
    };
    status: string;
    created_at: string;
}

defineProps<{
    repairs: RecentRepair[];
}>();

const router = useRouter();

const columnsTable = [
    {key: 'id', label: 'ID'},
    {key: 'device', label: 'Устройство'},
    {key: 'client', label: 'Клиент'},
    {key: 'status', label: 'Статус'},
    {key: 'created_at', label: 'Дата'}
];

const getStatusLabel = (status: string): string => {
    return repairStatusLabels[status] ?? status;
};

const showRepair = (id: number) => {
    router.push({
        name: 'repairs.show',
        params: {id},
    });
};
</script>

<template>
    <section class="mt-8">
        <h2 class="text-lg font-semibold text-slate-600">
            Последние ремонты
        </h2>

        <DefaultTable :columns="columnsTable" :rows="repairs" v-if="repairs.length > 0">
            <template #device="{ row }">
                {{ row.device.brand }} {{ row.device.model }}
            </template>

            <template #client="{ row }">
                {{ row.client.name }}
            </template>

            <template #status="{ row }">
                {{ getStatusLabel(row.status) }}
            </template>

            <template #created_at="{ row }">
                {{ formatDate(row.created_at) }}
            </template>

            <template #actions="{ row }">
                <button
                    type="button"
                    class="cursor-pointer text-sm text-blue-600 hover:text-blue-800"
                    @click="showRepair(row.id)"
                >
                    Открыть
                </button>
            </template>
        </DefaultTable>

        <p class="mt-2 text-slate-600" v-else>
            Ремонтов пока нет.
        </p>
    </section>
</template>
