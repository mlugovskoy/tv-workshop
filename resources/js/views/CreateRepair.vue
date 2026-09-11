<script setup lang="ts">
import PageTitle from "../components/PageTitle.vue";
import DefaultButton from "../components/DefaultButton.vue";
import {reactive} from "vue";

interface RepairForm {
    clientId: number | null;
    brand: string;
    model: string;
    serialNumber: string;
    problem: string;
    price: number | null;
}

const form = reactive<RepairForm>({
    clientId: null,
    brand: "",
    model: "",
    serialNumber: "",
    problem: "",
    price: null,
});

const submit = () => {
    console.log(form);
};
</script>

<template>
    <div class="flex items-center justify-between">
        <PageTitle
            title="Новый ремонт"
            subtitle="Добавление телевизора в ремонт"
        />
    </div>

    <div class="mt-6 rounded-lg border border-gray-200 bg-white p-6">
        <form class="space-y-6" @submit.prevent="submit">

            <div>
                <label
                    for="client"
                    class="block text-sm font-medium text-gray-700"
                >
                    Клиент
                </label>

                <select
                    id="client"
                    class="mt-2 block w-full rounded-md border border-gray-300 px-3 py-2 text-sm"
                    v-model="form.clientId"
                >
                    <option :value="null">Выберите клиента</option>
                    <option :value="1">Иван Петров</option>
                    <option :value="2">Алексей Сидоров</option>
                </select>
            </div>

            <div class="border-t border-gray-200 pt-6">
                <h2 class="text-lg font-medium text-gray-900">
                    Телевизор
                </h2>

                <div class="mt-4 grid grid-cols-2 gap-4">
                    <div>
                        <label
                            for="brand"
                            class="block text-sm font-medium text-gray-700"
                        >
                            Бренд
                        </label>

                        <input
                            id="brand"
                            v-model="form.brand"
                            type="text"
                            class="mt-2 block w-full rounded-md border border-gray-300 px-3 py-2 text-sm"
                            placeholder="Например, Samsung"
                        />
                    </div>

                    <div>
                        <label
                            for="model"
                            class="block text-sm font-medium text-gray-700"
                        >
                            Модель
                        </label>

                        <input
                            id="model"
                            v-model="form.model"
                            type="text"
                            class="mt-2 block w-full rounded-md border border-gray-300 px-3 py-2 text-sm"
                            placeholder="Например, UE55..."
                        />
                    </div>

                    <div class="col-span-2">
                        <label
                            for="serial_number"
                            class="block text-sm font-medium text-gray-700"
                        >
                            Серийный номер
                        </label>

                        <input
                            id="serial_number"
                            v-model="form.serialNumber"
                            type="text"
                            class="mt-2 block w-full rounded-md border border-gray-300 px-3 py-2 text-sm"
                        />
                    </div>
                </div>
            </div>

            <div class="border-t border-gray-200 pt-6">
                <label
                    for="problem"
                    class="block text-sm font-medium text-gray-700"
                >
                    Проблема
                </label>

                <textarea
                    id="problem"
                    v-model="form.problem"
                    rows="4"
                    class="mt-2 block w-full rounded-md border border-gray-300 px-3 py-2 text-sm"
                    placeholder="Опишите неисправность со слов клиента"
                ></textarea>
            </div>

            <div>
                <label
                    for="estimated_price"
                    class="block text-sm font-medium text-gray-700"
                >
                    Ориентировочная стоимость
                </label>

                <input
                    id="estimated_price"
                    v-model.number="form.price"
                    type="number"
                    min="0"
                    step="0.01"
                    class="mt-2 block w-64 rounded-md border border-gray-300 px-3 py-2 text-sm"
                    placeholder="0.00"
                />
            </div>

            <div class="flex justify-end gap-3 border-t border-gray-200 pt-6">
                <RouterLink
                    to="/repairs"
                    class="rounded-md border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50"
                >
                    Отмена
                </RouterLink>

                <DefaultButton text="Создать ремонт" />
            </div>

        </form>
    </div>
</template>

<style scoped>
</style>
