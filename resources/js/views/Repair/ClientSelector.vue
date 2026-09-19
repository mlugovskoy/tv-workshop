<script setup lang="ts">
import {ref} from "vue";

interface Client {
    id: number;
    name: string;
    phone: string | null;
}

interface ClientResource {
    data: Client[];
}

interface ValidationErrors {
    client_id?: string[];
    client_name?: string[];
    phone?: string[];
}

const props = defineProps<{
    errors: ValidationErrors;
}>();

const clientId = defineModel<number | null>('clientId', {required: true});
const clientName = defineModel<string>('clientName', {required: true});
const phone = defineModel<string>('phone', {required: true});
const mode = defineModel<'new' | 'existing'>('mode', {required: true});

const emit = defineEmits<{ selected: [client: Client]; reset: []; }>();

const clients = ref<Client[]>([]);
const selectedClient = ref<Client | null>(null);
const clientSearch = ref('');
const isSearchingClients = ref(false);

const searchClients = async () => {
    if (!clientSearch.value.trim()) {
        clients.value = [];
        return;
    }

    isSearchingClients.value = true;

    try {
        const response = await fetch(`/api/clients?search=${encodeURIComponent(clientSearch.value)}`);

        if (!response.ok) {
            throw new Error();
        }

        const data: ClientResource = await response.json();
        clients.value = data.data;
    } catch (error) {
        clients.value = [];
    } finally {
        isSearchingClients.value = false;
    }
};

const selectClient = (client: Client) => {
    selectedClient.value = client;
    clientId.value = client.id;

    clients.value = [];
    clientSearch.value = '';

    emit('selected', client);
};

const resetClientSelection = () => {
    selectedClient.value = null;
    clientId.value = null;

    clients.value = [];
    clientSearch.value = '';

    emit('reset');
};

const changeMode = (newMode: 'new' | 'existing') => {
    mode.value = newMode;

    clientName.value = '';
    phone.value = '';

    resetClientSelection();
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
                    type="radio"
                    value="new"
                    name="client_mode"
                    :checked="mode === 'new'"
                    @change="changeMode('new')"
                >
                <span class="text-sm text-gray-700">Новый клиент</span>
            </label>

            <label class="flex cursor-pointer items-center gap-2">
                <input
                    type="radio"
                    value="existing"
                    name="client_mode"
                    :checked="mode === 'existing'"
                    @change="changeMode('existing')"
                >
                <span class="text-sm text-gray-700">Существующий клиент</span>
            </label>
        </div>

        <div
            v-if="mode === 'new'"
            class="mt-4 space-y-4">
            <div>
                <label
                    for="client_name"
                    class="block text-sm font-medium text-gray-700">
                    Имя
                </label>

                <input
                    id="client_name"
                    v-model="clientName"
                    type="text"
                    class="mt-1 block w-full rounded-md border border-gray-300"
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
                    v-model="phone"
                    type="text"
                    class="mt-1 block w-full rounded-md border border-gray-300"
                    placeholder="+7 999 123-45-67"
                />

                <p v-if="props.errors.phone" class="mt-1 text-sm text-red-600">
                    {{ props.errors.phone[0] }}
                </p>
            </div>
        </div>

        <div v-if="mode === 'existing'"
             class="mt-4">
            <div v-if="!selectedClient">
                <div class="mt-1 flex gap-2">

                    <input
                        id="client_search"
                        v-model="clientSearch"
                        type="text"
                        class="block w-full rounded-md border border-gray-300 px-3 py-2 text-sm"
                        placeholder="Имя или номер телефона"
                        @keyup.enter="searchClients">

                    <button type="button"
                            class="cursor-pointer rounded-md border border-gray-300 px-4 py-2 text-sm hover:bg-gray-50"
                            :disabled="isSearchingClients" @click="searchClients">Найти
                    </button>
                </div>

                <p v-if="props.errors.client_id" class="mt-1 text-sm text-red-600">{{ props.errors.client_id[0] }}</p>

                <div v-if="isSearchingClients"
                     class="mt-2 text-sm text-gray-500">
                    Поиск...
                </div>

                <div
                    v-else-if="clients.length > 0"
                    class="mt-2 overflow-hidden rounded-md border border-gray-200"
                >
                    <button
                        v-for="client in clients"
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
            </div>

            <div
                v-else
                class="rounded-md border border-gray-200 bg-gray-50 p-4"
            >
                <div class="flex items-start justify-between gap-4">
                    <div>
                        <div class="text-sm font-medium text-gray-900">
                            {{ selectedClient.name }}
                        </div>

                        <div class="mt-1 text-xs text-gray-500">
                            {{ selectedClient.phone || 'Телефон не указан' }}
                        </div>
                    </div>

                    <button
                        type="button"
                        class="cursor-pointer text-sm text-blue-600 hover:text-blue-800"
                        @click="resetClientSelection"
                    >
                        Изменить
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>
