<template>
    <div>
        <div class="panel pb-0 mt-6">
            <div class="flex justify-between items-center mb-5">
                <h5 class="font-semibold text-lg dark:text-white-light">{{ $t('submissions.title') }}</h5>
            </div>

            <!-- Filters -->
            <div class="mb-5 p-4 border border-gray-200 dark:border-[#253a5b] rounded-lg shadow-sm">
                <h6 class="font-semibold text-md mb-3">{{ $t('filters') }}</h6>
                <div class="flex flex-wrap gap-4 mb-4">
                    <div class="flex-1 min-w-[200px]">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">{{ $t('submissions.order_number') }}</label>
                        <input type="text" v-model="filterData.order_number" class="form-input w-full" :placeholder="$t('submissions.order_number')">
                    </div>
                    <div class="flex-1 min-w-[200px]">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">{{ $t('search') }}</label>
                        <input type="text" v-model="filterData.search" class="form-input w-full" :placeholder="$t('submissions.search_placeholder')">
                    </div>
                </div>
                <div class="flex space-x-3 mt-5">
                    <button @click="applyFilters" class="btn btn-primary">{{ $t('filter') }}</button>
                    <button @click="resetFilters" class="btn btn-danger">{{ $t('reset') }}</button>
                </div>
            </div>

            <!-- Bulk Actions Bar -->
            <div v-if="selectedIds.length > 0" class="mb-5 p-4 bg-primary/10 border border-primary/30 rounded-lg flex flex-wrap items-center justify-between gap-4">
                <div class="flex items-center gap-2">
                    <span class="font-semibold text-primary">{{ selectedIds.length }} {{ $t('common.selected') }}</span>
                    <button @click="clearSelection" class="text-sm text-gray-500 hover:text-gray-700 dark:hover:text-gray-300">({{ $t('common.clear') }})</button>
                </div>
                <button @click="showBulkDeleteModal = true" class="btn btn-sm btn-outline-danger">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 ltr:mr-1 rtl:ml-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                    </svg>
                    {{ $t('common.delete') }}
                </button>
            </div>

            <!-- Column Chooser -->
            <div class="flex justify-end mb-5 mt-5">
                <div ref="columnDropdownRef" class="relative">
                    <button @click="toggleColumnChooser" class="btn btn-outline-secondary p-2" :title="$t('choose_columns')">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 6.75V5.25A2.25 2.25 0 0111.25 3h5.25A2.25 2.25 0 0118.75 5.25v13.5A2.25 2.25 0 0116.5 21H11.25A2.25 2.25 0 019 18.75v-1.5M15 12H3m0 0l3-3m-3 3l3 3" />
                        </svg>
                    </button>
                    <div v-if="showColumnDropdown" class="absolute right-0 top-10 w-64 bg-white dark:bg-[#1b2e4b] border border-gray-200 dark:border-[#253a5b] rounded-lg shadow-lg p-4 z-50">
                        <div class="flex justify-between items-center mb-3">
                            <h6 class="font-semibold text-md">{{ $t('choose_columns') }}</h6>
                            <button @click="toggleColumnChooser" class="text-gray-500 hover:text-gray-700 dark:hover:text-gray-300">
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                            </button>
                        </div>
                        <div class="space-y-2 max-h-64 overflow-y-auto">
                            <label v-for="col in allColumns" :key="col.field" class="flex items-center gap-2 cursor-pointer hover:bg-gray-100 dark:hover:bg-[#253a5b] p-2 rounded">
                                <input type="checkbox" :checked="isColumnVisible(col.field)" @change="toggleColumn(col.field)" class="form-checkbox">
                                <span>{{ col.title }}</span>
                            </label>
                        </div>
                        <div class="border-t border-gray-200 dark:border-[#253a5b] mt-3 pt-3">
                            <button @click="resetColumnVisibility" class="btn btn-sm btn-outline-primary w-full">{{ $t('reset_columns') }}</button>
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
                    firstArrow='<svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" class="w-4.5 h-4.5 rtl:rotate-180"><path d="M13 19L7 12L13 5" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/><path opacity="0.5" d="M16.9998 19L10.9998 12L16.9998 5" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg>'
                    lastArrow='<svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" class="w-4.5 h-4.5 rtl:rotate-180"><path d="M11 19L17 12L11 5" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/><path opacity="0.5" d="M6.99976 19L12.9998 12L6.99976 5" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg>'
                    previousArrow='<svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" class="w-4.5 h-4.5 rtl:rotate-180"><path d="M15 5L9 12L15 19" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg>'
                    nextArrow='<svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" class="w-4.5 h-4.5 rtl:rotate-180"><path d="M9 5L15 12L9 19" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg>'
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
                            <input type="checkbox" class="grid-checkbox" :checked="selectedIds.includes(data.value?.id)" @change="toggleSelection(data.value?.id)" @click.stop />
                        </div>
                    </template>

                    <template #id="data">
                        <strong class="text-info">#{{ data.value.id }}</strong>
                    </template>

                    <template #order_number="data">
                        <span class="font-mono font-semibold text-primary">{{ data.value.order_number }}</span>
                    </template>

                    <template #form_data_preview="data">
                        <div class="flex flex-wrap gap-1 max-w-xs">
                            <span
                                v-for="(value, key) in getPreviewFields(data.value.form_data)"
                                :key="key"
                                class="inline-flex items-center gap-1 text-xs bg-gray-100 dark:bg-[#253a5b] rounded px-2 py-0.5"
                            >
                                <span class="text-gray-500 dark:text-gray-400">{{ getFieldLabel(String(key)) }}:</span>
                                <span class="font-medium truncate max-w-[80px]">{{ value }}</span>
                            </span>
                        </div>
                    </template>

                    <template #attachments="data">
                        <span v-if="hasAttachments(data.value.attachments)" class="badge badge-outline-info">
                            {{ attachmentCount(data.value.attachments) }} {{ $t('submissions.files') }}
                        </span>
                        <span v-else class="text-gray-400 text-xs">—</span>
                    </template>

                    <template #created_at="data">
                        {{ formatDateTime(data.value.created_at) }}
                    </template>

                    <template #actions="data">
                        <div class="flex items-center gap-2">
                            <button @click="openDetail(data.value)" class="btn btn-sm btn-outline-primary" :title="$t('submissions.view')">
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                </svg>
                            </button>
                            <button @click="confirmDelete(data.value)" class="btn btn-sm btn-outline-danger" :title="$t('common.delete')">
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                </svg>
                            </button>
                        </div>
                    </template>
                </vue3-datatable>
            </div>
        </div>

        <!-- Submission Detail Modal -->
        <div v-if="showDetailModal && detailSubmission" class="fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
            <div class="panel max-w-2xl w-full mx-4 max-h-[90vh] flex flex-col">
                <!-- Header -->
                <div class="flex justify-between items-center p-6 border-b border-gray-200 dark:border-[#253a5b]">
                    <div>
                        <h3 class="text-lg font-semibold">{{ $t('submissions.detail_title') }}</h3>
                        <p class="text-sm text-gray-500 dark:text-gray-400 mt-0.5">
                            {{ $t('submissions.submitted_at') }}: {{ formatDateTime(detailSubmission.created_at) }}
                        </p>
                    </div>
                    <button @click="showDetailModal = false" class="text-gray-500 hover:text-gray-700 dark:hover:text-gray-300">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <!-- Scrollable body -->
                <div class="overflow-y-auto flex-1 p-6 space-y-4">
                    <!-- Order Number -->
                    <div class="flex items-center gap-3 p-3 bg-primary/5 border border-primary/20 rounded-lg">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-primary" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                        </svg>
                        <div>
                            <p class="text-xs text-gray-500 dark:text-gray-400">{{ $t('submissions.order_number') }}</p>
                            <p class="font-mono font-bold text-primary text-lg">{{ detailSubmission.order_number }}</p>
                        </div>
                    </div>

                    <!-- Form Fields -->
                    <div v-if="hasFormData(detailSubmission.form_data)">
                        <h4 class="font-semibold text-sm text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-3">
                            {{ $t('submissions.form_data') }}
                        </h4>
                        <div class="divide-y divide-gray-100 dark:divide-[#253a5b] border border-gray-200 dark:border-[#253a5b] rounded-lg overflow-hidden">
                            <div
                                v-for="(value, key) in detailSubmission.form_data"
                                :key="key"
                                class="flex items-start gap-3 px-4 py-3 bg-white dark:bg-[#1b2e4b]"
                            >
                                <span class="text-sm font-medium text-gray-600 dark:text-gray-300 min-w-[140px] pt-0.5">
                                    {{ getFieldLabel(String(key)) }}
                                </span>
                                <span class="text-sm text-gray-900 dark:text-gray-100 break-words flex-1">{{ value }}</span>
                            </div>
                        </div>
                    </div>

                    <!-- Attachments -->
                    <div v-if="hasAttachments(detailSubmission.attachments)">
                        <h4 class="font-semibold text-sm text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-3">
                            {{ $t('submissions.attachments') }}
                        </h4>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                            <div
                                v-for="(path, key) in detailSubmission.attachments"
                                :key="key"
                                class="border border-gray-200 dark:border-[#253a5b] rounded-lg overflow-hidden"
                            >
                                <!-- Image preview -->
                                <div v-if="isImage(String(path))" class="relative">
                                    <img
                                        :src="`/storage/${path}`"
                                        :alt="getFieldLabel(String(key))"
                                        class="w-full h-40 object-cover"
                                    />
                                    <div class="absolute bottom-0 left-0 right-0 bg-black/50 px-3 py-1.5">
                                        <p class="text-xs text-white font-medium truncate">{{ getFieldLabel(String(key)) }}</p>
                                    </div>
                                </div>
                                <!-- Non-image file -->
                                <div v-else class="flex items-center gap-3 px-4 py-3 bg-white dark:bg-[#1b2e4b]">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="w-8 h-8 text-gray-400 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                    </svg>
                                    <div class="flex-1 min-w-0">
                                        <p class="text-xs font-medium text-gray-600 dark:text-gray-300 truncate">{{ getFieldLabel(String(key)) }}</p>
                                        <p class="text-xs text-gray-400 truncate">{{ getFilename(String(path)) }}</p>
                                    </div>
                                </div>
                                <!-- Download link -->
                                <a
                                    :href="`/storage/${path}`"
                                    target="_blank"
                                    class="flex items-center justify-center gap-1.5 px-3 py-2 text-xs text-primary hover:bg-primary/5 border-t border-gray-200 dark:border-[#253a5b] transition-colors"
                                >
                                    <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                                    </svg>
                                    {{ $t('submissions.download') }}
                                </a>
                            </div>
                        </div>
                    </div>

                    <!-- IP Address -->
                    <div v-if="detailSubmission.ip_address" class="text-xs text-gray-400 pt-2">
                        {{ $t('submissions.ip_address') }}: {{ detailSubmission.ip_address }}
                    </div>
                </div>

                <div class="flex justify-end p-6 border-t border-gray-200 dark:border-[#253a5b]">
                    <button @click="showDetailModal = false" class="btn btn-outline-secondary">{{ $t('common.cancel') }}</button>
                </div>
            </div>
        </div>

        <!-- Delete Confirmation Modal -->
        <div v-if="showDeleteModal" class="fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center">
            <div class="panel p-6 max-w-md w-full mx-4">
                <h3 class="text-lg font-semibold mb-4">{{ $t('common.confirm_delete') }}</h3>
                <p class="text-gray-600 dark:text-gray-400 mb-6">
                    {{ $t('submissions.delete_confirmation', { order: submissionToDelete?.order_number }) }}
                </p>
                <div class="flex justify-end gap-3">
                    <button type="button" class="btn btn-outline-secondary" @click="showDeleteModal = false">{{ $t('common.cancel') }}</button>
                    <button type="button" class="btn btn-danger" @click="deleteSubmission" :disabled="deleting">
                        {{ deleting ? $t('common.deleting') : $t('common.delete') }}
                    </button>
                </div>
            </div>
        </div>

        <!-- Bulk Delete Modal -->
        <div v-if="showBulkDeleteModal" class="fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center">
            <div class="panel p-6 max-w-md w-full mx-4">
                <h3 class="text-lg font-semibold mb-4">{{ $t('common.confirm_bulk_delete') }}</h3>
                <p class="text-gray-600 dark:text-gray-400 mb-6">
                    {{ $t('submissions.bulk_delete_confirmation', { count: selectedIds.length }) }}
                </p>
                <div class="flex justify-end gap-3">
                    <button type="button" class="btn btn-outline-secondary" @click="showBulkDeleteModal = false">{{ $t('common.cancel') }}</button>
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
    form_data: Record<string, string> | null;
    attachments: Record<string, string> | null;
    ip_address: string | null;
    created_at: string;
}

interface SubmissionFilters {
    order_number: string;
    search: string;
    [key: string]: string;
}

interface Props {
    initialSubmissions: GridResponse<Submission>;
    formFields: FormField[];
}

const props = defineProps<Props>();
const i18n = useI18n();

useMeta({ title: i18n.t('submissions.title') });

// Build field label map: name → label
const fieldLabelMap = computed<Record<string, string>>(() => {
    const map: Record<string, string> = {};
    props.formFields.forEach(f => { map[f.name] = f.label; });
    return map;
});

const getFieldLabel = (key: string) => fieldLabelMap.value[key] ?? key;

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
} = useDataGrid<Submission, SubmissionFilters>({
    columns: [
        { field: 'checkbox',          title: '',                                       sort: false },
        { field: 'id',                title: i18n.t('ID'),                             sort: true  },
        { field: 'order_number',      title: i18n.t('submissions.order_number'),       sort: true  },
        { field: 'form_data_preview', title: i18n.t('submissions.form_data_preview'),  sort: false },
        { field: 'attachments',       title: i18n.t('submissions.attachments'),        sort: false },
        { field: 'created_at',        title: i18n.t('Created At'),                     sort: true  },
        { field: 'actions',           title: i18n.t('Actions'),                        sort: false },
    ],
    fetchUrl: route('merchant.submissions.list'),
    storageKey: 'merchant_submissions',
    initialData: props.initialSubmissions,
    defaultSort: { column: 'created_at', direction: 'desc' },
    initialFilters: { order_number: '', search: '' },
});

// Detail modal
const showDetailModal = ref(false);
const detailSubmission = ref<Submission | null>(null);

const openDetail = (submission: Submission) => {
    detailSubmission.value = submission;
    showDetailModal.value = true;
};

// Delete
const showDeleteModal = ref(false);
const submissionToDelete = ref<Submission | null>(null);
const deleting = ref(false);

const confirmDelete = (submission: Submission) => {
    submissionToDelete.value = submission;
    showDeleteModal.value = true;
};

const deleteSubmission = async () => {
    if (!submissionToDelete.value) return;
    deleting.value = true;
    try {
        await axios.delete(route('merchant.submissions.destroy', submissionToDelete.value.id));
        showDeleteModal.value = false;
        submissionToDelete.value = null;
        fetchData();
    } catch (error) {
        console.error('Failed to delete submission:', error);
    } finally {
        deleting.value = false;
    }
};

// Bulk
const selectedIds = ref<number[]>([]);
const showBulkDeleteModal = ref(false);
const bulkProcessing = ref(false);

const isAllSelected = computed(() =>
    rows.value.length > 0 && rows.value.every((r: Submission) => selectedIds.value.includes(r.id))
);

const toggleSelectAll = () => {
    if (isAllSelected.value) {
        const ids = rows.value.map((r: Submission) => r.id);
        selectedIds.value = selectedIds.value.filter(id => !ids.includes(id));
    } else {
        const ids = rows.value.map((r: Submission) => r.id);
        const next = [...selectedIds.value];
        ids.forEach(id => { if (!next.includes(id)) next.push(id); });
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

const applyBulkDelete = async () => {
    if (!selectedIds.value.length) return;
    bulkProcessing.value = true;
    try {
        await axios.post(route('merchant.submissions.bulk-destroy'), { ids: selectedIds.value });
        showBulkDeleteModal.value = false;
        clearSelection();
        fetchData();
    } catch (error) {
        console.error('Failed to bulk delete:', error);
    } finally {
        bulkProcessing.value = false;
    }
};

// Helpers
const hasFormData = (data: Record<string, string> | null) =>
    !!data && Object.keys(data).length > 0;

const getPreviewFields = (data: Record<string, string> | null) => {
    if (!data) return {};
    const entries = Object.entries(data).slice(0, 3);
    return Object.fromEntries(entries);
};

const hasAttachments = (attachments: Record<string, string> | null) =>
    !!attachments && Object.keys(attachments).length > 0;

const attachmentCount = (attachments: Record<string, string> | null) =>
    attachments ? Object.keys(attachments).length : 0;

const isImage = (path: string) => /\.(jpg|jpeg|png|gif|webp|svg)$/i.test(path);

const getFilename = (path: string) => path.split('/').pop() ?? path;

const formatDateTime = (dateString: string | null) => {
    if (!dateString) return '—';
    return new Date(dateString).toLocaleString('en-US', {
        year: 'numeric', month: 'short', day: 'numeric',
        hour: '2-digit', minute: '2-digit',
    });
};
</script>

<script lang="ts">
import appLayout from '@/layouts/app-layout.vue';
export default { layout: appLayout };
</script>

<style>
.grid-checkbox {
    width: 18px; height: 18px; cursor: pointer;
    border: 2px solid #6b7280; border-radius: 4px;
    background-color: #fff; appearance: none; -webkit-appearance: none; position: relative;
}
.grid-checkbox:checked { background-color: #4361ee; border-color: #4361ee; }
.grid-checkbox:checked::after {
    content: ''; position: absolute; left: 5px; top: 2px;
    width: 5px; height: 9px; border: solid white; border-width: 0 2px 2px 0; transform: rotate(45deg);
}
.dark .grid-checkbox { background-color: #1b2e4b; border-color: #888ea8; }
.dark .grid-checkbox:checked { background-color: #4361ee; border-color: #4361ee; }
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
