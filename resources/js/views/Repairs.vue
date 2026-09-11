<script setup lang="ts">
import PageTitle from "../components/PageTitle.vue";
import DefaultButton from "../components/DefaultButton.vue";
import DefaultTable from "../components/DefaultTable.vue";
import {useRouter} from "vue-router";

const router = useRouter()

const createRepair = () => {
    router.push({name: 'repairs.create'})
}
</script>

<template>
    <div>
        <div class="flex items-center justify-between">
            <PageTitle title="Ремонты" subtitle="Список телевизоров в ремонте"/>

            <DefaultButton text="Новый ремонт" @click="createRepair"/>
        </div>

        <DefaultTable/>
    </div>
</template>

<style scoped>

</style>
