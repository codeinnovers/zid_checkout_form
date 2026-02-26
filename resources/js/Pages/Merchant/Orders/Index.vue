<template>
    <div>
        <!-- Breadcrumb -->
        <ul class="flex space-x-2 rtl:space-x-reverse mb-5">
            <li>
                <a href="javascript:;" class="text-primary">{{ $t('checkout.title') }}</a>
            </li>
            <li class="before:content-['/'] ltr:before:mr-2 rtl:before:ml-2">
                <span>{{ $t('orders.title') }}</span>
            </li>
        </ul>

        <div class="panel pb-0 mt-6">
            <div class="flex justify-between items-center mb-5">
                <h5 class="font-semibold text-lg dark:text-white-light">{{ $t('orders.title') }}</h5>
            </div>

            <!-- Filters -->
            <div class="mb-5 p-4 border border-gray-200 dark:border-[#253a5b] rounded-lg shadow-sm">
                <h6 class="font-semibold text-md mb-3">{{ $t('filters') }}</h6>
                <div class="flex flex-wrap gap-4 mb-4">
                    <!-- Order Number -->
                    <div class="flex-1 min-w-[200px]">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                            {{ $t('orders.order_number') }}
                        </label>
                        <input
                            type="text"
                            v-model="filterData.order_number"
                            class="form-input w-full"
                            :placeholder="$t('orders.search_placeholder')"
                        />
                    </div>
                    <!-- Status -->
                    <div class="flex-1 min-w-[160px]">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                            {{ $t('status.title') }}
                        </label>
                        <select v-model="filterData.status" class="form-select w-full">
                            <option value="">{{ $t('orders.all_statuses') }}</option>
                            <option v-for="opt in statusOptions" :key="opt.id" :value="opt.id">{{ opt.label }}</option>
                        </select>
                    </div>
                </div>
                <div class="flex gap-2">
                    <button type="button" class="btn btn-primary btn-sm" @click="applyFilters">{{ $t('filter') }}</button>
                    <button type="button" class="btn btn-outline-danger btn-sm" @click="resetFilters">{{ $t('reset') }}</button>
                </div>
            </div>

            <!-- Bulk Actions -->
            <div v-if="selectedRows.length > 0" class="flex items-center gap-3 mb-4 p-3 bg-primary/10 rounded-lg">
                <span class="text-sm font-medium text-primary">{{ selectedRows.length }} {{ $t('common.selected') }}</span>
                <button type="button" class="btn btn-danger btn-sm" @click="confirmBulkDelete">
                    {{ $t('common.confirm_bulk_delete') }}
                </button>
                <button type="button" class="btn btn-outline-secondary btn-sm" @click="selectedRows = []">
                    {{ $t('common.clear') }}
                </button>
            </div>

            <!-- Column Chooser -->
            <div class="flex justify-end mb-2">
                <div class="relative" ref="columnChooserRef">
                    <button type="button" class="btn btn-outline-secondary btn-sm" @click="showColumnChooser = !showColumnChooser">
                        {{ $t('choose_columns') }}
                    </button>
                    <div v-if="showColumnChooser" class="absolute right-0 mt-1 w-48 bg-white dark:bg-[#1b2e4b] border border-gray-200 dark:border-[#253a5b] rounded shadow-lg z-10 p-3">
                        <label v-for="col in toggleableColumns" :key="col.key" class="flex items-center gap-2 text-sm py-1 cursor-pointer">
                            <input type="checkbox" v-model="col.visible" class="form-checkbox" />
                            {{ col.label }}
                        </label>
                        <button type="button" class="btn btn-xs btn-outline-secondary mt-2 w-full" @click="resetColumns">
                            {{ $t('reset_columns') }}
                        </button>
                    </div>
                </div>
            </div>

            <!-- Table -->
            <vue3-datatable
                :rows="rows"
                :columns="visibleColumns"
                :totalRows="totalRows"
                :isServerMode="true"
                :pageSize="pageSize"
                :loading="loading"
                skin="whitespace-nowrap bh-table-hover"
                firstArrow='<svg width="24" height="24" viewBox="0 0 24 24" fill="none"><path d="M13 19L7 12L13 5" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/><path opacity="0.5" d="M16.9998 19L10.9998 12L16.9998 5" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg>'
                lastArrow='<svg width="24" height="24" viewBox="0 0 24 24" fill="none"><path d="M11 19L17 12L11 5" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/><path opacity="0.5" d="M6.99976 19L12.9998 12L6.99976 5" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg>'
                previousArrow='<svg width="24" height="24" viewBox="0 0 24 24" fill="none"><path d="M15 5L9 12L15 19" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg>'
                nextArrow='<svg width="24" height="24" viewBox="0 0 24 24" fill="none"><path d="M9 5L15 12L9 19" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg>'
                @change="onTableChange"
            >
                <template #checkbox="{ row }">
                    <input type="checkbox" class="form-checkbox" :checked="selectedRows.includes(row.id)" @change="toggleRow(row.id)" />
                </template>

                <template #id="{ row }">
                    <strong class="text-info">#{{ row.id }}</strong>
                </template>

                <template #order_number="{ row }">
                    <span class="font-mono text-sm">{{ row.order_number }}</span>
                </template>

                <template #status="{ row }">
                    <span class="badge" :class="statusBadgeClass(row.status)">
                        {{ statusLabel(row.status) }}
                    </span>
                </template>

                <template #form_data="{ row }">
                    <div class="flex flex-wrap gap-1">
                        <span
                            v-for="(value, key) in previewData(row.form_data)"
                            :key="key"
                            class="inline-block text-xs bg-gray-100 dark:bg-[#1b2e4b] text-gray-600 dark:text-gray-300 px-2 py-0.5 rounded"
                        >
                            {{ fieldLabel(key) }}: {{ value }}
                        </span>
                        <span v-if="!row.form_data || Object.keys(row.form_data).length === 0" class="text-gray-400 text-xs">—</span>
                    </div>
                </template>

                <template #attachments="{ row }">
                    <span v-if="row.attachments && Object.keys(row.attachments).length > 0" class="badge badge-outline-info">
                        {{ Object.keys(row.attachments).length }} {{ $t('orders.files') }}
                    </span>
                    <span v-else class="text-gray-400 text-xs">—</span>
                </template>

                <template #created_at="{ row }">
                    <span class="text-gray-500 dark:text-gray-400 text-sm">{{ formatDate(row.created_at) }}</span>
                </template>

                <template #actions="{ row }">
                    <div class="flex items-center gap-2">
                        <button type="button" class="btn btn-xs btn-outline-primary" @click="openDetail(row)">
                            {{ $t('orders.view') }}
                        </button>
                        <button
                            type="button"
                            class="btn btn-xs btn-outline-success"
                            :disabled="row.status === 'processed'"
                            @click="processOrder(row)"
                        >
                            {{ $t('orders.process') }}
                        </button>
                        <button type="button" class="btn btn-xs btn-outline-danger" @click="confirmDelete(row)">
                            {{ $t('common.delete') }}
                        </button>
                    </div>
                </template>
            </vue3-datatable>
        </div>

        <!-- Detail Modal -->
        <div v-if="detailModal.open" class="fixed inset-0 z-[999] flex items-center justify-center bg-black/50 px-4" @click.self="detailModal.open = false">
            <div class="bg-white dark:bg-[#0e1726] rounded-xl shadow-2xl w-full max-w-2xl max-h-[90vh] overflow-y-auto">
                <div class="flex items-center justify-between px-6 py-4 border-b border-gray-200 dark:border-[#253a5b]">
                    <h3 class="text-lg font-semibold">{{ $t('orders.detail_title') }}</h3>
                    <button type="button" class="text-gray-400 hover:text-gray-700 dark:hover:text-gray-200 text-2xl leading-none" @click="detailModal.open = false">&times;</button>
                </div>
                <div class="p-6 space-y-4" v-if="detailModal.submission">
                    <!-- Header info -->
                    <div class="flex flex-wrap gap-4 items-start">
                        <div>
                            <p class="text-xs text-gray-400 mb-1">{{ $t('orders.order_number') }}</p>
                            <p class="font-mono font-bold text-xl text-primary">{{ detailModal.submission.order_number }}</p>
                        </div>
                        <div>
                            <p class="text-xs text-gray-400 mb-1">{{ $t('status.title') }}</p>
                            <span class="badge text-sm" :class="statusBadgeClass(detailModal.submission.status)">
                                {{ statusLabel(detailModal.submission.status) }}
                            </span>
                        </div>
                        <div class="ml-auto text-right">
                            <p class="text-xs text-gray-400 mb-1">{{ $t('orders.submitted_at') }}</p>
                            <p class="text-sm text-gray-600 dark:text-gray-300">{{ formatDate(detailModal.submission.created_at) }}</p>
                        </div>
                    </div>

                    <!-- Form Data -->
                    <div v-if="detailModal.submission.form_data && Object.keys(detailModal.submission.form_data).length > 0">
                        <h4 class="font-semibold mb-3 text-sm uppercase tracking-wide text-gray-500">{{ $t('orders.form_data') }}</h4>
                        <div class="space-y-2">
                            <div v-for="(value, key) in detailModal.submission.form_data" :key="key" class="flex gap-3">
                                <span class="text-sm font-medium text-gray-500 dark:text-gray-400 min-w-[140px] shrink-0">{{ fieldLabel(key) }}</span>
                                <span class="text-sm">{{ value }}</span>
                            </div>
                        </div>
                    </div>

                    <!-- Attachments -->
                    <div v-if="detailModal.submission.attachments && Object.keys(detailModal.submission.attachments).length > 0">
                        <h4 class="font-semibold mb-3 text-sm uppercase tracking-wide text-gray-500">{{ $t('orders.attachments') }}</h4>
                        <div class="space-y-2">
                            <div v-for="(path, key) in detailModal.submission.attachments" :key="key" class="flex items-center gap-3">
                                <span class="text-sm font-medium text-gray-500 dark:text-gray-400 min-w-[140px] shrink-0">{{ fieldLabel(key) }}</span>
                                <template v-if="isImage(path)">
                                    <a :href="`/storage/${path}`" target="_blank" class="inline-block">
                                        <img :src="`/storage/${path}`" class="h-16 w-24 object-cover rounded border border-gray-200 dark:border-[#253a5b]" />
                                    </a>
                                </template>
                                <template v-else>
                                    <a :href="`/storage/${path}`" target="_blank" class="btn btn-xs btn-outline-primary">
                                        {{ $t('orders.download') }}
                                    </a>
                                </template>
                            </div>
                        </div>
                    </div>

                    <!-- IP -->
                    <div v-if="detailModal.submission.ip_address" class="pt-3 border-t border-gray-100 dark:border-[#253a5b] text-xs text-gray-400">
                        {{ $t('orders.ip_address') }}: {{ detailModal.submission.ip_address }}
                    </div>
                </div>
            </div>
        </div>

        <!-- Delete Confirm Modal -->
        <div v-if="deleteModal.open" class="fixed inset-0 z-[999] flex items-center justify-center bg-black/50 px-4">
            <div class="bg-white dark:bg-[#0e1726] rounded-xl shadow-2xl w-full max-w-sm p-6">
                <h3 class="text-lg font-semibold mb-2">{{ $t('common.confirm_delete') }}</h3>
                <p class="text-sm text-gray-500 dark:text-gray-400 mb-5">
                    {{ $t('orders.delete_confirmation', { order: deleteModal.submission?.order_number }) }}
                </p>
                <div class="flex gap-3 justify-end">
                    <button type="button" class="btn btn-outline-secondary" @click="deleteModal.open = false">{{ $t('common.cancel') }}</button>
                    <button type="button" class="btn btn-danger" :disabled="deleteModal.loading" @click="doDelete">
                        {{ deleteModal.loading ? $t('common.deleting') : $t('common.delete') }}
                    </button>
                </div>
            </div>
        </div>

        <!-- Bulk Delete Confirm Modal -->
        <div v-if="bulkDeleteModal.open" class="fixed inset-0 z-[999] flex items-center justify-center bg-black/50 px-4">
            <div class="bg-white dark:bg-[#0e1726] rounded-xl shadow-2xl w-full max-w-sm p-6">
                <h3 class="text-lg font-semibold mb-2">{{ $t('common.confirm_bulk_delete') }}</h3>
                <p class="text-sm text-gray-500 dark:text-gray-400 mb-5">
                    {{ $t('orders.bulk_delete_confirmation', { count: selectedRows.length }) }}
                </p>
                <div class="flex gap-3 justify-end">
                    <button type="button" class="btn btn-outline-secondary" @click="bulkDeleteModal.open = false">{{ $t('common.cancel') }}</button>
                    <button type="button" class="btn btn-danger" :disabled="bulkDeleteModal.loading" @click="doBulkDelete">
                        {{ bulkDeleteModal.loading ? $t('common.deleting') : $t('common.delete') }}
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup lang="ts">
import { ref, computed, onMounted, onBeforeUnmount } from 'vue';
import { router } from '@inertiajs/vue3';
import { useI18n } from 'vue-i18n';
import { useMeta } from '@/composables/use-meta';
import { useNotifications } from '@/composables/use-notifications';
import { route } from 'ziggy-js';
import Vue3Datatable from '@bhplugin/vue3-datatable';

interface FormField {
    id: number;
    name: string;
    label: string;
    label_ar: string;
    field_type: string;
}

interface Submission {
    id: number;
    order_number: string;
    status: string;
    form_data: Record<string, any> | null;
    attachments: Record<string, string> | null;
    ip_address: string | null;
    created_at: string;
}

interface Props {
    initialSubmissions: { data: Submission[]; meta: { total: number; per_page: number; current_page: number; last_page: number } };
    formFields: FormField[];
    statusOptions: { id: string; label: string }[];
}

const props = defineProps<Props>();
const i18n = useI18n();
const { success, error } = useNotifications();

useMeta({ title: i18n.t('orders.title') });

// Field label map
const fieldLabelMap = computed(() => {
    const map: Record<string, string> = {};
    props.formFields.forEach(f => { map[f.name] = f.label; });
    return map;
});

const fieldLabel = (key: string) => fieldLabelMap.value[key] ?? key;

// Status helpers
const statusLabel = (status: string) => {
    const opt = props.statusOptions.find(o => o.id === status);
    return opt?.label ?? status;
};

const statusBadgeClass = (status: string) => {
    return status === 'processed' ? 'badge-outline-success' : 'badge-outline-warning';
};

// Table state
const loading = ref(false);
const rows = ref<Submission[]>(props.initialSubmissions.data);
const totalRows = ref(props.initialSubmissions.meta.total);
const pageSize = ref(props.initialSubmissions.meta.per_page);
const selectedRows = ref<number[]>([]);
const currentPage = ref(1);
const currentSort = ref({ column: 'id', direction: 'desc' });

// Filters
const filterData = ref({ order_number: '', status: '' });

// Column visibility
const STORAGE_KEY = 'datagrid_columns_merchant_orders';

const allColumns = [
    { key: 'checkbox', label: '', title: '', width: '40px', sortable: false, toggleable: false },
    { key: 'id', label: '#', title: '#', sortable: true, toggleable: false },
    { key: 'order_number', label: i18n.t('orders.order_number'), title: i18n.t('orders.order_number'), sortable: true, toggleable: true },
    { key: 'status', label: i18n.t('status.title'), title: i18n.t('status.title'), sortable: true, toggleable: true },
    { key: 'form_data', label: i18n.t('orders.form_data_preview'), title: i18n.t('orders.form_data_preview'), sortable: false, toggleable: true },
    { key: 'attachments', label: i18n.t('orders.attachments'), title: i18n.t('orders.attachments'), sortable: false, toggleable: true },
    { key: 'created_at', label: i18n.t('Created At'), title: i18n.t('Created At'), sortable: true, toggleable: true },
    { key: 'actions', label: i18n.t('Actions'), title: i18n.t('Actions'), sortable: false, toggleable: false },
];

const savedVisibility = (() => {
    try { return JSON.parse(localStorage.getItem(STORAGE_KEY) ?? 'null'); } catch { return null; }
})();

const toggleableColumns = ref(
    allColumns
        .filter(c => c.toggleable)
        .map(c => ({
            key: c.key,
            label: c.title,
            visible: savedVisibility ? (savedVisibility[c.key] ?? true) : true,
        }))
);

const visibleColumns = computed(() => {
    const hidden = new Set(toggleableColumns.value.filter(c => !c.visible).map(c => c.key));
    return allColumns.filter(c => !hidden.has(c.key));
});

const resetColumns = () => {
    toggleableColumns.value.forEach(c => { c.visible = true; });
    localStorage.removeItem(STORAGE_KEY);
};

const showColumnChooser = ref(false);
const columnChooserRef = ref<HTMLElement | null>(null);

const handleOutsideClick = (e: MouseEvent) => {
    if (columnChooserRef.value && !columnChooserRef.value.contains(e.target as Node)) {
        showColumnChooser.value = false;
    }
};

onMounted(() => document.addEventListener('click', handleOutsideClick));
onBeforeUnmount(() => document.removeEventListener('click', handleOutsideClick));

// Persist column visibility
const saveColumns = () => {
    const state: Record<string, boolean> = {};
    toggleableColumns.value.forEach(c => { state[c.key] = c.visible; });
    localStorage.setItem(STORAGE_KEY, JSON.stringify(state));
};
toggleableColumns.value.forEach(c => {
    // watch via a simple manual save on changes
});

// Data fetching
const fetchData = async (page = 1, sort = currentSort.value, filters = filterData.value) => {
    loading.value = true;
    try {
        const params: Record<string, any> = {
            page,
            sort_by: sort.column,
            sort_dir: sort.direction,
        };
        if (filters.order_number) params.order_number = filters.order_number;
        if (filters.status) params.status = filters.status;

        const res = await fetch(route('merchant.orders.list') + '?' + new URLSearchParams(params).toString(), {
            headers: { 'X-Requested-With': 'XMLHttpRequest', Accept: 'application/json' },
        });
        const data = await res.json();
        rows.value = data.data ?? [];
        totalRows.value = data.meta?.total ?? 0;
    } catch {
        error(i18n.t('common.error', 'Failed to load data'));
    } finally {
        loading.value = false;
    }
};

const onTableChange = (params: any) => {
    currentPage.value = params.current_page ?? 1;
    if (params.sort_column) {
        currentSort.value = { column: params.sort_column, direction: params.sort_direction ?? 'asc' };
    }
    fetchData(currentPage.value, currentSort.value);
};

const applyFilters = () => fetchData(1, currentSort.value, filterData.value);
const resetFilters = () => {
    filterData.value = { order_number: '', status: '' };
    fetchData(1, currentSort.value, filterData.value);
};

// Row selection
const toggleRow = (id: number) => {
    const idx = selectedRows.value.indexOf(id);
    if (idx >= 0) selectedRows.value.splice(idx, 1);
    else selectedRows.value.push(id);
};

// Helpers
const previewData = (formData: Record<string, any> | null) => {
    if (!formData) return {};
    const entries = Object.entries(formData).slice(0, 3);
    return Object.fromEntries(entries);
};

const isImage = (path: string) => /\.(jpg|jpeg|png|gif|webp|svg)$/i.test(path);

const formatDate = (dateStr: string | null) => {
    if (!dateStr) return '—';
    return new Date(dateStr).toLocaleString('en-US', { year: 'numeric', month: 'short', day: 'numeric', hour: '2-digit', minute: '2-digit' });
};

// Detail modal
const detailModal = ref<{ open: boolean; submission: Submission | null }>({ open: false, submission: null });
const openDetail = (row: Submission) => { detailModal.value = { open: true, submission: row }; };

// Process order
const processOrder = async (row: Submission) => {
    if (row.status === 'processed') return;
    try {
        const csrf = (document.cookie.match(/XSRF-TOKEN=([^;]+)/) ?? [])[1];
        await fetch(route('merchant.orders.process', { submission: row.id }), {
            method: 'PATCH',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'X-XSRF-TOKEN': decodeURIComponent(csrf ?? ''),
                Accept: 'application/json',
            },
        });
        // Will be fully implemented in the next step
        success(i18n.t('orders.process_initiated'));
        fetchData(currentPage.value);
    } catch {
        error(i18n.t('orders.process_error'));
    }
};

// Delete
const deleteModal = ref<{ open: boolean; submission: Submission | null; loading: boolean }>({
    open: false, submission: null, loading: false,
});

const confirmDelete = (row: Submission) => { deleteModal.value = { open: true, submission: row, loading: false }; };

const doDelete = async () => {
    if (!deleteModal.value.submission) return;
    deleteModal.value.loading = true;
    try {
        const csrf = (document.cookie.match(/XSRF-TOKEN=([^;]+)/) ?? [])[1];
        const res = await fetch(route('merchant.submissions.destroy', { submission: deleteModal.value.submission.id }), {
            method: 'DELETE',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'X-XSRF-TOKEN': decodeURIComponent(csrf ?? ''),
                Accept: 'application/json',
            },
        });
        if (res.ok) {
            success(i18n.t('orders.deleted'));
            deleteModal.value.open = false;
            fetchData(currentPage.value);
        }
    } catch {
        error(i18n.t('orders.delete_error'));
    } finally {
        deleteModal.value.loading = false;
    }
};

// Bulk delete
const bulkDeleteModal = ref<{ open: boolean; loading: boolean }>({ open: false, loading: false });
const confirmBulkDelete = () => { bulkDeleteModal.value = { open: true, loading: false }; };

const doBulkDelete = async () => {
    bulkDeleteModal.value.loading = true;
    try {
        const csrf = (document.cookie.match(/XSRF-TOKEN=([^;]+)/) ?? [])[1];
        await fetch(route('merchant.submissions.bulk-destroy'), {
            method: 'POST',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'X-XSRF-TOKEN': decodeURIComponent(csrf ?? ''),
                'Content-Type': 'application/json',
                Accept: 'application/json',
            },
            body: JSON.stringify({ ids: selectedRows.value }),
        });
        success(i18n.t('orders.bulk_deleted', { count: selectedRows.value.length }));
        selectedRows.value = [];
        bulkDeleteModal.value.open = false;
        fetchData(currentPage.value);
    } catch {
        error(i18n.t('orders.bulk_delete_error'));
    } finally {
        bulkDeleteModal.value.loading = false;
    }
};
</script>

<script lang="ts">
import appLayout from '@/layouts/app-layout.vue';
export default { layout: appLayout };
</script>
