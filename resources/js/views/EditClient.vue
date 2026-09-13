<script setup lang="ts">
import {useRoute} from 'vue-router';
import {onMounted, reactive, ref} from "vue";
import PageTitle from "../components/PageTitle.vue";
import DefaultButton from "../components/DefaultButton.vue";
import router from "../router";
import {useNotification} from "../composables/useNotification";

interface ClientForm {
    name: string;
    phone: string | null;
    comment: string | null;
}

interface ValidationErrors {
    name?: string[];
    phone?: string[];
    comment?: string[];
}

const isSubmitting = ref(false);
const errors = reactive<ValidationErrors>({});
const route = useRoute();
const form = reactive<ClientForm>({
    name: "",
    phone: "",
    comment: ""
});
const {showSuccess, showError} = useNotification();

const submit = async () => {
    isSubmitting.value = true;
    errors.name = undefined;
    errors.phone = undefined;
    errors.comment = undefined;

    try {
        const response = await fetch(`/api/clients/${route.params.id}`, {
            method: 'PUT',
            headers: {
                'Content-Type': 'application/json;charset=utf-8'
            },
            body: JSON.stringify(form)
        });

        if (!response.ok) {
            const errorResponse = await response.json();

            for (const [key, value] of Object.entries(errorResponse.errors)) {
                errors[key as keyof ValidationErrors] = value;
            }

            return;
        }

        showSuccess('Клиент успешно изменен')
        router.push('/clients');
    } catch (error) {
        showError('Не удалось связаться с сервером');
    } finally {
        isSubmitting.value = false;
    }
};

onMounted(async () => {
    try {
        const clientResponse = await fetch(`/api/clients/${route.params.id}`);

        if (!clientResponse.ok) {
            showError('Не удалось загрузить клиента');
            return;
        }

        const response = await clientResponse.json();
        form.name = response.data.name;
        form.phone = response.data.phone;
        form.comment = response.data.comment;
    } catch (error) {
        showError('Не удалось связаться с сервером');
    }
})
</script>

<template>
    <div class="flex items-center justify-between">
        <PageTitle
            title="Редактирование клиента"
            subtitle="Редактирование существующего в базе клиента"
        />
    </div>

    <div class="mt-6 rounded-lg border border-gray-200 bg-white p-6">
        <form class="space-y-6" @submit.prevent="submit">

            <div class="mt-4 grid grid-cols-2 gap-4">
                <div>
                    <label
                        for="name"
                        class="block text-sm font-medium text-gray-700"
                    >
                        Имя
                    </label>

                    <input
                        id="name"
                        v-model="form.name"
                        type="text"
                        class="mt-2 block w-full rounded-md border border-gray-300 px-3 py-2 text-sm"
                        placeholder="Василий"
                    />

                    <p v-if="errors.name" class="mt-1 text-sm text-red-500">
                        {{ errors.name[0] }}
                    </p>
                </div>

                <div>
                    <label
                        for="phone"
                        class="block text-sm font-medium text-gray-700"
                    >
                        Телефон
                    </label>

                    <input
                        id="phone"
                        v-model="form.phone"
                        type="text"
                        class="mt-2 block w-full rounded-md border border-gray-300 px-3 py-2 text-sm"
                        placeholder="8914..."
                    />

                    <p v-if="errors.phone" class="mt-1 text-sm text-red-600">
                        {{ errors.phone[0] }}
                    </p>
                </div>

                <div class="col-span-2">
                    <label
                        for="comment"
                        class="block text-sm font-medium text-gray-700"
                    >
                        Комментарий
                    </label>

                    <input
                        id="comment"
                        v-model="form.comment"
                        type="text"
                        placeholder="Например, Постоянный клиент"
                        class="mt-2 block w-full rounded-md border border-gray-300 px-3 py-2 text-sm"
                    />

                    <p v-if="errors.comment" class="mt-1 text-sm text-red-600">
                        {{ errors.comment[0] }}
                    </p>
                </div>
            </div>

            <div class="flex justify-end gap-3 border-t border-gray-200 pt-6">
                <RouterLink
                    to="/clients"
                    class="rounded-md border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50"
                >
                    Отмена
                </RouterLink>

                <DefaultButton text="Сохранить" :isDisabled="isSubmitting"/>
            </div>

        </form>
    </div>
</template>
