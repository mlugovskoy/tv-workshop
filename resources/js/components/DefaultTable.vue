<script setup lang="ts">
interface Column {
    key: string;
    label: string;
}

interface Props {
    columns: Column[];
    rows: Record<string, unknown>[];
}

defineProps<Props>();
</script>

<template>
    <div class="mt-6 overflow-hidden rounded-lg border border-gray-200 bg-white">
        <table class="w-full text-left text-sm">
            <thead class="border-b border-gray-200 bg-gray-50">
            <tr>
                <th class="px-6 py-3 font-medium text-gray-600"
                    v-for="column in columns"
                    :key="column.key">
                    {{ column.label }}
                </th>

                <th class="px-6 py-3 font-medium text-gray-600">
                    Действия
                </th>
            </tr>
            </thead>

            <tbody class="divide-y divide-gray-100">
            <tr v-for="(row, index) in rows"
                :key="index">
                <td class="px-6 py-4 font-medium text-gray-900"
                    v-for="column in columns"
                    :key="column.key">
                    {{ row[column.key] }}
                </td>

                <td class="px-6 py-4 flex items-center gap-2">
                    <slot name="actions" :row="row"/>
                </td>
            </tr>
            </tbody>
        </table>
    </div>
</template>
