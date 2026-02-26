<template>
    <div>
        <!-- Breadcrumb -->
        <ul class="flex space-x-2 rtl:space-x-reverse mb-5">
            <li>
                <span class="text-primary">{{ $t('admin.title') }}</span>
            </li>
            <li class="before:content-['/'] ltr:before:mr-2 rtl:before:ml-2">
                <span>{{ $t('dashboard') }}</span>
            </li>
        </ul>

        <!-- Stats cards -->
        <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-3 gap-6 mb-6">
            <!-- Total Users -->
            <div class="panel h-full">
                <div class="flex justify-between dark:text-white-light mb-5">
                    <h5 class="font-semibold text-lg">{{ $t('admin.stats.total_users') }}</h5>
                    <svg class="w-5 h-5 text-primary" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <circle cx="9" cy="6" r="4" fill="currentColor" />
                        <path opacity="0.5" d="M17 8C18.6569 8 20 6.65685 20 5C20 3.34315 18.6569 2 17 2" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" />
                        <path d="M2 18C2 15.2386 5.13401 13 9 13C12.866 13 16 15.2386 16 18" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" />
                        <path opacity="0.5" d="M22 20C22 17.7614 19.3137 16 16 16" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" />
                    </svg>
                </div>
                <div class="text-3xl font-bold text-primary">{{ stats.totalUsers }}</div>
                <div class="flex gap-4 mt-3 text-sm text-gray-500 dark:text-gray-400">
                    <span class="text-info font-medium">{{ stats.totalAdmins }} {{ $t('admin.stats.admins') }}</span>
                    <span>·</span>
                    <span class="text-success font-medium">{{ stats.totalMerchants }} {{ $t('admin.stats.merchants') }}</span>
                </div>
            </div>

            <!-- Active Users -->
            <div class="panel h-full">
                <div class="flex justify-between dark:text-white-light mb-5">
                    <h5 class="font-semibold text-lg">{{ $t('admin.stats.active_users') }}</h5>
                    <svg class="w-5 h-5 text-success" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <circle cx="12" cy="12" r="3" fill="currentColor" />
                        <path opacity="0.5" d="M20.188 10.9343C20.5762 11.4056 20.7703 11.6412 20.7703 12C20.7703 12.3588 20.5762 12.5944 20.188 13.0657C18.7679 14.7899 15.6357 18 12 18C8.36427 18 5.23206 14.7899 3.81197 13.0657C3.42381 12.5944 3.22973 12.3588 3.22973 12C3.22973 11.6412 3.42381 11.4056 3.81197 10.9343C5.23206 9.21014 8.36427 6 12 6C15.6357 6 18.7679 9.21014 20.188 10.9343Z" stroke="currentColor" stroke-width="1.5" />
                    </svg>
                </div>
                <div class="text-3xl font-bold text-success">{{ stats.activeUsers }}</div>
                <div class="mt-3 text-sm">
                    <span class="text-success/70">{{ activePercent }}% {{ $t('admin.stats.of_total') }}</span>
                </div>
            </div>

            <!-- Inactive / Suspended -->
            <div class="panel h-full">
                <div class="flex justify-between dark:text-white-light mb-5">
                    <h5 class="font-semibold text-lg">{{ $t('admin.stats.inactive_suspended') }}</h5>
                    <svg class="w-5 h-5 text-danger" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <circle opacity="0.5" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="1.5" />
                        <path d="M14.5 9.5L9.5 14.5M9.5 9.5L14.5 14.5" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" />
                    </svg>
                </div>
                <div class="text-3xl font-bold text-danger">{{ stats.inactiveUsers + stats.suspendedUsers }}</div>
                <div class="flex gap-4 mt-3 text-sm">
                    <span class="text-warning font-medium">{{ stats.inactiveUsers }} {{ $t('status.inactive') }}</span>
                    <span>·</span>
                    <span class="text-danger font-medium">{{ stats.suspendedUsers }} {{ $t('status.suspended') }}</span>
                </div>
            </div>
        </div>

        <!-- Recent Users -->
        <div class="panel">
            <div class="flex items-center justify-between mb-5">
                <h5 class="font-semibold text-lg dark:text-white-light">{{ $t('admin.recent_users') }}</h5>
                <Link :href="route('admin.users.index')" class="btn btn-sm btn-outline-primary">
                    {{ $t('users.list') }}
                </Link>
            </div>
            <div class="table-responsive">
                <table class="table-striped table-hover">
                    <thead>
                        <tr>
                            <th>{{ $t('ID') }}</th>
                            <th>{{ $t('users.name') }}</th>
                            <th>{{ $t('users.email') }}</th>
                            <th>{{ $t('users.role') }}</th>
                            <th>{{ $t('Status') }}</th>
                            <th>{{ $t('Created At') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="user in recentUsers" :key="user.id">
                            <td><strong class="text-info">#{{ user.id }}</strong></td>
                            <td>{{ user.name }}</td>
                            <td class="text-gray-500 dark:text-gray-400">{{ user.email }}</td>
                            <td>
                                <span class="badge" :class="user.role === 'admin' ? 'badge-outline-primary' : 'badge-outline-success'">
                                    {{ getRoleLabel(user.role) }}
                                </span>
                            </td>
                            <td>
                                <span class="badge" :class="getStatusBadgeClass(user.status)">
                                    {{ getStatusLabel(user.status) }}
                                </span>
                            </td>
                            <td class="text-gray-500 dark:text-gray-400">{{ formatDateTime(user.created_at) }}</td>
                        </tr>
                        <tr v-if="recentUsers.length === 0">
                            <td colspan="6" class="text-center text-gray-500 dark:text-gray-400 py-4">
                                {{ $t('admin.no_users') }}
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</template>

<script setup lang="ts">
import { computed } from 'vue';
import { Link } from '@inertiajs/vue3';
import { useI18n } from 'vue-i18n';
import { useMeta } from '@/composables/use-meta';
import { route } from 'ziggy-js';

interface Stats {
    totalUsers: number;
    totalAdmins: number;
    totalMerchants: number;
    activeUsers: number;
    inactiveUsers: number;
    suspendedUsers: number;
}

interface RecentUser {
    id: number;
    name: string;
    email: string;
    role: string;
    status: string;
    created_at: string;
}

interface Props {
    stats: Stats;
    recentUsers: RecentUser[];
}

const props = defineProps<Props>();
const i18n = useI18n();

useMeta({ title: i18n.t('dashboard') });

const activePercent = computed(() => {
    if (!props.stats.totalUsers) return 0;
    return Math.round((props.stats.activeUsers / props.stats.totalUsers) * 100);
});

const getRoleLabel = (role: string) => {
    return role === 'admin' ? i18n.t('admin.role_admin') : i18n.t('admin.role_merchant');
};

const getStatusLabel = (status: string) => {
    const map: Record<string, string> = {
        active: i18n.t('status.active'),
        inactive: i18n.t('status.inactive'),
        suspended: i18n.t('status.suspended'),
    };
    return map[status] || status;
};

const getStatusBadgeClass = (status: string) => {
    const map: Record<string, string> = {
        active: 'badge-outline-success',
        inactive: 'badge-outline-warning',
        suspended: 'badge-outline-danger',
    };
    return map[status] || 'badge-outline-secondary';
};

const formatDateTime = (dateString: string | null) => {
    if (!dateString) return '-';
    return new Date(dateString).toLocaleString('en-US', {
        year: 'numeric',
        month: 'short',
        day: 'numeric',
    });
};
</script>

<script lang="ts">
import appLayout from '@/layouts/app-layout.vue';

export default {
    layout: appLayout,
};
</script>
