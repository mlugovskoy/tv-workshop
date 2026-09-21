<script setup lang="ts">
import {onMounted, ref} from 'vue';
import DefaultButton from '../components/DefaultButton.vue';
import {useNotification} from "../composables/useNotification";
import PageTitle from "../components/PageTitle.vue";

interface UpdateAvailablePayload {
    version: string;
    releaseDate?: string;
    releaseName?: string;
    releaseNotes?: string;
}

interface DownloadProgressPayload {
    total: number;
    delta: number;
    transferred: number;
    percent: number;
    bytesPerSecond: number;
}

const {showSuccess, showError} = useNotification();

const isChecking = ref(false);
const updateAvailable = ref<string | null>(null);

const isDownloading = ref(false);
const downloadProgress = ref(0);

const updateDownloaded = ref(false);

const checkForUpdates = async () => {
    if (isChecking.value || isDownloading.value) {
        return;
    }

    isChecking.value = true;
    updateAvailable.value = null;
    updateDownloaded.value = false;

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

const downloadUpdate = async () => {
    if (isDownloading.value || !updateAvailable.value) {
        return;
    }

    isDownloading.value = true;
    downloadProgress.value = 0;

    try {
        const response = await fetch('/api/app/download-update', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
        });

        if (!response.ok) {
            throw new Error();
        }
    } catch {
        isDownloading.value = false;

        showError('Не удалось начать загрузку обновления');
    }
};

const installUpdate = async () => {
    try {
        const response = await fetch('/api/app/install-update', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
        });

        if (!response.ok) {
            throw new Error();
        }
    } catch {
        showError('Не удалось установить обновление');
    }
};

const handleCheckingForUpdate = () => {
    isChecking.value = true;
    updateAvailable.value = null;
    updateDownloaded.value = false;
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
    isDownloading.value = false;

    showError('Не удалось проверить обновления');
};

const handleDownloadProgress = (payload: DownloadProgressPayload) => {
    isDownloading.value = true;
    downloadProgress.value = Math.round(payload.percent);
};

const handleUpdateDownloaded = () => {
    isDownloading.value = false;
    downloadProgress.value = 100;
    updateDownloaded.value = true;

    showSuccess('Обновление скачано и готово к установке');
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

    window.Native.on(
        'Native\\Desktop\\Events\\AutoUpdater\\DownloadProgress',
        handleDownloadProgress
    );

    window.Native.on(
        'Native\\Desktop\\Events\\AutoUpdater\\UpdateDownloaded',
        handleUpdateDownloaded
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
                                v-if="updateAvailable && !isDownloading && !updateDownloaded"
                                class="mt-2 text-sm font-medium text-green-600"
                            >
                                Доступна версия {{ updateAvailable }}
                            </p>

                            <div
                                v-if="isDownloading"
                                class="mt-3 w-72"
                            >
                                <div class="mb-1 flex justify-between text-xs text-gray-500">
                                    <span>Загрузка обновления</span>
                                    <span>{{ downloadProgress }}%</span>
                                </div>

                                <div class="h-2 overflow-hidden rounded-full bg-gray-200">
                                    <div
                                        class="h-full rounded-full bg-gray-600 transition-all duration-300"
                                        :style="{ width: `${downloadProgress}%` }"
                                    />
                                </div>
                            </div>

                            <p
                                v-if="updateDownloaded"
                                class="mt-2 text-sm font-medium text-green-600"
                            >
                                Обновление скачано и готово к установке
                            </p>
                        </div>

                        <div class="shrink-0">
                            <DefaultButton
                                v-if="!updateAvailable && !updateDownloaded"
                                :disabled="isChecking || isDownloading"
                                :text="isChecking
                                    ? 'Проверка...'
                                    : 'Проверить обновления'"
                                @click="checkForUpdates"
                            />

                            <DefaultButton
                                v-if="updateAvailable && !isDownloading && !updateDownloaded"
                                :disabled="isDownloading"
                                text="Скачать обновление"
                                @click="downloadUpdate"
                            />

                            <DefaultButton
                                v-if="updateDownloaded"
                                text="Перезапустить и обновить"
                                @click="installUpdate"
                            />
                        </div>
                    </div>
                </div>
            </section>
        </div>
    </div>
</template>
