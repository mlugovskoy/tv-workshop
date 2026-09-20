<script setup lang="ts">
import PageTitle from "../../components/PageTitle.vue";
import DefaultButton from "../../components/DefaultButton.vue";
import DefaultTable from "../../components/DefaultTable.vue";
import {useRouter} from "vue-router";
import Pagination from "../../components/Pagination.vue";
import EditIcon from "../../components/icons/EditIcon.vue";
import DeleteIcon from "../../components/icons/DeleteIcon.vue";
import {onMounted, ref} from "vue";
import {useNotification} from "../../composables/useNotification";
import {formatDateList} from "../../utils/formatDate";
import {formatPrice} from "../../utils/formatPrice";
import {repairStatusLabels, repairStatusStyles} from "../../utils/repairStatusLabels";
import RepairStatusBadge from "../../components/RepairStatusBadge.vue";

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

    estimated_price: string | null;
    final_price: string | null;

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
    {key: 'device', label: 'Устройство'},
    {key: 'client', label: 'Клиент'},
    {key: 'status', label: 'Статус'},
    {key: 'estimated_price', label: 'Предварительная цена'},
    {key: 'final_price', label: 'Итоговая цена'},
    {key: 'received_at', label: 'Дата приёма'}
];

const createRepair = () => {
    router.push({name: 'repairs.create'})
}

const showRepair = (id: number) => {
    router.push({
        name: 'repairs.show',
        params: {
            id
        }
    });
};
const editRepair = (id: number) => {
    router.push({
        name: 'repairs.edit',
        params: {
            id
        },
        query: {from: 'list'}
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
    const confirmed = window.confirm(
        'Вы уверены, что хотите удалить этот ремонт?'
    );

    if (!confirmed) {
        return;
    }


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
        <PageTitle title="Ремонты" subtitle="Список устройств в ремонте"/>

        <DefaultButton text="Новый ремонт" @click="createRepair"/>
    </div>

    <DefaultTable :columns="columnsTable" :rows="repairs" v-if="repairs.length > 0">
        <template #id="{ row }">
            <button
                type="button"
                class="cursor-pointer text-blue-600 p-2 hover:text-blue-800 underline hover:no-underline"
                @click="showRepair(row.id)"
            >
                {{ row.id }}
            </button>
        </template>
        <template #client="{ row }">
            {{ row.client.name }}, ID: {{ row.client.id }}
        </template>
        <template #status="{ row }">
            <RepairStatusBadge :status="row.status"/>
        </template>
        <template #device="{ row }">
            {{ row.device.brand }} {{ row.device.model }}
        </template>
        <template #estimated_price="{ row }">
            {{ formatPrice(row.estimated_price) ?? row.estimated_price }}
        </template>
        <template #final_price="{ row }">
            {{ formatPrice(row.final_price) ?? row.final_price }}
        </template>
        <template #received_at="{ row }">
            {{ formatDateList(row.received_at) ?? row.received_at }}
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
