<script setup lang="ts">
import PageTitle from "../components/PageTitle.vue";
import DefaultButton from "../components/DefaultButton.vue";
import {reactive, ref} from "vue";
import {useNotification} from "../composables/useNotification";
import {useRouter} from "vue-router";

interface Client {
    id: number;
    name: string;
    phone: string | null;
}

interface Device {
    id: number;
    brand: string;
    model: string;
    serial_number: string | null;
    client: {
        id: number;
        name: string;
    };
}

interface ClientResource {
    data: Client[];
}

interface DeviceResource {
    data: Device[];
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

const form = reactive<CreateRepairForm>({
    client_id: null,
    client_name: '',
    phone: '',
    device_id: null,
    brand: '',
    model: '',
    problem_description: '',
    estimated_price: null
});

const clientMode = ref<'new' | 'existing'>('new');
const deviceMode = ref<'new' | 'existing'>('new');

const clients = ref<Client[]>([]);
const selectedClient = ref<Client | null>(null);
const clientSearch = ref('');

const devices = ref<Device[]>([]);
const selectedDevice = ref<Device | null>(null);

const isSearchingClients = ref(false);
const isLoadingDevices = ref(false);
const isSubmitting = ref(false);

const errors = reactive<ValidationErrors>({});

const router = useRouter();
const {showSuccess, showError} = useNotification();

const clearErrors = () => {
    errors.client_id = undefined;
    errors.client_name = undefined;
    errors.phone = undefined;
    errors.device_id = undefined;
    errors.brand = undefined;
    errors.model = undefined;
    errors.problem_description = undefined;
    errors.estimated_price = undefined;
};

const submit = async () => {
    clearErrors();

    if (clientMode.value === 'existing' && !selectedClient.value) {
        showError('Выберите клиента');
        return;
    }

    if (
        clientMode.value === 'existing' &&
        deviceMode.value === 'existing' &&
        !selectedDevice.value
    ) {
        showError('Выберите телевизор');
        return;
    }

    isSubmitting.value = true;

    try {
        let payload;

        if (clientMode.value === 'new') {
            payload = {
                client_name: form.client_name,
                phone: form.phone,
                brand: form.brand,
                model: form.model,
                problem_description: form.problem_description,
                estimated_price: form.estimated_price,
            };
        } else if (deviceMode.value === 'existing') {
            payload = {
                client_id: form.client_id,
                device_id: form.device_id,
                problem_description: form.problem_description,
                estimated_price: form.estimated_price,
            };
        } else {
            payload = {
                client_id: form.client_id,
                brand: form.brand,
                model: form.model,
                problem_description: form.problem_description,
                estimated_price: form.estimated_price,
            };
        }

        const response = await fetch('/api/repairs', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json;charset=utf-8'
            },
            body: JSON.stringify(payload)
        });

        if (!response.ok) {
            const errorResponse = await response.json();

            if (response.status === 422 && errorResponse.errors) {
                for (const [key, value] of Object.entries(errorResponse.errors)) {
                    if (key in errors) {
                        errors[key as keyof ValidationErrors] = value as string[];
                    }
                }
            } else {
                showError(errorResponse.message ?? 'Не удалось создать ремонт');
            }

            return;
        }

        showSuccess('Ремонт успешно создан');

        await router.push('/repairs');
    } catch (error) {
        showError('Не удалось связаться с сервером');
    } finally {
        isSubmitting.value = false;
    }
};

const searchClients = async () => {
    if (!clientSearch.value.trim()) {
        clients.value = [];
        return;
    }

    isSearchingClients.value = true;

    try {
        const response = await fetch(
            `/api/clients?search=${encodeURIComponent(clientSearch.value)}`
        );

        if (!response.ok) {
            showError('Не удалось найти клиентов');
            return;
        }

        const data: ClientResource = await response.json();

        clients.value = data.data;
    } catch (error) {
        showError('Не удалось связаться с сервером');
    } finally {
        isSearchingClients.value = false;
    }
};

const loadClientDevices = async (clientId: number) => {
    isLoadingDevices.value = true;

    try {
        const response = await fetch(
            `/api/devices?client_id=${clientId}`
        );

        if (!response.ok) {
            showError('Не удалось загрузить телевизоры клиента');
            return;
        }

        const data: DeviceResource = await response.json();

        devices.value = data.data;
    } catch (error) {
        showError('Не удалось связаться с сервером');
    } finally {
        isLoadingDevices.value = false;
    }
};

const selectClient = async (client: Client) => {
    selectedClient.value = client;

    form.client_id = client.id;
    form.client_name = '';
    form.phone = '';

    clients.value = [];
    clientSearch.value = '';

    selectedDevice.value = null;
    form.device_id = null;

    form.brand = '';
    form.model = '';

    await loadClientDevices(client.id);

    deviceMode.value = devices.value.length > 0
        ? 'existing'
        : 'new';
};

const selectDevice = (device: Device) => {
    selectedDevice.value = device;

    form.device_id = device.id;
    form.brand = '';
    form.model = '';
};

const changeClientMode = (mode: 'new' | 'existing') => {
    clientMode.value = mode;

    selectedClient.value = null;
    selectedDevice.value = null;

    clients.value = [];
    devices.value = [];
    clientSearch.value = '';

    form.client_id = null;
    form.device_id = null;

    form.client_name = '';
    form.phone = '';
    form.brand = '';
    form.model = '';
};

const changeDeviceMode = (mode: 'new' | 'existing') => {
    deviceMode.value = mode;

    selectedDevice.value = null;
    form.device_id = null;

    form.brand = '';
    form.model = '';
};

const resetClientSelection = () => {
    selectedClient.value = null;
    selectedDevice.value = null;

    form.client_id = null;
    form.device_id = null;

    devices.value = [];

    deviceMode.value = 'new';

    form.brand = '';
    form.model = '';
};
</script>

<template>
    <div class="flex items-center justify-between">
        <PageTitle
            title="Новый ремонт"
            subtitle="Добавление ремонта в базу"
        />
    </div>

    <div class="mt-6 rounded-lg border border-gray-200 bg-white p-6">
        <form class="space-y-6" @submit.prevent="submit">
            <div>
                <h2 class="text-lg font-medium text-gray-900">
                    Клиент
                </h2>

                <div class="mt-4 flex gap-6">
                    <label class="flex cursor-pointer items-center gap-2">
                        <input
                            :checked="clientMode === 'new'"
                            @change="changeClientMode('new')"
                            type="radio"
                            value="new"
                        >
                        <span class="text-sm text-gray-700">
                            Новый клиент
                        </span>
                    </label>

                    <label class="flex cursor-pointer items-center gap-2">
                        <input
                            :checked="clientMode === 'existing'"
                            @change="changeClientMode('existing')"
                            type="radio"
                            value="existing"
                        >
                        <span class="text-sm text-gray-700">
                            Существующий клиент
                        </span>
                    </label>
                </div>

                <div
                    v-if="clientMode === 'new'"
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

                        <p v-if="errors.client_name" class="mt-1 text-sm text-red-600">
                            {{ errors.client_name[0] }}
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

                        <p v-if="errors.phone" class="mt-1 text-sm text-red-600">
                            {{ errors.phone[0] }}
                        </p>
                    </div>
                </div>

                <div v-else class="mt-4">
                    <div class="flex gap-2">
                        <input
                            v-model="clientSearch"
                            type="text"
                            class="block w-full rounded-md border border-gray-300 px-3 py-2 text-sm"
                            placeholder="Имя или номер телефона"
                            @keyup.enter="searchClients"
                        >

                        <DefaultButton
                            text="Найти"
                            type="button"
                            @click="searchClients"
                        />
                    </div>

                    <div
                        v-if="isSearchingClients"
                        class="mt-2 text-sm text-gray-500"
                    >
                        Поиск...
                    </div>

                    <div
                        v-if="clients.length > 0"
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

                    <div
                        v-if="selectedClient"
                        class="mt-3 flex items-center justify-between rounded-md bg-gray-50 px-4 py-3"
                    >
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

            <div
                v-if="clientMode === 'existing' && selectedClient"
                class="border-t border-gray-200 pt-6"
            >
                <label class="block text-sm font-medium text-gray-700">
                    Телевизор
                </label>

                <div class="mt-4 flex gap-6">
                    <label class="flex cursor-pointer items-center gap-2">
                        <input
                            type="radio"
                            value="existing"
                            :checked="deviceMode === 'existing'"
                            @change="changeDeviceMode('existing')"
                        >

                        <span class="text-sm text-gray-700">
                            Существующий телевизор
                        </span>
                    </label>

                    <label class="flex cursor-pointer items-center gap-2">
                        <input
                            type="radio"
                            value="new"
                            :checked="deviceMode === 'new'"
                            @change="changeDeviceMode('new')"
                        >

                        <span class="text-sm text-gray-700">
                            Новый телевизор
                        </span>
                    </label>
                </div>

                <div v-if="deviceMode === 'existing'" class="mt-4">
                    <div v-if="isLoadingDevices" class="text-sm text-gray-500">
                        Загрузка...
                    </div>
                    <div v-else-if="devices.length > 0" class="space-y-2">
                        <button
                            v-for="device in devices"
                            :key="device.id"
                            type="button"
                            class="block w-full cursor-pointer rounded-md border border-gray-200 px-4 py-3 text-left hover:bg-gray-50"
                            :class="{
                                'border-blue-500 bg-blue-50':
                                    selectedDevice?.id === device.id
                            }"
                            @click="selectDevice(device)">
                            <span class="text-sm block font-medium text-gray-900">
                                {{ device.brand }} {{ device.model }}
                            </span>

                            <span
                                v-if="device.serial_number"
                                class="mt-1 block text-xs text-gray-500"
                            >
                                S/N: {{ device.serial_number }}
                            </span>
                        </button>
                    </div>

                    <div
                        v-else
                        class="mt-2 text-sm text-gray-500"
                    >
                        У клиента пока нет зарегистрированных телевизоров.
                    </div>
                </div>

                <div
                    v-if="deviceMode === 'new'"
                    class="mt-4 grid grid-cols-2 gap-4"
                >
                    <div>
                        <label
                            for="existing_client_brand"
                            class="block text-sm font-medium text-gray-700"
                        >
                            Бренд
                        </label>

                        <input
                            id="existing_client_brand"
                            v-model="form.brand"
                            type="text"
                            class="mt-2 block w-full rounded-md border border-gray-300 px-3 py-2 text-sm"
                            placeholder="Samsung"
                        >

                        <p
                            v-if="errors.brand"
                            class="mt-1 text-sm text-red-600"
                        >
                            {{ errors.brand[0] }}
                        </p>
                    </div>

                    <div>
                        <label
                            for="existing_client_model"
                            class="block text-sm font-medium text-gray-700"
                        >
                            Модель
                        </label>

                        <input
                            id="existing_client_model"
                            v-model="form.model"
                            type="text"
                            class="mt-2 block w-full rounded-md border border-gray-300 px-3 py-2 text-sm"
                            placeholder="UE55TU8000"
                        >

                        <p
                            v-if="errors.model"
                            class="mt-1 text-sm text-red-600"
                        >
                            {{ errors.model[0] }}
                        </p>
                    </div>
                </div>
            </div>

            <div class="border-t border-gray-200 pt-6" v-if="clientMode === 'new'">
                <h2 class="text-lg font-medium text-gray-900">
                    Телевизор
                </h2>

                <div class="grid grid-cols-2 gap-4 mt-4">
                    <div>
                        <label
                            for="brand"
                            class="block text-sm font-medium text-gray-700"
                        >
                            Бренд
                        </label>

                        <input
                            id="brand"
                            v-model="form.brand"
                            type="text"
                            class="mt-2 block w-full rounded-md border border-gray-300 px-3 py-2 text-sm"
                            placeholder="Samsung"
                        />

                        <p v-if="errors.brand" class="mt-1 text-sm text-red-600">
                            {{ errors.brand[0] }}
                        </p>
                    </div>

                    <div>
                        <label
                            for="model"
                            class="block text-sm font-medium text-gray-700"
                        >
                            Модель
                        </label>

                        <input
                            id="model"
                            v-model="form.model"
                            type="text"
                            class="mt-2 block w-full rounded-md border border-gray-300 px-3 py-2 text-sm"
                            placeholder="Smart"
                        />

                        <p v-if="errors.model" class="mt-1 text-sm text-red-600">
                            {{ errors.model[0] }}
                        </p>
                    </div>
                </div>
            </div>

            <div class="border-t border-gray-200 pt-6">
                <h2 class="text-lg font-medium text-gray-900">
                    Ремонт
                </h2>

                <div class="mt-4 space-y-4">
                    <div class="max-w-sm">
                        <label
                            for="estimated_price"
                            class="block text-sm font-medium text-gray-700"
                        >
                            Предварительная цена
                        </label>

                        <input
                            id="estimated_price"
                            v-model.number="form.estimated_price"
                            type="number"
                            min="0"
                            class="mt-2 block w-full rounded-md border border-gray-300 px-3 py-2 text-sm"
                            placeholder="3000"
                        />

                        <p class="mt-1 text-xs text-gray-500">
                            Можно оставить пустым, если цена пока неизвестна
                        </p>

                        <p v-if="errors.estimated_price" class="mt-1 text-sm text-red-600">
                            {{ errors.estimated_price[0] }}
                        </p>
                    </div>
                    <div class="col-span-2">
                        <label
                            for="problem_description"
                            class="block text-sm font-medium text-gray-700"
                        >
                            Что случилось?
                        </label>

                        <textarea
                            id="problem_description"
                            v-model="form.problem_description"
                            rows="3"
                            placeholder="Например: не включается, мигает индикатор"
                            class="mt-2 block w-full resize-none rounded-md border border-gray-300 px-3 py-2 text-sm"
                        />

                        <p v-if="errors.problem_description" class="mt-1 text-sm text-red-600">
                            {{ errors.problem_description[0] }}
                        </p>
                    </div>
                </div>
            </div>

            <div class="flex justify-end gap-3 border-t border-gray-200 pt-6">
                <RouterLink
                    to="/repairs"
                    class="rounded-md border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50"
                >
                    Отмена
                </RouterLink>

                <DefaultButton text="Создать ремонт" :isDisabled="isSubmitting"/>
            </div>

        </form>
    </div>
</template>
