<script setup lang="ts">
import {useRoute, useRouter} from 'vue-router';
import {onMounted, reactive, ref} from "vue";
import PageTitle from "../../components/PageTitle.vue";
import DefaultButton from "../../components/DefaultButton.vue";
import {useNotification} from "../../composables/useNotification";

interface Repair {
    id: number;
    client: {
        id: number;
        name: string;
        phone: string | null;
    };
    device: {
        id: number;
        brand: string;
        model: string;
        serial_number: string | null;
    };
    status: string;
    problem_description: string;
    diagnosis: string | null;
    repair_description: string | null;
    estimated_price: number | null;
    final_price: number | null;
    received_at: string;
    completed_at: string | null;
    issued_at: string | null;
}

interface EditRepairForm {
    problem_description: string;
    diagnosis: string;
    repair_description: string;
    estimated_price: number | null;
    final_price: number | null;
}

interface ValidationErrors {
    problem_description?: string[];
    diagnosis?: string[];
    repair_description?: string[];
    estimated_price?: string[];
    final_price?: string[];
}

const route = useRoute();
const router = useRouter();

const {showSuccess, showError} = useNotification();

const repair = ref<Repair | null>(null);

const form = reactive<EditRepairForm>({
    problem_description: '',
    diagnosis: '',
    repair_description: '',
    estimated_price: null,
    final_price: null,
});

const errors = reactive<ValidationErrors>({});

const isLoading = ref(false);
const isSubmitting = ref(false);

const clearErrors = () => {
    errors.problem_description = undefined;
    errors.diagnosis = undefined;
    errors.repair_description = undefined;
    errors.estimated_price = undefined;
    errors.final_price = undefined;
};

const loadRepair = async () => {
    isLoading.value = true;

    try {
        const repairResponse = await fetch(`/api/repairs/${route.params.id}`);

        if (!repairResponse.ok) {
            showError('Не удалось загрузить ремонт');
            return;
        }

        const response = await repairResponse.json();

        repair.value = response.data;

        form.problem_description = response.data.problem_description;
        form.diagnosis = response.data.diagnosis ?? '';
        form.repair_description = response.data.repair_description ?? '';
        form.estimated_price = response.data.estimated_price;
        form.final_price = response.data.final_price;

    } catch (error) {
        showError('Не удалось связаться с сервером');
    } finally {
        isLoading.value = false;
    }
}

const submit = async () => {
    isSubmitting.value = true;
    clearErrors();

    try {
        const response = await fetch(
            `/api/repairs/${route.params.id}`,
            {
                method: 'PUT',
                headers: {
                    'Content-Type': 'application/json;charset=utf-8',
                },
                body: JSON.stringify(form),
            }
        );

        if (!response.ok) {
            const errorResponse = await response.json();

            for (const [key, value] of Object.entries(errorResponse.errors)) {
                errors[key as keyof ValidationErrors] = value;
            }
            return;
        }

        showSuccess('Ремонт успешно изменен');
        router.push('/repairs');
    } catch (error) {
        showError('Не удалось связаться с сервером');
    } finally {
        isSubmitting.value = false;
    }
};

onMounted(loadRepair);
</script>

<template>
    <div class="flex items-center justify-between">
        <PageTitle
            title="Редактирование ремонта"
            subtitle="Редактирование существующего в базе ремонта"
        />
    </div>

    <div
        v-if="isLoading"
        class="mt-6 rounded-lg border border-gray-200 bg-white p-6"
    >
        <p class="text-sm text-gray-500">
            Загрузка...
        </p>
    </div>

    <div
        v-else-if="repair"
        class="mt-6 rounded-lg border border-gray-200 bg-white p-6"
    >
        <form
            class="space-y-6"
            @submit.prevent="submit"
        >
            <!-- Клиент -->
            <div>
                <h2 class="text-base font-semibold text-gray-900">
                    Клиент
                </h2>

                <div class="mt-3 grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700">
                            Имя
                        </label>

                        <div
                            class="mt-2 rounded-md border border-gray-200 bg-gray-50 px-3 py-2 text-sm text-gray-700"
                        >
                            {{ repair.client.name }}
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700">
                            Телефон
                        </label>

                        <div
                            class="mt-2 rounded-md border border-gray-200 bg-gray-50 px-3 py-2 text-sm text-gray-700"
                        >
                            {{ repair.client.phone ?? '—' }}
                        </div>
                    </div>
                </div>
            </div>

            <!-- Телевизор -->
            <div>
                <h2 class="text-base font-semibold text-gray-900">
                    Телевизор
                </h2>

                <div class="mt-3 grid grid-cols-3 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700">
                            Бренд
                        </label>

                        <div
                            class="mt-2 rounded-md border border-gray-200 bg-gray-50 px-3 py-2 text-sm text-gray-700"
                        >
                            {{ repair.device.brand }}
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700">
                            Модель
                        </label>

                        <div
                            class="mt-2 rounded-md border border-gray-200 bg-gray-50 px-3 py-2 text-sm text-gray-700"
                        >
                            {{ repair.device.model }}
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700">
                            Серийный номер
                        </label>

                        <div
                            class="mt-2 rounded-md border border-gray-200 bg-gray-50 px-3 py-2 text-sm text-gray-700"
                        >
                            {{ repair.device.serial_number ?? '—' }}
                        </div>
                    </div>
                </div>
            </div>

            <!-- Информация о ремонте -->
            <div>
                <h2 class="text-base font-semibold text-gray-900">
                    Информация о ремонте
                </h2>

                <div class="mt-3 space-y-4">
                    <div>
                        <label
                            for="problem_description"
                            class="block text-sm font-medium text-gray-700"
                        >
                            Описание проблемы
                        </label>

                        <textarea
                            id="problem_description"
                            v-model="form.problem_description"
                            rows="4"
                            class="mt-2 block w-full rounded-md border border-gray-300 px-3 py-2 text-sm"
                        />

                        <p
                            v-if="errors.problem_description"
                            class="mt-1 text-sm text-red-500"
                        >
                            {{ errors.problem_description[0] }}
                        </p>
                    </div>

                    <div>
                        <label
                            for="diagnosis"
                            class="block text-sm font-medium text-gray-700"
                        >
                            Диагностика
                        </label>

                        <textarea
                            id="diagnosis"
                            v-model="form.diagnosis"
                            rows="4"
                            class="mt-2 block w-full rounded-md border border-gray-300 px-3 py-2 text-sm"
                            placeholder="Например, неисправен блок питания"
                        />

                        <p
                            v-if="errors.diagnosis"
                            class="mt-1 text-sm text-red-500"
                        >
                            {{ errors.diagnosis[0] }}
                        </p>
                    </div>

                    <div>
                        <label
                            for="repair_description"
                            class="block text-sm font-medium text-gray-700"
                        >
                            Описание ремонта
                        </label>

                        <textarea
                            id="repair_description"
                            v-model="form.repair_description"
                            rows="4"
                            class="mt-2 block w-full rounded-md border border-gray-300 px-3 py-2 text-sm"
                            placeholder="Например, заменён блок питания"
                        />

                        <p
                            v-if="errors.repair_description"
                            class="mt-1 text-sm text-red-500"
                        >
                            {{ errors.repair_description[0] }}
                        </p>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
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
                                step="0.01"
                                class="mt-2 block w-full rounded-md border border-gray-300 px-3 py-2 text-sm"
                                placeholder="5000"
                            />

                            <p
                                v-if="errors.estimated_price"
                                class="mt-1 text-sm text-red-500"
                            >
                                {{ errors.estimated_price[0] }}
                            </p>
                        </div>

                        <div>
                            <label
                                for="final_price"
                                class="block text-sm font-medium text-gray-700"
                            >
                                Итоговая цена
                            </label>

                            <input
                                id="final_price"
                                v-model.number="form.final_price"
                                type="number"
                                min="0"
                                step="0.01"
                                class="mt-2 block w-full rounded-md border border-gray-300 px-3 py-2 text-sm"
                                placeholder="5000"
                            />

                            <p
                                v-if="errors.final_price"
                                class="mt-1 text-sm text-red-500"
                            >
                                {{ errors.final_price[0] }}
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <div
                class="flex justify-end gap-3 border-t border-gray-200 pt-6"
            >
                <RouterLink
                    to="/repairs"
                    class="rounded-md border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50"
                >
                    Отмена
                </RouterLink>

                <DefaultButton text="Сохранить" :isDisabled="isSubmitting"/>
            </div>
        </form>
    </div>
</template>
