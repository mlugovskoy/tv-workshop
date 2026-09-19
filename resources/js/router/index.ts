import {createRouter, createWebHistory} from 'vue-router';

import Dashboard from '../views/Dashboard.vue';
import Repairs from '../views/Repairs.vue';
import Clients from '../views/Clients.vue';
import Parts from '../views/Parts.vue';
import CreateRepair from '../views/CreateRepair.vue';
import CreateClient from '../views/CreateClient.vue';
import EditClient from "../views/EditClient.vue";
import Devices from "../views/Devices.vue";
import CreateDevice from "../views/CreateDevice.vue";
import EditDevice from "../views/EditDevice.vue";

const router = createRouter({
    history: createWebHistory(),

    routes: [
        {
            path: '/',
            name: 'dashboard',
            component: Dashboard,
        },
        {
            path: '/devices',
            name: 'devices',
            component: Devices,
        },
        {
            path: '/devices/create',
            name: 'devices.create',
            component: CreateDevice,
        },
        {
            path: '/devices/:id/edit',
            name: 'devices.edit',
            component: EditDevice,
        },
        {
            path: '/repairs',
            name: 'repairs',
            component: Repairs,
        },
        {
            path: '/repairs/create',
            name: 'repairs.create',
            component: CreateRepair,
        },
        {
            path: '/clients',
            name: 'clients',
            component: Clients,
        },
        {
            path: '/clients/create',
            name: 'clients.create',
            component: CreateClient,
        },
        {
            path: '/clients/:id/edit',
            name: 'clients.edit',
            component: EditClient,
        },
        {
            path: '/parts',
            name: 'parts',
            component: Parts,
        },
    ],
});

export default router;
