<script setup lang="ts">

</script>

<template>
    <div class="mt-6 overflow-hidden rounded-lg border border-gray-200 bg-white">
        <table class="w-full text-left text-sm">
            <thead class="border-b border-gray-200 bg-gray-50">
            <tr>
                <th class="px-6 py-3 font-medium text-gray-600">
                    Клиент
                </th>

                <th class="px-6 py-3 font-medium text-gray-600">
                    Телевизор
                </th>

                <th class="px-6 py-3 font-medium text-gray-600">
                    Проблема
                </th>

                <th class="px-6 py-3 font-medium text-gray-600">
                    Статус
                </th>

                <th class="px-6 py-3 font-medium text-gray-600">
                    Цена
                </th>

                <th class="px-6 py-3 font-medium text-gray-600">
                    Принят
                </th>
            </tr>
            </thead>

            <tbody class="divide-y divide-gray-100">
            <tr>
                <td class="px-6 py-4 font-medium text-gray-900">
                    Иван Петров
                </td>

                <td class="px-6 py-4 text-gray-600">
                    Samsung UE55AU8000
                </td>

                <td class="px-6 py-4 text-gray-600">
                    Не включается
                </td>

                <td class="px-6 py-4">
                            <span class="rounded-full bg-blue-100 px-2.5 py-1 text-xs font-medium text-blue-700">
                                Диагностика
                            </span>
                </td>

                <td class="px-6 py-4 text-gray-600">
                    —
                </td>

                <td class="px-6 py-4 text-gray-500">
                    11.09.2026
                </td>
            </tr>

            <tr>
                <td class="px-6 py-4 font-medium text-gray-900">
                    Алексей Сидоров
                </td>

                <td class="px-6 py-4 text-gray-600">
                    LG 43NANO756
                </td>

                <td class="px-6 py-4 text-gray-600">
                    Нет изображения
                </td>

                <td class="px-6 py-4">
                            <span class="rounded-full bg-yellow-100 px-2.5 py-1 text-xs font-medium text-yellow-700">
                                Ждёт запчасть
                            </span>
                </td>

                <td class="px-6 py-4 text-gray-600">
                    8 500 ₽
                </td>

                <td class="px-6 py-4 text-gray-500">
                    10.09.2026
                </td>
            </tr>

            <tr>
                <td class="px-6 py-4 font-medium text-gray-900">
                    Сергей Иванов
                </td>

                <td class="px-6 py-4 text-gray-600">
                    Philips 50PUS8507
                </td>

                <td class="px-6 py-4 text-gray-600">
                    Перезагружается
                </td>

                <td class="px-6 py-4">
                            <span class="rounded-full bg-green-100 px-2.5 py-1 text-xs font-medium text-green-700">
                                Готов
                            </span>
                </td>

                <td class="px-6 py-4 text-gray-600">
                    6 000 ₽
                </td>

                <td class="px-6 py-4 text-gray-500">
                    09.09.2026
                </td>
            </tr>
            </tbody>
        </table>
    </div>
</template>

<style scoped>

</style>
