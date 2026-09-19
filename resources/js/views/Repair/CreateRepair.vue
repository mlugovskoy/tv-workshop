<script setup lang="ts">
import PageTitle from "../../components/PageTitle.vue";
import DefaultButton from "../../components/DefaultButton.vue";
import {reactive, ref} from "vue";
import {useNotification} from "../../composables/useNotification";
import {useRouter} from "vue-router";
import ClientSection from "./ClientSection.vue";
import DeviceSection from "./DeviceSection.vue";
import RepairSection from "./RepairSection.vue";

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
    estimated_price: string | null;
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
    estimated_price: ''
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
    form.client_name = "";
    form.phone = "";

    clients.value = [];
    clientSearch.value = "";

    selectedDevice.value = null;
    form.device_id = null;

    form.brand = "";
    form.model = "";

    await loadClientDevices(client.id);

    deviceMode.value = devices.value.length > 0 ? 'existing' : 'new';
}

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

    deviceMode.value = 'new';
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
            <ClientSection
                v-model:form="form"
                :errors="errors"
                :mode="clientMode"
                :clients="clients"
                :selected-client="selectedClient"
                :search="clientSearch"
                :is-searching="isSearchingClients"
                @update:mode="changeClientMode"
                @update:search="clientSearch = $event"
                @search="searchClients"
                @select="selectClient"
                @reset="resetClientSelection"
            />

            <DeviceSection
                v-model:form="form"
                :errors="errors"
                :mode="deviceMode"
                :client-mode="clientMode"
                :devices="devices"
                :selected-client="selectedClient"
                :selected-device="selectedDevice"
                :is-loading="isLoadingDevices"
                @update:mode="changeDeviceMode"
                @select="selectDevice"
            />

            <RepairSection
                v-model:form="form"
                :errors="errors"
            />

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
