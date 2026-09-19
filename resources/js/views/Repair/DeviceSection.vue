<script setup lang="ts">
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
    mode: "new" | "existing";
    clientMode: "new" | "existing";
    devices: Device[];
    selectedClient: Client | null;
    selectedDevice: Device | null;
    isLoading: boolean;
}>();

const emit = defineEmits<{
    "update:mode": [mode: "new" | "existing"];
    select: [device: Device];
}>();

const changeMode = (mode: "new" | "existing") => {
    emit("update:mode", mode);
};

const selectDevice = (device: Device) => {
    emit("select", device);
};
</script>

<template>
    <div
        v-if="props.clientMode === 'existing' && props.selectedClient"
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
                    :checked="props.mode === 'existing'"
                    @change="changeMode('existing')"
                >

                <span class="text-sm text-gray-700">
                            Существующий телевизор
                        </span>
            </label>

            <label class="flex cursor-pointer items-center gap-2">
                <input
                    type="radio"
                    value="new"
                    :checked="props.mode === 'new'"
                    @change="changeMode('new')"
                >

                <span class="text-sm text-gray-700">
                            Новый телевизор
                        </span>
            </label>
        </div>

        <div v-if="props.mode === 'existing'" class="mt-4">
            <div v-if="props.isLoading" class="text-sm text-gray-500">
                Загрузка...
            </div>
            <div v-else-if="props.devices.length > 0" class="space-y-2">
                <button
                    v-for="device in props.devices"
                    :key="device.id"
                    type="button"
                    class="block w-full cursor-pointer rounded-md border border-gray-200 px-4 py-3 text-left hover:bg-gray-50"
                    :class="{
                                'border-blue-500 bg-blue-50':
                                    props.selectedDevice?.id === device.id
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
            v-if="props.mode === 'new'"
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
                    v-if="props.errors.brand"
                    class="mt-1 text-sm text-red-600"
                >
                    {{ props.errors.brand[0] }}
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
                    v-if="props.errors.model"
                    class="mt-1 text-sm text-red-600"
                >
                    {{ props.errors.model[0] }}
                </p>
            </div>
        </div>
    </div>

    <div class="border-t border-gray-200 pt-6" v-if="props.clientMode === 'new'">
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

                <p v-if="props.errors.brand" class="mt-1 text-sm text-red-600">
                    {{ props.errors.brand[0] }}
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

                <p v-if="props.errors.model" class="mt-1 text-sm text-red-600">
                    {{ props.errors.model[0] }}
                </p>
            </div>
        </div>
    </div>
</template>
