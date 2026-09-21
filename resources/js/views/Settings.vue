```vue
<script setup lang="ts">
import {onMounted, ref} from 'vue';
import DefaultButton from '../components/DefaultButton.vue';
import {useNotification} from "../composables/useNotification";
import PageTitle from "@/components/PageTitle.vue";

interface UpdateAvailablePayload {
    version: string;
    releaseDate?: string;
    releaseName?: string;
    releaseNotes?: string;
}

const {showSuccess, showError} = useNotification();

const isChecking = ref(false);
const updateAvailable = ref<string | null>(null);

const checkForUpdates = async () => {
    if (isChecking.value) {
        return;
    }

    isChecking.value = true;
    updateAvailable.value = null;

    try {
        const response = await fetch('/api/app/check-update', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
        });

        if (!response.ok) {
            throw new Error();
        }
    } catch {
        isChecking.value = false;
        showError('Не удалось проверить обновления');
    }
};

const handleCheckingForUpdate = () => {
    isChecking.value = true;
    updateAvailable.value = null;
};

const handleUpdateAvailable = (payload: UpdateAvailablePayload) => {
    isChecking.value = false;
    updateAvailable.value = payload.version;

    showSuccess(`Доступно обновление ${payload.version}`);
};

const handleUpdateNotAvailable = () => {
    isChecking.value = false;
    updateAvailable.value = null;

    showSuccess('Установлена последняя версия');
};

const handleUpdateError = () => {
    isChecking.value = false;

    showError('Не удалось проверить наличие обновлений');
};

onMounted(() => {
    window.Native.on(
        'Native\\Desktop\\Events\\AutoUpdater\\CheckingForUpdate',
        handleCheckingForUpdate
    );

    window.Native.on(
        'Native\\Desktop\\Events\\AutoUpdater\\UpdateAvailable',
        handleUpdateAvailable
    );

    window.Native.on(
        'Native\\Desktop\\Events\\AutoUpdater\\UpdateNotAvailable',
        handleUpdateNotAvailable
    );

    window.Native.on(
        'Native\\Desktop\\Events\\AutoUpdater\\Error',
        handleUpdateError
    );
});
</script>

<template>
    <div>
        <PageTitle
            title="Настройки"
            subtitle="Настройки приложения"
        />

        <div class="mt-6 space-y-6">
            <section class="rounded-lg border border-gray-200 bg-white">
                <div class="border-b border-gray-200 px-6 py-4">
                    <h2 class="text-base font-semibold text-gray-900">
                        Внешний вид
                    </h2>

                    <p class="mt-1 text-sm text-gray-500">
                        Настройки отображения приложения
                    </p>
                </div>

                <div class="px-6 py-5">
                    <div class="flex items-center justify-between gap-6">
                        <div>
                            <h3 class="text-sm font-medium text-gray-900">
                                Фон приложения
                            </h3>

                            <p class="mt-1 text-sm text-gray-500">
                                Изменить фоновое изображение приложения
                            </p>
                        </div>

                        <span class="text-sm text-gray-400">
                            Скоро
                        </span>
                    </div>
                </div>
            </section>

            <section class="rounded-lg border border-gray-200 bg-white">
                <div class="border-b border-gray-200 px-6 py-4">
                    <h2 class="text-base font-semibold text-gray-900">
                        Приложение
                    </h2>

                    <p class="mt-1 text-sm text-gray-500">
                        Версия и обновления приложения
                    </p>
                </div>

                <div class="px-6 py-5">
                    <div class="flex items-center justify-between gap-6">
                        <div>
                            <h3 class="text-sm font-medium text-gray-900">
                                Обновления
                            </h3>

                            <p class="mt-1 text-sm text-gray-500">
                                Проверить наличие новой версии приложения
                            </p>

                            <p
                                v-if="updateAvailable"
                                class="mt-2 text-sm font-medium text-green-600"
                            >
                                Доступна версия {{ updateAvailable }}
                            </p>
                        </div>

                        <DefaultButton
                            :disabled="isChecking"
                            :text="isChecking
                                ? 'Проверка...'
                                : 'Проверить обновления'"
                            @click="checkForUpdates"
                        />
                    </div>
                </div>
            </section>
        </div>
    </div>
</template>
```
