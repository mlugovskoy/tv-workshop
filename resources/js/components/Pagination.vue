<script setup lang="ts">
defineProps<{
    currentPage: number;
    lastPage: number;
}>();

const emit = defineEmits<{
    change: [page: number];
}>();

const goToPage = (page: number) => {
    emit('change', page);
};
</script>

<template>
    <div class="mt-4 flex items-center justify-between">
        <button
            type="button"
            class="cursor-pointer rounded-md border border-gray-300 px-3 py-2 text-sm disabled:cursor-not-allowed disabled:opacity-50"
            :disabled="currentPage === 1"
            @click="goToPage(currentPage - 1)"
        >
            Назад
        </button>

        <span class="text-sm text-gray-600">
        Страница {{ currentPage }} из {{ lastPage }}
    </span>

        <button
            type="button"
            class="cursor-pointer rounded-md border border-gray-300 px-3 py-2 text-sm disabled:cursor-not-allowed disabled:opacity-50"
            :disabled="currentPage === lastPage"
            @click="goToPage(currentPage + 1)"
        >
            Далее
        </button>
    </div>
</template>
