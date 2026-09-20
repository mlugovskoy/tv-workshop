<script setup lang="ts">
import PageTitle from "../components/PageTitle.vue";
import RecentRepairs from "../components/RecentRepairs.vue";
import DashboardStats from "../components/DashboardStats.vue";
import {useNotification} from "../composables/useNotification";
import {onMounted, ref} from "vue";

interface DashboardStatistics {
    in_repair: number;
    ready: number;
    new_this_month: number;
    revenue_this_month: string;
}

interface RecentRepair {
    id: number;
    client: {
        id: number;
        name: string;
    };
    device: {
        id: number;
        brand: string;
        model: string;
    };
    status: string;
    created_at: string;
}

interface Dashboard {
    statistics: DashboardStatistics;
    recent_repairs: RecentRepair[];
}

const {showError} = useNotification();
const dashboard = ref<Dashboard | null>(null);
const isLoading = ref(false);

const loadDashboard = async () => {
    isLoading.value = true;

    try {
        const dashboardResponse = await fetch('/api/dashboard');

        if (!dashboardResponse.ok) {
            showError('Не удалось загрузить данные');
            return;
        }

        const response = await dashboardResponse.json();

        dashboard.value = response.data;
    } catch {
        showError('Не удалось связаться с сервером');
    } finally {
        isLoading.value = false;
    }
};

onMounted(loadDashboard);
</script>


<template>
    <PageTitle title="Главная"/>

    <div
        v-if="isLoading"
        class="mt-6 rounded-lg border border-slate-200 bg-white p-6"
    >
        <p class="text-sm text-slate-600">
            Загрузка...
        </p>
    </div>

    <template v-else-if="dashboard">
        <DashboardStats :statistics="dashboard.statistics"/>

        <RecentRepairs :repairs="dashboard.recent_repairs"/>
    </template>
</template>
