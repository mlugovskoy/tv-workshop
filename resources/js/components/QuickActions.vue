<script setup lang="ts">
import {useRouter} from "vue-router";
import AddIcon from "./icons/AddIcon.vue";
import CheckIcon from "./icons/CheckIcon.vue";
import {pluralizeRepairs} from "../utils/pluralizeRepairs";

interface Props {
    readyRepairs: number;
}

defineProps<Props>();

const router = useRouter();

const goToCreateRepair = () => {
    router.push({name: 'repairs.create', query: {from: 'dashboard'}});
};

const goToReadyRepairs = () => {
    router.push('/repairs?status=ready');
};
</script>

<template>
    <div class="mt-6 grid gap-4 md:grid-cols-2">
        <button
            type="button"
            class="cursor-pointer rounded-lg border border-gray-200 bg-white p-6 text-left transition hover:border-gray-300 hover:shadow-sm"
            @click="goToCreateRepair"
        >
            <span class="flex items-center gap-4">
                <span
                    class="flex h-10 w-10 shrink-0 items-center justify-center rounded-lg bg-blue-100 border border-blue-200 text-xl"
                >
                    <AddIcon class="fill-gray-700"/>
                </span>

                <span class="flex flex-col">
                    <span class="text-base font-semibold text-gray-700">
                        Принять в ремонт
                    </span>

                    <span class="mt-1 text-sm text-slate-600">
                        Создать новый ремонт
                    </span>
                </span>
            </span>
        </button>

        <button
            type="button"
            class="cursor-pointer rounded-lg border border-gray-200 bg-white p-6 text-left transition hover:border-gray-300 hover:shadow-sm"
            @click="goToReadyRepairs"
        >
            <span class="flex items-center gap-4">
                <span
                    class="flex h-10 w-10 shrink-0 items-center justify-center rounded-lg bg-green-100 border border-green-200 text-xl"
                >
                    <CheckIcon class="fill-gray-700"/>
                </span>

                <span class="flex flex-col">
                    <span class="text-base font-semibold text-gray-700">
                        Выдать телевизор
                    </span>

                    <span class="mt-1 text-sm text-slate-600">
                        {{ readyRepairs }} {{
                            pluralizeRepairs(readyRepairs, 'готовый ремонт', 'готовых ремонта', 'готовых ремонтов')
                        }}
                    </span>
                </span>
            </span>
        </button>
    </div>
</template>
