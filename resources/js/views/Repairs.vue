<script setup lang="ts">
import PageTitle from "../components/PageTitle.vue";
import DefaultButton from "../components/DefaultButton.vue";
import DefaultTable from "../components/DefaultTable.vue";
import {useRouter} from "vue-router";
import Pagination from "../components/Pagination.vue";
import EditIcon from "../components/icons/EditIcon.vue";
import DeleteIcon from "../components/icons/DeleteIcon.vue";
import SearchInput from "../components/SearchInput.vue";
import {onMounted, ref} from "vue";
import {useNotification} from "../composables/useNotification";

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

interface RepairResource {
    data: Repair[];
    meta: {
        current_page: number;
        last_page: number;
    };
}

const router = useRouter();
const currentPage = ref(1);
const lastPage = ref(1);
const repairs = ref<Repair[]>([]);
const {showSuccess, showError} = useNotification();
const columnsTable = [
    {key: 'id', label: 'ID'},
    {key: 'client', label: 'Клиент'},
    {key: 'device', label: 'Устройство'},
    {key: 'problem_description', label: 'Проблема'},
    {key: 'diagnosis', label: 'Диагностика'},
    {key: 'repair_description', label: 'Что сделано'},
    {key: 'status', label: 'Статус'},
    {key: 'estimated_price', label: 'Предварительная цена'},
    {key: 'final_price', label: 'Итоговая цена'},
    {key: 'received_at', label: 'Дата приёма'},
    {key: 'completed_at', label: 'Дата завершения'},
    {key: 'issued_at', label: 'Дата выдачи'},
];

const createRepair = () => {
    router.push({name: 'repairs.create'})
}
const editRepair = (id: number) => {
    router.push({
        name: 'repair.edit',
        params: {
            id
        }
    })
};

const loadRepairs = async () => {
    try {
        const repairResponse = await fetch(`/api/repairs?page=${currentPage.value}`);

        if (!repairResponse.ok) {
            showError('Не удалось загрузить список ремонтов');
            return;
        }

        const response: RepairResource = await repairResponse.json();

        repairs.value = response.data;
        currentPage.value = response.meta.current_page;
        lastPage.value = response.meta.last_page;
    } catch (error) {
        showError('Не удалось связаться с сервером');
    }
}

const deleteRepair = async (id: number) => {
    try {
        const response = await fetch(`/api/repairs/${id}`, {
            method: 'DELETE',
        });

        if (!response.ok) {
            showError('Не удалось удалить запись ремонта');
            return;
        }

        if (repairs.value.length === 1 && currentPage.value > 1) {
            currentPage.value--;
        }

        await loadRepairs();

        showSuccess('Запись ремонта удалена');
    } catch (error) {
        showError('Не удалось связаться с сервером');
    }
}

const goToPage = async (page: number) => {
    if (page < 1 || page > lastPage.value || page === currentPage.value) {
        return;
    }

    currentPage.value = page;

    await loadRepairs();
};

onMounted(loadRepairs);
</script>

<template>
    <div class="flex items-center justify-between">
        <PageTitle title="Ремонты" subtitle="Список телевизоров в ремонте"/>

        <DefaultButton text="Новый ремонт" @click="createRepair"/>
    </div>

    <DefaultTable :columns="columnsTable" :rows="repairs" v-if="repairs.length > 0">
        <template #client="{ row }">
            {{ row.client.name }}
        </template>
        <template #device="{ row }">
            {{ row.device.brand }} {{ row.device.model }}
        </template>
        <template #actions="{ row }">
            <button type="button"
                    class="cursor-pointer text-blue-600 hover:text-blue-800 h-5 w-5 flex items-center justify-center"
                    @click="editRepair(row.id)">
                <EditIcon/>
            </button>
            <button type="button"
                    class="cursor-pointer text-red-600 hover:text-red-800 h-5 w-5 flex items-center justify-center"
                    @click="deleteRepair(row.id)">
                <DeleteIcon/>
            </button>
        </template>
    </DefaultTable>

    <div v-else class="mt-6 flex min-h-48 items-center justify-center text-gray-500">
        Ремонты не найдены
    </div>

    <Pagination
        v-if="repairs.length > 0"
        :current-page="currentPage"
        :last-page="lastPage"
        @change="goToPage"
    />
</template>
