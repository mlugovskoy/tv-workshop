<script setup lang="ts">

import PageTitle from "./PageTitle.vue";
import Notification from "./Notification.vue";
</script>

<template>
    <div class="flex min-h-screen bg-slate-50">
        <aside class="w-60 border-r border-slate-200 bg-white p-6">
            <PageTitle title="Мастерская" tag="h2"/>

            <nav class="mt-8 flex flex-col gap-1">
                <RouterLink
                    to="/"
                    class="rounded-md px-3 py-2 text-slate-600 hover:bg-slate-100 hover:text-slate-900"
                    active-class="bg-blue-50 text-blue-700"
                >
                    Главная
                </RouterLink>

                <RouterLink
                    to="/repairs"
                    class="rounded-md px-3 py-2 text-slate-600 hover:bg-slate-100 hover:text-slate-900"
                    active-class="bg-blue-50 text-blue-700"
                >
                    Ремонты
                </RouterLink>

                <RouterLink
                    to="/clients"
                    class="rounded-md px-3 py-2 text-slate-600 hover:bg-slate-100 hover:text-slate-900"
                    active-class="bg-blue-50 text-blue-700"
                >
                    Клиенты
                </RouterLink>

                <RouterLink
                    to="/devices"
                    class="rounded-md px-3 py-2 text-slate-600 hover:bg-slate-100 hover:text-slate-900"
                    active-class="bg-blue-50 text-blue-700"
                >
                    Устройства
                </RouterLink>

                <RouterLink
                    to="/settings"
                    class="rounded-md px-3 py-2 text-slate-600 hover:bg-slate-100 hover:text-slate-900"
                    active-class="bg-blue-50 text-blue-700"
                >
                    Настройки
                </RouterLink>

<!--                <RouterLink-->
<!--                    to="/parts"-->
<!--                    class="rounded-md px-3 py-2 text-slate-600 hover:bg-slate-100 hover:text-slate-900"-->
<!--                    active-class="bg-blue-50 text-blue-700"-->
<!--                >-->
<!--                    Запчасти-->
<!--                </RouterLink>-->
            </nav>
        </aside>

        <main class="flex-1 p-8">
            <RouterView/>
        </main>

        <Notification />
    </div>
</template>
