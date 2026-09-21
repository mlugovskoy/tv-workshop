import {createRouter, createWebHistory} from 'vue-router';

import Dashboard from '../views/Dashboard.vue';
import Repairs from '../views/Repair/Repairs.vue';
import Clients from '../views/Client/Clients.vue';
import Parts from '../views/Parts.vue';
import CreateRepair from '../views/Repair/CreateRepair.vue';
import CreateClient from '../views/Client/CreateClient.vue';
import EditClient from "../views/Client/EditClient.vue";
import Devices from "../views/Device/Devices.vue";
import CreateDevice from "../views/Device/CreateDevice.vue";
import EditDevice from "../views/Device/EditDevice.vue";
import EditRepair from "../views/Repair/EditRepair.vue";
import ShowRepair from "../views/Repair/ShowRepair.vue";
import Settings from "../views/Settings.vue";

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
            path: '/repairs/:id/edit',
            name: 'repairs.edit',
            component: EditRepair,
        },
        {
            path: '/repairs/:id',
            name: 'repairs.show',
            component: ShowRepair,
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
            path: '/settings',
            name: 'settings',
            component: Settings
        }
        // {
        //     path: '/parts',
        //     name: 'parts',
        //     component: Parts,
        // },
    ],
});

export default router;
