<script setup lang="ts">
import {onMounted, reactive, ref} from "vue";
import PageTitle from "../components/PageTitle.vue";
import DefaultButton from "../components/DefaultButton.vue";
import {useNotification} from "../composables/useNotification";
import {useRouter} from "vue-router";

interface Client {
    id: number;
    name: string;
}

interface ClientResource {
    data: Client[];
}

interface DeviceForm {
    client_id: number | null;
    brand: string;
    model: string;
    serial_number: string | null;
    comment: string | null;
}

interface ValidationErrors {
    client_id?: string[];
    brand?: string[];
    model?: string[];
    serial_number?: string[];
    comment?: string[];
}

const router = useRouter();
const clients = ref<Client[]>([]);
const isSubmitting = ref(false);
const errors = reactive<ValidationErrors>({});
const form = reactive<DeviceForm>({
    client_id: null,
    brand: "",
    model: "",
    serial_number: "",
    comment: ""
});
const {showSuccess, showError} = useNotification();

const submit = async () => {
    isSubmitting.value = true;
    errors.client_id = undefined;
    errors.brand = undefined;
    errors.model = undefined;
    errors.serial_number = undefined;
    errors.comment = undefined;

    try {
        const response = await fetch('/api/devices', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json;charset=utf-8'
            },
            body: JSON.stringify(form)
        });

        if (!response.ok) {
            const errorResponse = await response.json();

            for (const [key, value] of Object.entries(errorResponse.errors)) {
                errors[key as keyof ValidationErrors] = value;
            }

            return;
        }

        showSuccess('Устройство успешно создано')
        router.push('/devices');
    } catch (error) {
        showError('Не удалось связаться с сервером');
    } finally {
        isSubmitting.value = false;
    }
};

const loadClients = async () => {
    try {
        const clientResponse = await fetch(`/api/device/clients`);

        if (!clientResponse.ok) {
            showError('Не удалось загрузить клиентов');
            return;
        }

        const response: ClientResource = await clientResponse.json();

        clients.value = response.data;
    } catch (error) {
        showError('Не удалось связаться с сервером');
    }
}

onMounted(loadClients);
</script>

<template>
    <div class="flex items-center justify-between">
        <PageTitle
            title="Новое устройство"
            subtitle="Добавление устройства в базу"
        />
    </div>

    <div class="mt-6 rounded-lg border border-gray-200 bg-white p-6">
        <form class="space-y-6" @submit.prevent="submit">

            <div class="mt-4 grid grid-cols-2 gap-4">
                <div>
                    <label
                        for="client_id"
                        class="block text-sm font-medium text-gray-700"
                    >
                        Клиент
                    </label>

                    <select
                        id="client_id"
                        v-model="form.client_id"
                        class="mt-2 block w-full rounded-md border border-gray-300 px-3 py-2 text-sm"
                    >
                        <option :value="null">
                            Выберите клиента
                        </option>

                        <option
                            v-for="client in clients"
                            :key="client.id"
                            :value="client.id">
                            {{ client.name}}
                        </option>
                    </select>

                    <p v-if="errors.client_id" class="mt-1 text-sm text-red-500">
                        {{ errors.client_id[0] }}
                    </p>
                </div>

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

                    <p v-if="errors.brand" class="mt-1 text-sm text-red-500">
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

                    <p v-if="errors.model" class="mt-1 text-sm text-red-500">
                        {{ errors.model[0] }}
                    </p>
                </div>

                <div>
                    <label
                        for="serial_number"
                        class="block text-sm font-medium text-gray-700"
                    >
                        Серийный номер
                    </label>

                    <input
                        id="serial_number"
                        v-model="form.serial_number"
                        type="text"
                        class="mt-2 block w-full rounded-md border border-gray-300 px-3 py-2 text-sm"
                        placeholder="7W813St..."
                    />

                    <p v-if="errors.serial_number" class="mt-1 text-sm text-red-600">
                        {{ errors.serial_number[0] }}
                    </p>
                </div>

                <div class="col-span-2">
                    <label
                        for="comment"
                        class="block text-sm font-medium text-gray-700"
                    >
                        Комментарий
                    </label>

                    <input
                        id="comment"
                        v-model="form.comment"
                        type="text"
                        placeholder="Например, Отличное устройство"
                        class="mt-2 block w-full rounded-md border border-gray-300 px-3 py-2 text-sm"
                    />

                    <p v-if="errors.comment" class="mt-1 text-sm text-red-600">
                        {{ errors.comment[0] }}
                    </p>
                </div>
            </div>

            <div class="flex justify-end gap-3 border-t border-gray-200 pt-6">
                <RouterLink
                    to="/devices"
                    class="rounded-md border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50"
                >
                    Отмена
                </RouterLink>

                <DefaultButton text="Создать устройство" :isDisabled="isSubmitting"/>
            </div>

        </form>
    </div>
</template>
