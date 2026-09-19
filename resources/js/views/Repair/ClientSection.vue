<script setup lang="ts">
import DefaultButton from "../../components/DefaultButton.vue";

interface Client {
    id: number;
    name: string;
    phone: string | null;
}

interface CreateRepairForm {
    client_id: number | null;
    client_name: string;
    phone: string;
    device_id: number | null;
    brand: string;
    model: string;
    problem_description: string;
    estimated_price: number | null;
}

interface ValidationErrors {
    client_id?: string[];
    client_name?: string[];
    phone?: string[];
    device_id?: string[];
    brand?: string[];
    model?: string[];
    problem_description?: string[];
    estimated_price?: string[];
}

const form = defineModel<CreateRepairForm>('form', {
    required: true
})

const props = defineProps<{
    errors: ValidationErrors;
    mode: 'new' | 'existing';
    clients: Client[];
    selectedClient: Client | null;
    search: string;
    isSearching: boolean;
}>();

const emit = defineEmits<{
    "update:mode": [mode: 'new' | 'existing'];
    "update:search": [search: string];
    search: [];
    select: [client: Client];
    reset: [];
}>();

const changeMode = (mode: "new" | "existing") => {
    emit("update:mode", mode);
};

const updateSearch = (event: Event) => {
    const target = event.target as HTMLInputElement;

    emit("update:search", target.value);
};

const searchClients = () => {
    emit("search");
};

const selectClient = (client: Client) => {
    emit("select", client);
};

const resetSelection = () => {
    emit("reset");
};
</script>

<template>
    <div>
        <h2 class="text-lg font-medium text-gray-900">
            Клиент
        </h2>

        <div class="mt-4 flex gap-6">
            <label class="flex cursor-pointer items-center gap-2">
                <input
                    :checked="props.mode === 'new'"
                    @change="changeMode('new')"
                    type="radio"
                    value="new"
                >
                <span class="text-sm text-gray-700">
                            Новый клиент
                        </span>
            </label>

            <label class="flex cursor-pointer items-center gap-2">
                <input
                    :checked="props.mode === 'existing'"
                    @change="changeMode('existing')"
                    type="radio"
                    value="existing"
                >
                <span class="text-sm text-gray-700">
                    Существующий клиент
                </span>
            </label>
        </div>

        <div
            v-if="props.mode === 'new'"
            class="mt-4 grid grid-cols-2 gap-4">
            <div>
                <label
                    for="client_name"
                    class="block text-sm font-medium text-gray-700"
                >
                    Имя
                </label>

                <input
                    id="client_name"
                    v-model="form.client_name"
                    type="text"
                    class="mt-2 block w-full rounded-md border border-gray-300 px-3 py-2 text-sm"
                    placeholder="Иван Иванов"
                />

                <p v-if="props.errors.client_name" class="mt-1 text-sm text-red-600">
                    {{ props.errors.client_name[0] }}
                </p>
            </div>

            <div>
                <label
                    for="phone"
                    class="block text-sm font-medium text-gray-700"
                >
                    Телефон
                </label>

                <input
                    id="phone"
                    v-model="form.phone"
                    type="text"
                    class="mt-2 block w-full rounded-md border border-gray-300 px-3 py-2 text-sm"
                    placeholder="+7 999 123-45-67"
                />

                <p v-if="props.errors.phone" class="mt-1 text-sm text-red-600">
                    {{ props.errors.phone[0] }}
                </p>
            </div>
        </div>

        <div v-else class="mt-4">
            <div class="flex gap-2">
                <input
                    :value="props.search"
                    type="text"
                    class="block w-full rounded-md border border-gray-300 px-3 py-2 text-sm"
                    placeholder="Имя или номер телефона"
                    @input="updateSearch"
                    @keyup.enter="searchClients"
                >

                <DefaultButton
                    text="Найти"
                    type="button"
                    @click="searchClients"
                />
            </div>

            <div
                v-if="props.isSearching"
                class="mt-2 text-sm text-gray-500"
            >
                Поиск...
            </div>

            <div
                v-if="props.clients.length > 0"
                class="mt-2 overflow-hidden rounded-md border border-gray-200"
            >
                <button
                    v-for="client in props.clients"
                    :key="client.id"
                    type="button"
                    class="block w-full cursor-pointer border-b border-gray-100 px-4 py-3 text-left last:border-b-0 hover:bg-gray-50"
                    @click="selectClient(client)"
                >
                    <span class="text-sm block font-medium text-gray-900">
                        {{ client.name }}
                    </span>

                    <span class="mt-1 block text-xs text-gray-500">
                        {{ client.phone || 'Телефон не указан' }}
                    </span>
                </button>
            </div>

            <div
                v-if="props.selectedClient"
                class="mt-3 flex items-center justify-between rounded-md bg-gray-50 px-4 py-3"
            >
                <div>
                    <div class="text-sm font-medium text-gray-900">
                        {{ props.selectedClient.name }}
                    </div>

                    <div class="mt-1 text-xs text-gray-500">
                        {{ props.selectedClient.phone || 'Телефон не указан' }}
                    </div>
                </div>

                <button
                    type="button"
                    class="cursor-pointer text-sm text-blue-600 hover:text-blue-800"
                    @click="resetSelection"
                >
                    Изменить
                </button>
            </div>
        </div>
    </div>
</template>
