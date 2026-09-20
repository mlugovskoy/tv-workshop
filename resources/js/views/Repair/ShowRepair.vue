<script setup lang="ts">
import {useRoute, useRouter} from 'vue-router';
import {onMounted, ref} from "vue";
import PageTitle from "../../components/PageTitle.vue";
import {useNotification} from "../../composables/useNotification";
import {formatPrice} from "../../utils/formatPrice";
import {formatDate} from "../../utils/formatDate";
import {repairStatusLabels} from "../../utils/repairStatusLabels";

interface StatusHistory {
    id: number;
    status: string;
    created_at: string;
}

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
    status_history: StatusHistory[];
    problem_description: string;
    diagnosis: string | null;
    repair_description: string | null;
    estimated_price: string | null;
    final_price: string | null;
    received_at: string;
    completed_at: string | null;
    issued_at: string | null;
}

const route = useRoute();
const router = useRouter();

const {showSuccess, showError} = useNotification();

const repair = ref<Repair | null>(null);

const isLoading = ref(false);
const isDeleting = ref(false);
const isChangingStatus = ref(false);

const getStatusLabel = (status: string): string => {
    return repairStatusLabels[status] ?? status;
};

const changeStatus = async (status: string) => {
    if (!repair.value || status === repair.value.status) {
        return;
    }

    isChangingStatus.value = true;

    try {
        const statusResponse = await fetch(
            `/api/repairs/${repair.value.id}/status`,
            {
                method: 'PATCH',
                headers: {
                    'Content-Type': 'application/json;charset=utf-8',
                },
                body: JSON.stringify({
                    status,
                }),
            }
        );

        if (!statusResponse.ok) {
            const errorResponse = await statusResponse.json();

            showError(errorResponse.message ?? 'Не удалось изменить статус');

            return;
        }

        const response = await statusResponse.json();

        repair.value = response.data;

        showSuccess('Статус успешно изменен');
    } catch {
        showError('Не удалось связаться с сервером');
    } finally {
        isChangingStatus.value = false;
    }
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
    } catch (error) {
        showError('Не удалось связаться с сервером');
    } finally {
        isLoading.value = false;
    }
}

const deleteRepair = async () => {
    if (!repair.value) {
        return;
    }

    const confirmed = window.confirm(
        "Вы уверены, что хотите удалить этот ремонт?"
    );

    if (!confirmed) {
        return;
    }

    isDeleting.value = true;

    try {
        const response = await fetch(
            `/api/repairs/${repair.value.id}`,
            {
                method: "DELETE",
            }
        );

        if (!response.ok) {
            showError("Не удалось удалить ремонт");
            return;
        }

        showSuccess("Ремонт удалён");

        await router.push("/repairs");
    } catch (error) {
        showError("Не удалось связаться с сервером");
    } finally {
        isDeleting.value = false;
    }
};

onMounted(loadRepair);
</script>

<template>
    <div class="flex items-center justify-between">
        <PageTitle
            title="Ремонт"
            subtitle="Информация о ремонте"
        />

        <div
            v-if="repair"
            class="flex gap-3"
        >
            <RouterLink
                :to="{
                    name: 'repairs.edit',
                    params: {id:repair.id},
                    query: {from: 'show'}
                }"
                class="rounded-md border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50"
            >
                Редактировать
            </RouterLink>

            <button
                type="button"
                class="cursor-pointer rounded-md border border-red-300 bg-white px-4 py-2 text-sm font-medium text-red-600 hover:bg-red-50 disabled:cursor-not-allowed disabled:opacity-50"
                :disabled="isDeleting"
                @click="deleteRepair"
            >
                Удалить
            </button>
        </div>
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
        class="mt-6 space-y-6"
    >
        <div class="rounded-lg border border-gray-200 bg-white p-6">
            <div class="flex items-center justify-between">
                <h2 class="text-base font-semibold text-gray-900">
                    Ремонт №{{ repair.id }}
                </h2>

                <div class="flex items-center gap-3">
                    <select
                        :value="repair.status"
                        :disabled="isChangingStatus"
                        class="rounded-md border border-gray-300 bg-white px-3 py-2 text-sm font-medium text-gray-700 disabled:cursor-not-allowed disabled:opacity-50"
                        @change="changeStatus(($event.target as HTMLSelectElement).value)"
                    >
                        <option
                            v-for="(status, key) in repairStatusLabels"
                            :key="key"
                            :value="key"
                        >
                            {{ getStatusLabel(key) }}
                        </option>
                    </select>
                </div>
            </div>

            <div class="mt-6 grid grid-cols-3 gap-6">
                <div>
                    <p class="text-sm text-gray-500">
                        Дата приёма
                    </p>

                    <p class="mt-1 text-sm font-medium text-gray-900">
                        {{ formatDate(repair.received_at) }}
                    </p>
                </div>

                <div>
                    <p class="text-sm text-gray-500">
                        Дата завершения
                    </p>

                    <p class="mt-1 text-sm font-medium text-gray-900">
                        {{ formatDate(repair.completed_at) }}
                    </p>
                </div>

                <div>
                    <p class="text-sm text-gray-500">
                        Дата выдачи
                    </p>

                    <p class="mt-1 text-sm font-medium text-gray-900">
                        {{ formatDate(repair.issued_at) }}
                    </p>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-2 gap-6">
            <div class="rounded-lg border border-gray-200 bg-white p-6">
                <h2 class="text-base font-semibold text-gray-900">
                    Клиент
                </h2>

                <div class="mt-5 space-y-4">
                    <div>
                        <p class="text-sm text-gray-500">
                            Имя
                        </p>

                        <p class="mt-1 text-sm font-medium text-gray-900">
                            {{ repair.client.name }}
                        </p>
                    </div>

                    <div>
                        <p class="text-sm text-gray-500">
                            Телефон
                        </p>

                        <p class="mt-1 text-sm font-medium text-gray-900">
                            {{ repair.client.phone ?? "—" }}
                        </p>
                    </div>
                </div>
            </div>

            <div class="rounded-lg border border-gray-200 bg-white p-6">
                <h2 class="text-base font-semibold text-gray-900">
                    Устройство
                </h2>

                <div class="mt-5 space-y-4">
                    <div>
                        <p class="text-sm text-gray-500">
                            Устройство
                        </p>

                        <p class="mt-1 text-sm font-medium text-gray-900">
                            {{ repair.device.brand }}
                            {{ repair.device.model }}
                        </p>
                    </div>

                    <div>
                        <p class="text-sm text-gray-500">
                            Серийный номер
                        </p>

                        <p class="mt-1 text-sm font-medium text-gray-900">
                            {{ repair.device.serial_number ?? "—" }}
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <div class="rounded-lg border border-gray-200 bg-white p-6">
            <h2 class="text-base font-semibold text-gray-900">
                Информация о ремонте
            </h2>

            <div class="mt-5 space-y-6">
                <div>
                    <p class="text-sm text-gray-500">
                        Описание проблемы
                    </p>

                    <p class="mt-1 whitespace-pre-line text-sm text-gray-900">
                        {{ repair.problem_description }}
                    </p>
                </div>

                <div>
                    <p class="text-sm text-gray-500">
                        Диагностика
                    </p>

                    <p class="mt-1 whitespace-pre-line text-sm text-gray-900">
                        {{ repair.diagnosis ?? "—" }}
                    </p>
                </div>

                <div>
                    <p class="text-sm text-gray-500">
                        Выполненный ремонт
                    </p>

                    <p class="mt-1 whitespace-pre-line text-sm text-gray-900">
                        {{ repair.repair_description ?? "—" }}
                    </p>
                </div>
            </div>
        </div>

        <div class="rounded-lg border border-gray-200 bg-white p-6">
            <h2 class="text-base font-semibold text-gray-900">
                Стоимость
            </h2>

            <div class="mt-5 grid grid-cols-2 gap-6">
                <div>
                    <p class="text-sm text-gray-500">
                        Предварительная цена
                    </p>

                    <p class="mt-1 text-lg font-semibold text-gray-900">
                        {{ formatPrice(repair.estimated_price) }}
                    </p>
                </div>

                <div>
                    <p class="text-sm text-gray-500">
                        Итоговая цена
                    </p>

                    <p class="mt-1 text-lg font-semibold text-gray-900">
                        {{ formatPrice(repair.final_price) }}
                    </p>
                </div>
            </div>
        </div>

        <div class="rounded-lg border border-gray-200 bg-white p-6">
            <h2 class="text-base font-semibold text-gray-900">
                История ремонта
            </h2>

            <div class="mt-5 divide-y divide-gray-100">
                <div
                    v-for="history in repair.status_history"
                    :key="history.id"
                    class="flex items-center gap-4 py-3 first:pt-0 last:pb-0"
                >
                    <div>
                        <p class="text-sm font-medium text-gray-900">
                            {{ getStatusLabel(history.status) }}
                        </p>
                    </div>

                    <p class="text-sm text-gray-500">
                        {{ formatDate(history.created_at) }}
                    </p>
                </div>
            </div>
        </div>

        <div class="flex justify-start">
            <RouterLink
                to="/repairs"
                class="rounded-md border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50"
            >
                ← К списку ремонтов
            </RouterLink>
        </div>
    </div>
</template>
