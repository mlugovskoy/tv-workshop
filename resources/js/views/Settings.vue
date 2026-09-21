```vue
<script setup lang="ts">
import {onMounted, onUnmounted, ref} from 'vue';
import DefaultButton from '../components/DefaultButton.vue';
import {useNotification} from "../composables/useNotification";

interface UpdateAvailablePayload {
    version: string;
    releaseDate?: string;
    releaseName?: string;
    releaseNotes?: string;
}

const {showSuccess, showError} = useNotification();

const isChecking = ref(false);

const checkForUpdates = async () => {
    if (isChecking.value) {
        return;
    }

    isChecking.value = true;

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

        showSuccess('Проверка обновлений запущена');
    } catch {
        isChecking.value = false;
        showError('Не удалось проверить обновления');
    }
};

const handleCheckingForUpdate = () => {
    isChecking.value = true;
};

const handleUpdateAvailable = (payload: UpdateAvailablePayload) => {
    isChecking.value = false;

    showSuccess(`Доступно обновление ${payload.version}`);
};

const handleUpdateNotAvailable = () => {
    isChecking.value = false;

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
    <DefaultButton
        :disabled="isChecking"
        :text="isChecking ? 'Проверка...' : 'Проверить обновления'"
        @click="checkForUpdates"
    />
</template>
```
