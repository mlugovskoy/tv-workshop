<script setup lang="ts">
interface Column {
    key: string;
    label: string;
}

interface Props {
    columns: Column[];
    rows: Record<string, unknown>[];
    sortBy?: string | null;
    sortDir?: 'asc' | 'desc';
}

const props = defineProps<Props>();
const emit = defineEmits<{ (e: 'sort', key: string): void }>();
</script>

<template>
    <div class="mt-6 overflow-hidden rounded-lg border border-gray-200 bg-white">
        <table class="w-full table-fixed text-left text-sm">
            <thead class="border-b border-gray-200 bg-gray-50">
            <tr>
                <th class="px-6 py-3 font-medium text-gray-600"
                    :class="{'w-24': column.key === 'id'}"
                    v-for="column in props.columns"
                    :key="column.key">
                    <button v-if="column.sortable"
                            type="button"
                            class="flex items-center gap-2 cursor-pointer hover:text-gray-900"
                            @click="emit('sort', column.key)">
                        {{ column.label }}
                        <svg class="h-4 w-4 shrink-0"
                             :class="sortBy === column.key ? 'text-gray-900' : 'text-gray-300'"
                             viewBox="0 0 16 16" fill="none" stroke="currentColor"
                             stroke-width="2" stroke-linecap="round">
                            <template v-if="sortBy === column.key && sortDir === 'asc'">
                                <line x1="2" y1="4" x2="6" y2="4"/>
                                <line x1="2" y1="8" x2="10" y2="8"/>
                                <line x1="2" y1="12" x2="14" y2="12"/>
                            </template>
                            <template v-else-if="sortBy === column.key && sortDir === 'desc'">
                                <line x1="2" y1="4" x2="14" y2="4"/>
                                <line x1="2" y1="8" x2="10" y2="8"/>
                                <line x1="2" y1="12" x2="6" y2="12"/>
                            </template>
                            <template v-else>
                                <line x1="2" y1="4" x2="14" y2="4"/>
                                <line x1="2" y1="8" x2="14" y2="8"/>
                                <line x1="2" y1="12" x2="14" y2="12"/>
                            </template>
                        </svg>
                    </button>
                    <template v-else>{{ column.label }}</template>
                </th>

                <th class="px-6 py-3 w-30 font-medium text-gray-600">
                    Действия
                </th>
            </tr>
            </thead>

            <tbody class="divide-y divide-gray-100">
            <tr v-for="(row, index) in props.rows"
                :key="index">
                <td class="px-6 py-4 font-medium text-gray-900"
                    v-for="column in props.columns"
                    :key="column.key">
                    <slot :name="column.key" :row="row">
                        {{ row[column.key] }}
                    </slot>
                </td>

                <td class="px-6 py-4 flex items-center gap-2">
                    <slot name="actions" :row="row"/>
                </td>
            </tr>
            </tbody>
        </table>
    </div>
</template>
