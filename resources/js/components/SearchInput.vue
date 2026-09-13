<script setup lang="ts">
import SearchIcon from "./icons/SearchIcon.vue";

const model = defineModel<string>({required: true});

const emit = defineEmits<{
    search: [];
}>();
</script>

<template>
    <div class="relative w-1/2 gap-2">
        <input id="search"
               type="text"
               v-model="model"
               class="block w-full rounded-md border border-gray-300 px-3 py-2 pr-10 text-sm"
               placeholder="Поиск..."
               @keyup.enter="emit('search')">
        <button
            type="button"
            class="absolute right-1 top-1/2 flex h-8 w-8 bg-blue-600 hover:bg-blue-700 -translate-y-1/2 cursor-pointer items-center justify-center rounded-md text-gray-500"
            @click="emit('search')"
        >
            <SearchIcon/>
        </button>
    </div>
</template>
