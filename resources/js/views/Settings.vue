<script setup>

import PageTitle from "../components/PageTitle.vue";
import DefaultButton from "../components/DefaultButton.vue";
import {ref} from "vue";
import {useNotification} from "../composables/useNotification.js";

const isLoading = ref(false);
const {showSuccess, showError} = useNotification();

const createBackup = async () => {
    isLoading.value = true;

    try {
        const response = await fetch("/api/backup", {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json;charset=utf-8'
            }
        });

        const data = await response.json();

        if (!response.ok) {
            showError(data.message ?? 'Не удалось создать резервную копию');
        }

        if (data.cancelled) {
            return;
        }

        showSuccess('Резервная копия успешно создана');
    } catch (error) {
        showError(error.message || "Не удалось создать резервную копию");
    } finally {
        isLoading.value = false;
    }
};
</script>

<template>
    <div class="flex items-center justify-between">
        <PageTitle title="Настройки"/>
    </div>

    <section class="rounded-lg border border-slate-200 bg-white p-5 mt-6">
        <div class="mb-5">
            <h2 class="text-lg font-semibold text-slate-900">
                Резервное копирование
            </h2>

            <p class="mt-1 text-sm text-slate-500">
                Сохраните копию базы данных, чтобы восстановить данные
                в случае сбоя или потери компьютера.
            </p>
        </div>

        <div class="flex items-center justify-between gap-6">
            <div>
                <h3 class="text-sm font-medium text-slate-900">
                    Создать резервную копию
                </h3>

                <p class="mt-1 text-sm text-slate-500">
                    Выберите место, куда будет сохранена копия базы данных.
                </p>
            </div>

            <DefaultButton
                :is-disabled="isLoading"
                :text="isLoading ? 'Создание...' : 'Создать копию'"
                @click="createBackup"
            />
        </div>
    </section>
</template>
