<script setup lang="ts">
import {onMounted, ref} from "vue";
import {useRouter} from "vue-router";
import PageTitle from "../components/PageTitle.vue";
import DefaultTable from "../components/DefaultTable.vue";
import DefaultButton from "../components/DefaultButton.vue";
import {useNotification} from "../composables/useNotification";
import Pagination from "../components/Pagination.vue";
import DeleteIcon from "../components/icons/DeleteIcon.vue";
import EditIcon from "../components/icons/EditIcon.vue";
import SearchInput from "../components/SearchInput.vue";

interface Client {
    id: number;
    name: string;
    phone: string | null;
    comment: string | null;
}

interface ClientResource {
    data: Client[];
    current_page: number;
    last_page: number;
}

const currentPage = ref(1);
const lastPage = ref(1);
const clients = ref<Client[]>([]);
const columnsTable = [
    {key: 'name', label: 'Имя'},
    {key: 'phone', label: 'Телефон'},
    {key: 'comment', label: 'Комментарий'}
];

const router = useRouter()
const search = ref('');

const createClient = () => {
    router.push({name: 'clients.create'})
}
const {showSuccess, showError} = useNotification();

const editClient = (id: number) => {
    router.push({
        name: 'clients.edit',
        params: {
            id
        }
    })
};

const loadClients = async () => {
    try {
        const clientResponse = await fetch(`/api/clients?page=${currentPage.value}&search=${encodeURIComponent(search.value.toLowerCase())}`);

        if (!clientResponse.ok) {
            showError('Не удалось загрузить клиентов');
            return;
        }

        const response: ClientResource = await clientResponse.json();

        clients.value = response.data;
        currentPage.value = response.meta.current_page;
        lastPage.value = response.meta.last_page;
    } catch (error) {
        showError('Не удалось связаться с сервером');
    }
}

const searchClients = async () => {
    currentPage.value = 1;

    await loadClients()
}

const deleteClient = async (id: number) => {
    try {
        const response = await fetch(`/api/clients/${id}`, {
            method: 'DELETE',
        });

        if (response.status === 409) {
            const error = await response.json();
            showError(error.message);
            return;
        }

        if (!response.ok) {
            showError('Не удалось удалить клиента');
            return;
        }

        if (clients.value.length === 1 && currentPage.value > 1) {
            currentPage.value--;
        }

        await loadClients();

        showSuccess('Клиент удалён');
    } catch (error) {
        showError('Не удалось связаться с сервером');
    }
};

const goToPage = async (page: number) => {
    if (page < 1 || page > lastPage.value || page === currentPage.value) {
        return;
    }

    currentPage.value = page;

    await loadClients();
};

onMounted(loadClients);
</script>

<template>
    <div class="flex items-center justify-between">
        <PageTitle title="Клиенты"/>

        <SearchInput v-model="search"
                     @search="searchClients"/>

        <DefaultButton text="Новый клиент" @click="createClient"/>
    </div>

    <DefaultTable :columns="columnsTable" :rows="clients">
        <template #actions="{ row }">
            <button type="button"
                    class="cursor-pointer text-blue-600 hover:text-blue-800 h-5 w-5 flex items-center justify-center"
                    @click="editClient(row.id)">
                <EditIcon/>
            </button>
            <button type="button"
                    class="cursor-pointer text-red-600 hover:text-red-800 h-5 w-5 flex items-center justify-center"
                    @click="deleteClient(row.id)">
                <DeleteIcon/>
            </button>
        </template>
    </DefaultTable>

    <Pagination
        :current-page="currentPage"
        :last-page="lastPage"
        @change="goToPage"
    />
</template>
