<script setup lang="ts">
import {onMounted, ref} from "vue";
import {useRouter} from "vue-router";
import PageTitle from "../../components/PageTitle.vue";
import DefaultTable from "../../components/DefaultTable.vue";
import DefaultButton from "../../components/DefaultButton.vue";
import {useNotification} from "../../composables/useNotification";
import Pagination from "../../components/Pagination.vue";
import DeleteIcon from "../../components/icons/DeleteIcon.vue";
import EditIcon from "../../components/icons/EditIcon.vue";
import SearchInput from "../../components/SearchInput.vue";

interface Device {
    id: number;
    client: {
        id: number;
        name: string;
    };
    brand: string;
    model: string;
    serial_number: string | null;
    comment: string | null;
}

interface DeviceResource {
    data: Device[];
    meta: {
        current_page: number;
        last_page: number;
    };
}

const currentPage = ref(1);
const lastPage = ref(1);
const devices = ref<Device[]>([]);
const columnsTable = [
    {key: 'id', label: 'ID'},
    {key: 'client', label: 'Клиент'},
    {key: 'brand', label: 'Бренд'},
    {key: 'model', label: 'Модель'},
    {key: 'serial_number', label: 'Серийный номер'},
    {key: 'comment', label: 'Комментарий'}
];

const router = useRouter()
const search = ref('');

const createDevice = () => {
    router.push({name: 'devices.create'})
}
const {showSuccess, showError} = useNotification();

const editDevice = (id: number) => {
    router.push({
        name: 'devices.edit',
        params: {
            id
        }
    })
};

const loadDevices = async () => {
    try {
        const deviceResponse = await fetch(`/api/devices?page=${currentPage.value}&search=${encodeURIComponent(search.value)}`);

        if (!deviceResponse.ok) {
            showError('Не удалось загрузить устройства');
            return;
        }

        const response: DeviceResource = await deviceResponse.json();

        devices.value = response.data;
        currentPage.value = response.meta.current_page;
        lastPage.value = response.meta.last_page;
    } catch (error) {
        showError('Не удалось связаться с сервером');
    }
}

const searchDevice = async () => {
    currentPage.value = 1;

    await loadDevices()
}

const deleteDevice = async (id: number) => {
    const confirmed = window.confirm(
        'Вы уверены, что хотите удалить это устройство?'
    );

    if (!confirmed) {
        return;
    }

    try {
        const response = await fetch(`/api/devices/${id}`, {
            method: 'DELETE',
        });

        if (response.status === 409) {
            const error = await response.json();
            showError(error.message);
            return;
        }

        if (!response.ok) {
            showError('Не удалось удалить устройство');
            return;
        }

        if (devices.value.length === 1 && currentPage.value > 1) {
            currentPage.value--;
        }

        await loadDevices();

        showSuccess('Устройство удалено');
    } catch (error) {
        showError('Не удалось связаться с сервером');
    }
}

const goToPage = async (page: number) => {
    if (page < 1 || page > lastPage.value || page === currentPage.value) {
        return;
    }

    currentPage.value = page;

    await loadDevices();
};

onMounted(loadDevices);
</script>

<template>
    <div class="flex items-center justify-between">
        <PageTitle title="Устройства"/>

        <SearchInput
            v-model="search"
            @search="searchDevice"/>

        <DefaultButton text="Новое устройство" @click="createDevice"/>
    </div>

    <DefaultTable :columns="columnsTable" :rows="devices" v-if="devices.length > 0">
        <template #client="{row}">
            {{ row.client.name }}, ID: {{row.client.id}}
        </template>
        <template #actions="{row}">
            <button type="button"
                    class="cursor-pointer text-blue-600 hover:text-blue-800 h-5 w-5 flex items-center justify-center"
                    @click="editDevice(row.id)">
                <EditIcon/>
            </button>
            <button type="button"
                    class="cursor-pointer text-red-600 hover:text-red-800 h-5 w-5 flex items-center justify-center"
                    @click="deleteDevice(row.id)">
                <DeleteIcon/>
            </button>
        </template>
    </DefaultTable>

    <div v-else class="mt-6 flex min-h-48 items-center justify-center text-gray-500">
        Устройства не найдены
    </div>

    <Pagination
        v-if="devices.length > 0"
        :current-page="currentPage"
        :last-page="lastPage"
        @change="goToPage"
    />
</template>
