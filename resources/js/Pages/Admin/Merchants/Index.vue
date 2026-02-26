<template>
    <div>
        <div class="panel pb-0 mt-6">
            <div class="flex justify-between items-center mb-5">
                <h5 class="font-semibold text-lg dark:text-white-light">{{ $t('merchants.title') }}</h5>
            </div>

            <!-- Filters Panel -->
            <div class="mb-5 p-4 border border-gray-200 dark:border-[#253a5b] rounded-lg shadow-sm">
                <h6 class="font-semibold text-md mb-3">{{ $t('filters') }}</h6>

                <div class="flex flex-wrap gap-4 mb-4">
                    <!-- Name -->
                    <div class="flex-1 min-w-[200px]">
                        <label for="name" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                            {{ $t('merchants.name') }}
                        </label>
                        <input
                            type="text"
                            id="name"
                            v-model="filterData.name"
                            class="form-input w-full"
                            :placeholder="$t('merchants.name')"
                        >
                    </div>

                    <!-- Username -->
                    <div class="flex-1 min-w-[200px]">
                        <label for="username" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                            {{ $t('merchants.username') }}
                        </label>
                        <input
                            type="text"
                            id="username"
                            v-model="filterData.username"
                            class="form-input w-full"
                            :placeholder="$t('merchants.username')"
                        >
                    </div>

                    <!-- Email -->
                    <div class="flex-1 min-w-[200px]">
                        <label for="email" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                            {{ $t('merchants.email') }}
                        </label>
                        <input
                            type="text"
                            id="email"
                            v-model="filterData.email"
                            class="form-input w-full"
                            :placeholder="$t('merchants.email')"
                        >
                    </div>

                    <!-- Search -->
                    <div class="flex-1 min-w-[200px]">
                        <label for="search" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                            {{ $t('search') }}
                        </label>
                        <input
                            type="text"
                            id="search"
                            v-model="filterData.search"
                            class="form-input w-full"
                            :placeholder="$t('merchants.search_placeholder')"
                        >
                    </div>
                </div>

                <div class="flex justify-between items-center mt-5">
                    <div class="flex space-x-3">
                        <button @click="applyFilters" class="btn btn-primary">{{ $t('filter') }}</button>
                        <button @click="resetFilters" class="btn btn-danger">{{ $t('reset') }}</button>
                    </div>
                </div>
            </div>

            <!-- Bulk Actions Bar -->
            <div v-if="selectedIds.length > 0" class="mb-5 p-4 bg-primary/10 border border-primary/30 rounded-lg flex flex-wrap items-center justify-between gap-4">
                <div class="flex items-center gap-2">
                    <span class="font-semibold text-primary">{{ selectedIds.length }} {{ $t('common.selected') }}</span>
                    <button @click="clearSelection" class="text-sm text-gray-500 hover:text-gray-700 dark:hover:text-gray-300">
                        ({{ $t('common.clear') }})
                    </button>
                </div>
                <div class="flex items-center gap-3">
                    <button @click="showBulkDeleteModal = true" class="btn btn-sm btn-outline-danger">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 ltr:mr-1 rtl:ml-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                        </svg>
                        {{ $t('common.delete') }}
                    </button>
                </div>
            </div>

            <!-- Column Chooser -->
            <div class="flex justify-between items-center mb-5 mt-5">
                <h2 class="text-lg font-semibold"></h2>
                <div ref="columnDropdownRef" class="flex items-center space-x-3 relative">
                    <button @click="toggleColumnChooser" class="btn btn-outline-secondary p-2" :title="$t('choose_columns')">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 6.75V5.25A2.25 2.25 0 0111.25 3h5.25A2.25 2.25 0 0118.75 5.25v13.5A2.25 2.25 0 0116.5 21H11.25A2.25 2.25 0 019 18.75v-1.5M15 12H3m0 0l3-3m-3 3l3 3" />
                        </svg>
                    </button>

                    <div
                        v-if="showColumnDropdown"
                        class="absolute right-0 top-10 w-72 bg-white dark:bg-[#1b2e4b] border border-gray-200 dark:border-[#253a5b] rounded-lg shadow-lg p-4 z-50"
                    >
                        <div class="flex justify-between items-center mb-3">
                            <h6 class="font-semibold text-md">{{ $t('choose_columns') }}</h6>
                            <button @click="toggleColumnChooser" class="text-gray-500 hover:text-gray-700 dark:hover:text-gray-300">
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>
                        </div>
                        <div class="space-y-2 max-h-64 overflow-y-auto">
                            <label
                                v-for="col in allColumns"
                                :key="col.field"
                                class="flex items-center gap-2 cursor-pointer hover:bg-gray-100 dark:hover:bg-[#253a5b] p-2 rounded"
                            >
                                <input type="checkbox" :checked="isColumnVisible(col.field)" @change="toggleColumn(col.field)" class="form-checkbox">
                                <span>{{ col.title }}</span>
                            </label>
                        </div>
                        <div class="border-t border-gray-200 dark:border-[#253a5b] mt-3 pt-3">
                            <button @click="resetColumnVisibility" class="btn btn-sm btn-outline-primary w-full">
                                {{ $t('reset_columns') }}
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Datatable -->
            <div class="datatable">
                <vue3-datatable
                    :rows="rows"
                    :columns="visibleColumns"
                    :totalRows="pagination.total"
                    :sortable="true"
                    :isServerMode="true"
                    :loading="loading"
                    skin="whitespace-nowrap bh-table-hover"
                    firstArrow='<svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" class="w-4.5 h-4.5 rtl:rotate-180"> <path d="M13 19L7 12L13 5" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/> <path opacity="0.5" d="M16.9998 19L10.9998 12L16.9998 5" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/> </svg>'
                    lastArrow='<svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" class="w-4.5 h-4.5 rtl:rotate-180"> <path d="M11 19L17 12L11 5" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/> <path opacity="0.5" d="M6.99976 19L12.9998 12L6.99976 5" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/> </svg>'
                    previousArrow='<svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" class="w-4.5 h-4.5 rtl:rotate-180"> <path d="M15 5L9 12L15 19" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/> </svg>'
                    nextArrow='<svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" class="w-4.5 h-4.5 rtl:rotate-180"> <path d="M9 5L15 12L9 19" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/> </svg>'
                    :sortColumn="sorting.column"
                    :sortDirection="sorting.direction"
                    :currentPage="pagination.currentPage"
                    :pageSize="pagination.perPage"
                    :showPageSize="true"
                    :showSearchFilter="false"
                    :showColumnFilter="false"
                    class="table-striped"
                    @change="handleTableChange"
                >
                    <template #header_checkbox>
                        <div class="flex items-center justify-center">
                            <input type="checkbox" class="grid-checkbox" :checked="isAllSelected" @change="toggleSelectAll" />
                        </div>
                    </template>

                    <template #checkbox="data">
                        <div class="flex items-center justify-center">
                            <input
                                type="checkbox"
                                class="grid-checkbox"
                                :checked="selectedIds.includes(data.value?.id)"
                                @change="toggleSelection(data.value?.id)"
                                @click.stop
                            />
                        </div>
                    </template>

                    <template #id="data">
                        <strong class="text-info">#{{ data.value.id }}</strong>
                    </template>

                    <template #user="data">
                        <div v-if="data.value.user" class="flex flex-col">
                            <span class="font-medium">{{ data.value.user.name }}</span>
                            <span class="text-xs text-gray-400">{{ data.value.user.email }}</span>
                        </div>
                        <span v-else class="text-gray-400 italic text-xs">{{ $t('merchants.no_user') }}</span>
                    </template>

                    <template #form_fields_count="data">
                        <span class="badge badge-outline-info">
                            {{ data.value.form_fields_count }}
                        </span>
                    </template>

                    <template #token_expiration="data">
                        <span v-if="data.value.token_expiration" :class="isExpired(data.value.token_expiration) ? 'text-danger font-medium' : 'text-success'">
                            {{ formatDate(data.value.token_expiration) }}
                        </span>
                        <span v-else class="text-gray-400 italic text-xs">—</span>
                    </template>

                    <template #created_at="data">
                        {{ formatDate(data.value.created_at) }}
                    </template>

                    <template #actions="data">
                        <div class="flex items-center gap-2">
                            <button
                                @click="confirmDelete(data.value)"
                                class="btn btn-sm btn-outline-danger"
                                :title="$t('common.delete')"
                            >
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                </svg>
                            </button>
                        </div>
                    </template>
                </vue3-datatable>
            </div>
        </div>

        <!-- Delete Confirmation Modal -->
        <div v-if="showDeleteModal" class="fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center">
            <div class="panel p-6 max-w-md w-full mx-4">
                <h3 class="text-lg font-semibold mb-4">{{ $t('common.confirm_delete') }}</h3>
                <p class="text-gray-600 dark:text-gray-400 mb-6">
                    {{ $t('merchants.delete_confirmation', { name: merchantToDelete?.name }) }}
                </p>
                <div class="flex justify-end gap-3">
                    <button type="button" class="btn btn-outline-secondary" @click="showDeleteModal = false">
                        {{ $t('common.cancel') }}
                    </button>
                    <button type="button" class="btn btn-danger" @click="deleteMerchant" :disabled="deleting">
                        {{ deleting ? $t('common.deleting') : $t('common.delete') }}
                    </button>
                </div>
            </div>
        </div>

        <!-- Bulk Delete Confirmation Modal -->
        <div v-if="showBulkDeleteModal" class="fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center">
            <div class="panel p-6 max-w-md w-full mx-4">
                <h3 class="text-lg font-semibold mb-4">{{ $t('common.confirm_bulk_delete') }}</h3>
                <p class="text-gray-600 dark:text-gray-400 mb-6">
                    {{ $t('merchants.bulk_delete_confirmation', { count: selectedIds.length }) }}
                </p>
                <div class="flex justify-end gap-3">
                    <button type="button" class="btn btn-outline-secondary" @click="showBulkDeleteModal = false">
                        {{ $t('common.cancel') }}
                    </button>
                    <button type="button" class="btn btn-danger" @click="applyBulkDelete" :disabled="bulkProcessing">
                        {{ bulkProcessing ? $t('common.deleting') : $t('common.delete') }}
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup lang="ts">
import { ref, computed } from 'vue';
import { useI18n } from 'vue-i18n';
import { useMeta } from '@/composables/use-meta';
import { useDataGrid, type GridResponse } from '@/composables/use-data-grid';
import Vue3Datatable from '@bhplugin/vue3-datatable';
import '@bhplugin/vue3-datatable/dist/style.css';
import { route } from 'ziggy-js';
import axios from 'axios';

interface MerchantUser {
    id: number;
    name: string;
    email: string;
}

interface Merchant {
    id: number;
    name: string;
    username: string;
    email: string | null;
    phone: string | null;
    store_reference: string | null;
    token_expiration: string | null;
    form_fields_count: number;
    user: MerchantUser | null;
    created_at: string;
}

interface MerchantFilters {
    name: string;
    username: string;
    email: string;
    search: string;
    [key: string]: string;
}

interface Props {
    initialMerchants: GridResponse<Merchant>;
}

const props = defineProps<Props>();
const i18n = useI18n();

useMeta({ title: i18n.t('merchants.title') });

const {
    rows,
    loading,
    pagination,
    sorting,
    filterData,
    allColumns,
    visibleColumns,
    showColumnDropdown,
    columnDropdownRef,
    fetchData,
    handleTableChange,
    applyFilters,
    resetFilters,
    toggleColumnChooser,
    isColumnVisible,
    toggleColumn,
    resetColumnVisibility,
} = useDataGrid<Merchant, MerchantFilters>({
    columns: [
        { field: 'checkbox',           title: '',                                     sort: false },
        { field: 'id',                 title: i18n.t('ID'),                           sort: true  },
        { field: 'name',               title: i18n.t('merchants.name'),               sort: true  },
        { field: 'username',           title: i18n.t('merchants.username'),           sort: true  },
        { field: 'email',              title: i18n.t('merchants.email'),              sort: true  },
        { field: 'phone',              title: i18n.t('merchants.phone'),              sort: false },
        { field: 'store_reference',    title: i18n.t('merchants.store_reference'),    sort: true  },
        { field: 'user',               title: i18n.t('merchants.linked_user'),        sort: false },
        { field: 'form_fields_count',  title: i18n.t('merchants.form_fields'),        sort: false },
        { field: 'token_expiration',   title: i18n.t('merchants.token_expiration'),   sort: false },
        { field: 'created_at',         title: i18n.t('Created At'),                   sort: true  },
        { field: 'actions',            title: i18n.t('Actions'),                      sort: false },
    ],
    fetchUrl: route('admin.merchants.list'),
    storageKey: 'admin_merchants',
    initialData: props.initialMerchants,
    defaultSort: { column: 'created_at', direction: 'desc' },
    initialFilters: {
        name: '',
        username: '',
        email: '',
        search: '',
    },
});

// --- Delete ---
const showDeleteModal = ref(false);
const merchantToDelete = ref<Merchant | null>(null);
const deleting = ref(false);

// --- Bulk ---
const selectedIds = ref<number[]>([]);
const showBulkDeleteModal = ref(false);
const bulkProcessing = ref(false);

const isAllSelected = computed(() =>
    rows.value.length > 0 && rows.value.every((row: Merchant) => selectedIds.value.includes(row.id))
);

const toggleSelectAll = () => {
    if (isAllSelected.value) {
        const rowIds = rows.value.map((r: Merchant) => r.id);
        selectedIds.value = selectedIds.value.filter(id => !rowIds.includes(id));
    } else {
        const rowIds = rows.value.map((r: Merchant) => r.id);
        const next = [...selectedIds.value];
        rowIds.forEach(id => { if (!next.includes(id)) next.push(id); });
        selectedIds.value = next;
    }
};

const toggleSelection = (id: number | undefined) => {
    if (id === undefined) return;
    const idx = selectedIds.value.indexOf(id);
    if (idx === -1) selectedIds.value.push(id);
    else selectedIds.value.splice(idx, 1);
};

const clearSelection = () => { selectedIds.value = []; };

const confirmDelete = (merchant: Merchant) => {
    merchantToDelete.value = merchant;
    showDeleteModal.value = true;
};

const deleteMerchant = async () => {
    if (!merchantToDelete.value) return;
    deleting.value = true;
    try {
        await axios.delete(route('admin.merchants.destroy', merchantToDelete.value.id));
        showDeleteModal.value = false;
        merchantToDelete.value = null;
        fetchData();
    } catch (error) {
        console.error('Failed to delete merchant:', error);
    } finally {
        deleting.value = false;
    }
};

const applyBulkDelete = async () => {
    if (selectedIds.value.length === 0) return;
    bulkProcessing.value = true;
    try {
        await axios.post(route('admin.merchants.bulk-destroy'), { ids: selectedIds.value });
        showBulkDeleteModal.value = false;
        clearSelection();
        fetchData();
    } catch (error) {
        console.error('Failed to bulk delete merchants:', error);
    } finally {
        bulkProcessing.value = false;
    }
};

const isExpired = (dateString: string) => new Date(dateString) < new Date();

const formatDate = (dateString: string | null) => {
    if (!dateString) return '—';
    return new Date(dateString).toLocaleDateString('en-US', {
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

<style>
.grid-checkbox {
    width: 18px;
    height: 18px;
    cursor: pointer;
    border: 2px solid #6b7280;
    border-radius: 4px;
    background-color: #fff;
    appearance: none;
    -webkit-appearance: none;
    position: relative;
}
.grid-checkbox:checked {
    background-color: #4361ee;
    border-color: #4361ee;
}
.grid-checkbox:checked::after {
    content: '';
    position: absolute;
    left: 5px;
    top: 2px;
    width: 5px;
    height: 9px;
    border: solid white;
    border-width: 0 2px 2px 0;
    transform: rotate(45deg);
}
.dark .grid-checkbox {
    background-color: #1b2e4b;
    border-color: #888ea8;
}
.dark .grid-checkbox:checked {
    background-color: #4361ee;
    border-color: #4361ee;
}
.bh-table-hover tbody tr:hover { background-color: rgba(67, 97, 238, 0.1); }
.dark .bh-table-hover tbody tr:hover { background-color: rgba(67, 97, 238, 0.2); }
.datatable .bh-datatable { @apply bg-white dark:bg-[#1b2e4b] rounded-lg; }
.datatable .bh-datatable th { @apply bg-gray-100 dark:bg-[#253a5b] text-gray-700 dark:text-white-light font-semibold; }
.datatable .bh-datatable td { @apply border-b border-gray-200 dark:border-[#253a5b] dark:text-gray-200; }
.datatable .bh-pagination { @apply flex items-center justify-between mt-4; }
.datatable .bh-pagination button { @apply px-3 py-1 rounded border border-gray-300 dark:border-[#253a5b] hover:bg-primary hover:text-white transition-colors; }
.datatable .bh-pagination button.active { @apply bg-primary text-white border-primary; }
.datatable .bh-pagination button:disabled { @apply opacity-50 cursor-not-allowed hover:bg-transparent hover:text-current; }
</style>
