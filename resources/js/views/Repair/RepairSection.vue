<script setup lang="ts">
import FormLabel from "../../components/FormLabel.vue";

interface CreateRepairForm {
    client_id: number | null;
    client_name: string;
    phone: string;
    device_id: number | null;
    brand: string;
    model: string;
    problem_description: string;
    estimated_price: number | null;
}

interface ValidationErrors {
    client_id?: string[];
    client_name?: string[];
    phone?: string[];
    device_id?: string[];
    brand?: string[];
    model?: string[];
    problem_description?: string[];
    estimated_price?: string[];
}

const form = defineModel<CreateRepairForm>('form', {
    required: true
})

const props = defineProps<{
    errors: ValidationErrors;
}>();
</script>

<template>
    <div class="border-t border-gray-200 pt-6">
        <h2 class="text-lg font-medium text-gray-900">
            Ремонт
        </h2>

        <div class="mt-4 space-y-4">
            <div class="max-w-sm">
                <FormLabel text="Предварительная цена"/>

                <input
                    id="estimated_price"
                    v-model.number="form.estimated_price"
                    type="number"
                    min="0"
                    class="mt-2 block w-full rounded-md border border-gray-300 px-3 py-2 text-sm"
                    placeholder="3000"
                />

                <p class="mt-1 text-xs text-gray-500">
                    Можно оставить пустым, если цена пока неизвестна
                </p>

                <p v-if="props.errors.estimated_price" class="mt-1 text-sm text-red-600">
                    {{ props.errors.estimated_price[0] }}
                </p>
            </div>
            <div class="col-span-2">
                <FormLabel text="Описание проблемы" required/>

                <textarea
                    id="problem_description"
                    v-model="form.problem_description"
                    rows="3"
                    placeholder="Например: не включается, мигает индикатор"
                    class="mt-2 block w-full resize-none rounded-md border border-gray-300 px-3 py-2 text-sm"
                />

                <p v-if="props.errors.problem_description" class="mt-1 text-sm text-red-600">
                    {{ props.errors.problem_description[0] }}
                </p>
            </div>
        </div>
    </div>
</template>
