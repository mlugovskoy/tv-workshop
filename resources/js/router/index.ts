import {createRouter, createWebHistory} from 'vue-router';

import Dashboard from '../views/Dashboard.vue';
import Repairs from '../views/Repairs.vue';
import Clients from '../views/Clients.vue';
import Parts from '../views/Parts.vue';
import CreateRepair from '../views/CreateRepair.vue';

const router = createRouter({
    history: createWebHistory(),

    routes: [
        {
            path: '/',
            name: 'dashboard',
            component: Dashboard,
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
            path: '/parts',
            name: 'parts',
            component: Parts,
        },
    ],
});

export default router;
