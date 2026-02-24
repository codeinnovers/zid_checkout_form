<template>
    <div>
        <div class="panel pb-0 mt-6">
            <div class="flex justify-between items-center mb-5">
                <h5 class="font-semibold text-lg dark:text-white-light">{{ $t('form_fields.title') }}</h5>
                <button type="button" @click="openAddPanel" class="btn btn-primary gap-2">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    {{ $t('form_fields.add') }}
                </button>
            </div>

            <!-- Filters Panel -->
            <div class="mb-5 p-4 border border-gray-200 dark:border-[#253a5b] rounded-lg shadow-sm">
                <h6 class="font-semibold text-md mb-3">{{ $t('filters') }}</h6>

                <div class="flex flex-wrap gap-4 mb-4">
                    <!-- Label -->
                    <div class="flex-1 min-w-[200px]">
                        <label for="label" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                            {{ $t('form_fields.label') }}
                        </label>
                        <input
                            type="text"
                            id="label"
                            v-model="filterData.label"
                            class="form-input w-full"
                            :placeholder="$t('form_fields.label')"
                        >
                    </div>

                    <!-- Field Type -->
                    <div class="flex-1 min-w-[200px]">
                        <label for="field_type" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                            {{ $t('form_fields.field_type') }}
                        </label>
                        <select id="field_type" v-model="filterData.field_type" class="form-select w-full">
                            <option value="">{{ $t('form_fields.all_types') }}</option>
                            <option v-for="(label, value) in fieldTypes" :key="value" :value="value">
                                {{ label }}
                            </option>
                        </select>
                    </div>

                    <!-- Status -->
                    <div class="flex-1 min-w-[200px]">
                        <label for="status" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                            {{ $t('status.title') }}
                        </label>
                        <select id="status" v-model="filterData.status" class="form-select w-full">
                            <option value="">{{ $t('status.all_statuses') }}</option>
                            <option v-for="status in fieldStatuses" :key="status.id" :value="status.id">
                                {{ status.label }}
                            </option>
                        </select>
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
                            :placeholder="$t('form_fields.search_placeholder')"
                        >
                    </div>
                </div>

                <!-- Filter Buttons -->
                <div class="flex justify-between items-center mt-5">
                    <div class="flex space-x-3">
                        <button @click="applyFilters" class="btn btn-primary">
                            {{ $t('filter') }}
                        </button>
                        <button @click="resetFilters" class="btn btn-danger">
                            {{ $t('reset') }}
                        </button>
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
                    <button @click="showBulkStatusModal = true" class="btn btn-sm btn-outline-primary">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                        </svg>
                        {{ $t('common.change_status') }}
                    </button>
                    <button @click="showBulkDeleteModal = true" class="btn btn-sm btn-outline-danger">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
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
                    <button
                        @click="toggleColumnChooser"
                        class="btn btn-outline-secondary p-2"
                        :title="$t('choose_columns')"
                    >
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
                                <input
                                    type="checkbox"
                                    :checked="isColumnVisible(col.field)"
                                    @change="toggleColumn(col.field)"
                                    class="form-checkbox"
                                >
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

                    <template #field_type="data">
                        <span class="badge bg-primary/20 text-primary font-mono text-xs px-2 py-1 rounded">
                            {{ fieldTypes[data.value.field_type] ?? data.value.field_type }}
                        </span>
                    </template>

                    <template #is_required="data">
                        <span
                            class="font-semibold text-xs"
                            :class="data.value.is_required ? 'text-success' : 'text-gray-400'"
                        >
                            {{ data.value.is_required ? $t('form_fields.yes') : $t('form_fields.no') }}
                        </span>
                    </template>

                    <template #status="data">
                        <div class="relative inline-block">
                            <select
                                :ref="(el) => setStatusSelectRef(data.value.id, el)"
                                @change="confirmStatusChange(data.value, ($event.target as HTMLSelectElement).value)"
                                class="appearance-none cursor-pointer border-0 bg-transparent pr-6 py-1 font-semibold text-xs rounded focus:ring-2 focus:ring-primary"
                                :class="getStatusSelectClass(data.value.status)"
                                :disabled="updatingStatus === data.value.id"
                            >
                                <option
                                    v-for="status in fieldStatuses"
                                    :key="status.id"
                                    :value="status.id"
                                    :selected="status.id === data.value.status"
                                >
                                    {{ status.label }}
                                </option>
                            </select>
                            <svg
                                v-if="updatingStatus !== data.value.id"
                                class="absolute right-1 top-1/2 -translate-y-1/2 w-3 h-3 pointer-events-none"
                                fill="none" stroke="currentColor" viewBox="0 0 24 24"
                            >
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                            </svg>
                            <svg
                                v-else
                                class="absolute right-1 top-1/2 -translate-y-1/2 w-3 h-3 animate-spin pointer-events-none"
                                fill="none" viewBox="0 0 24 24"
                            >
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                        </div>
                    </template>

                    <template #created_at="data">
                        {{ formatDateTime(data.value.created_at) }}
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
                    {{ $t('form_fields.delete_confirmation', { label: fieldToDelete?.label }) }}
                </p>
                <div class="flex justify-end gap-3">
                    <button type="button" class="btn btn-outline-secondary" @click="showDeleteModal = false">
                        {{ $t('common.cancel') }}
                    </button>
                    <button type="button" class="btn btn-danger" @click="deleteField" :disabled="deleting">
                        {{ deleting ? $t('common.deleting') : $t('common.delete') }}
                    </button>
                </div>
            </div>
        </div>

        <!-- Status Change Confirmation Modal -->
        <div v-if="showStatusModal" class="fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center">
            <div class="panel p-6 max-w-md w-full mx-4">
                <h3 class="text-lg font-semibold mb-4">{{ $t('common.confirm_status_change') }}</h3>
                <p class="text-gray-600 dark:text-gray-400 mb-6">
                    {{ $t('form_fields.status_change_confirmation', {
                        label: pendingStatusChange?.field?.label,
                        from: getStatusLabel(pendingStatusChange?.field?.status || ''),
                        to: getStatusLabel(pendingStatusChange?.newStatus || '')
                    }) }}
                </p>
                <div class="flex justify-end gap-3">
                    <button type="button" class="btn btn-outline-secondary" @click="cancelStatusChange">
                        {{ $t('common.cancel') }}
                    </button>
                    <button type="button" class="btn btn-primary" @click="applyStatusChange" :disabled="updatingStatus !== null">
                        {{ updatingStatus !== null ? $t('common.updating') : $t('common.confirm') }}
                    </button>
                </div>
            </div>
        </div>

        <!-- Bulk Status Change Modal -->
        <div v-if="showBulkStatusModal" class="fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center">
            <div class="panel p-6 max-w-md w-full mx-4">
                <h3 class="text-lg font-semibold mb-4">{{ $t('common.bulk_status_change') }}</h3>
                <p class="text-gray-600 dark:text-gray-400 mb-4">
                    {{ $t('form_fields.bulk_status_change_confirmation', { count: selectedIds.length }) }}
                </p>
                <div class="mb-6">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        {{ $t('common.select_status') }}
                    </label>
                    <select v-model="bulkNewStatus" class="form-select w-full">
                        <option value="">{{ $t('common.select_status') }}</option>
                        <option v-for="status in fieldStatuses" :key="status.id" :value="status.id">
                            {{ status.label }}
                        </option>
                    </select>
                </div>
                <div class="flex justify-end gap-3">
                    <button type="button" class="btn btn-outline-secondary" @click="showBulkStatusModal = false; bulkNewStatus = '';">
                        {{ $t('common.cancel') }}
                    </button>
                    <button type="button" class="btn btn-primary" @click="applyBulkStatusChange" :disabled="!bulkNewStatus || bulkProcessing">
                        {{ bulkProcessing ? $t('common.updating') : $t('common.confirm') }}
                    </button>
                </div>
            </div>
        </div>

        <!-- Bulk Delete Confirmation Modal -->
        <div v-if="showBulkDeleteModal" class="fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center">
            <div class="panel p-6 max-w-md w-full mx-4">
                <h3 class="text-lg font-semibold mb-4">{{ $t('common.confirm_bulk_delete') }}</h3>
                <p class="text-gray-600 dark:text-gray-400 mb-6">
                    {{ $t('form_fields.bulk_delete_confirmation', { count: selectedIds.length }) }}
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

        <!-- Add Field: Backdrop -->
        <Transition
            enter-active-class="transition-opacity duration-300 ease-out"
            enter-from-class="opacity-0"
            enter-to-class="opacity-100"
            leave-active-class="transition-opacity duration-200 ease-in"
            leave-from-class="opacity-100"
            leave-to-class="opacity-0"
        >
            <div v-if="showAddPanel" class="fixed inset-0 bg-black/50 z-40" @click="closePanel" />
        </Transition>

        <!-- Add Field: Slide-over Panel -->
        <Transition
            enter-active-class="transition-transform duration-300 ease-out"
            enter-from-class="translate-x-full"
            enter-to-class="translate-x-0"
            leave-active-class="transition-transform duration-200 ease-in"
            leave-from-class="translate-x-0"
            leave-to-class="translate-x-full"
        >
            <div v-if="showAddPanel" class="fixed top-0 ltr:right-0 rtl:left-0 h-full w-full sm:w-[620px] bg-white dark:bg-[#0e1726] shadow-2xl z-50 flex flex-col">

                <!-- Panel Header -->
                <div class="flex items-center justify-between px-6 py-4 border-b border-gray-200 dark:border-[#253a5b] shrink-0">
                    <h3 class="text-lg font-semibold dark:text-white-light">{{ $t('form_fields.add_title') }}</h3>
                    <button type="button" @click="closePanel" class="p-1.5 rounded-lg text-gray-500 hover:text-gray-700 dark:hover:text-gray-300 hover:bg-gray-100 dark:hover:bg-[#253a5b] transition-colors">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <!-- Panel Body -->
                <div class="flex-1 overflow-y-auto px-6 py-6 space-y-6">

                    <!-- Field Type Selector -->
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 dark:text-gray-200 mb-3">
                            {{ $t('form_fields.field_type') }} <span class="text-danger">*</span>
                        </label>
                        <div class="grid grid-cols-5 gap-2">
                            <button
                                v-for="(typeLabel, typeKey) in fieldTypes"
                                :key="typeKey"
                                type="button"
                                @click="form.field_type = typeKey"
                                class="flex flex-col items-center gap-1.5 py-3 px-2 rounded-xl border-2 text-xs font-medium transition-all"
                                :class="form.field_type === typeKey
                                    ? 'border-primary bg-primary/10 text-primary dark:text-primary'
                                    : 'border-gray-200 dark:border-[#253a5b] hover:border-primary/40 text-gray-500 dark:text-gray-400'"
                            >
                                <span class="text-base font-bold leading-none" v-html="fieldTypeSymbols[typeKey]"></span>
                                <span class="leading-tight text-center">{{ typeLabel }}</span>
                            </button>
                        </div>
                    </div>

                    <!-- Labels -->
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-wider text-gray-400 dark:text-gray-500 mb-3">{{ $t('form_fields.basic_info') }}</p>
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                    {{ $t('form_fields.label') }} <span class="text-danger">*</span>
                                </label>
                                <input
                                    type="text"
                                    v-model="form.label"
                                    class="form-input w-full"
                                    :class="{ 'border-danger focus:border-danger': getError('label') }"
                                    :placeholder="$t('form_fields.label')"
                                />
                                <p v-if="getError('label')" class="text-danger text-xs mt-1">{{ getError('label') }}</p>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                    {{ $t('form_fields.label_ar') }}
                                </label>
                                <input
                                    type="text"
                                    v-model="form.label_ar"
                                    class="form-input w-full text-right"
                                    dir="rtl"
                                    :placeholder="$t('form_fields.label_ar')"
                                />
                            </div>
                        </div>
                    </div>

                    <!-- Field Name / Key -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                            {{ $t('form_fields.name') }} <span class="text-danger">*</span>
                        </label>
                        <div class="relative">
                            <input
                                type="text"
                                v-model="form.name"
                                @input="onNameInput"
                                class="form-input w-full font-mono pr-9"
                                :class="{ 'border-danger focus:border-danger': getError('name') }"
                                :placeholder="$t('form_fields.name_placeholder')"
                            />
                            <button
                                v-if="nameManuallyEdited"
                                type="button"
                                @click="resetName"
                                :title="$t('form_fields.name_reset')"
                                class="absolute ltr:right-2.5 rtl:left-2.5 top-1/2 -translate-y-1/2 text-gray-400 hover:text-primary transition-colors"
                            >
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                                </svg>
                            </button>
                        </div>
                        <p class="text-gray-400 dark:text-gray-500 text-xs mt-1">{{ $t('form_fields.name_hint') }}</p>
                        <p v-if="getError('name')" class="text-danger text-xs mt-1">{{ getError('name') }}</p>
                    </div>

                    <!-- Placeholder (conditional) -->
                    <div v-if="showsPlaceholder">
                        <p class="text-xs font-semibold uppercase tracking-wider text-gray-400 dark:text-gray-500 mb-3">{{ $t('form_fields.display_settings') }}</p>
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                    {{ $t('form_fields.placeholder') }}
                                </label>
                                <input type="text" v-model="form.placeholder" class="form-input w-full" :placeholder="$t('form_fields.placeholder')" />
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                    {{ $t('form_fields.placeholder_ar') }}
                                </label>
                                <input type="text" v-model="form.placeholder_ar" class="form-input w-full text-right" dir="rtl" :placeholder="$t('form_fields.placeholder_ar')" />
                            </div>
                        </div>
                    </div>

                    <!-- Default Value (conditional) -->
                    <div v-if="showsDefaultValue">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                            {{ $t('form_fields.default_value') }}
                        </label>
                        <input type="text" v-model="form.default_value" class="form-input w-full" :placeholder="$t('form_fields.default_value')" />
                    </div>

                    <!-- Options (conditional) -->
                    <div v-if="showsOptions">
                        <div class="flex items-center justify-between mb-3">
                            <label class="text-sm font-medium text-gray-700 dark:text-gray-300">
                                {{ $t('form_fields.options') }} <span class="text-danger">*</span>
                            </label>
                            <button type="button" @click="addOption" class="btn btn-sm btn-outline-primary gap-1">
                                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                                </svg>
                                {{ $t('form_fields.add_option') }}
                            </button>
                        </div>
                        <div v-if="form.options.length === 0"
                            class="text-center py-5 text-sm text-gray-400 dark:text-gray-500 border-2 border-dashed border-gray-200 dark:border-[#253a5b] rounded-xl">
                            {{ $t('form_fields.no_options_yet') }}
                        </div>
                        <div v-else class="space-y-2">
                            <div class="grid grid-cols-[1fr_1fr_32px] gap-2 px-1">
                                <span class="text-xs font-medium text-gray-400">{{ $t('form_fields.option_label') }}</span>
                                <span class="text-xs font-medium text-gray-400">{{ $t('form_fields.option_value') }}</span>
                                <span></span>
                            </div>
                            <div
                                v-for="(option, index) in form.options"
                                :key="index"
                                class="grid grid-cols-[1fr_1fr_32px] gap-2 items-center"
                            >
                                <input
                                    type="text"
                                    v-model="option.label"
                                    @input="onOptionLabelInput(index)"
                                    class="form-input w-full text-sm"
                                    :placeholder="$t('form_fields.option_label')"
                                />
                                <input
                                    type="text"
                                    v-model="option.value"
                                    class="form-input w-full text-sm font-mono"
                                    :placeholder="$t('form_fields.option_value')"
                                />
                                <button
                                    type="button"
                                    @click="removeOption(index)"
                                    class="flex items-center justify-center w-8 h-8 rounded-lg text-gray-400 hover:text-danger hover:bg-danger/10 transition-colors"
                                >
                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                    </svg>
                                </button>
                            </div>
                        </div>
                        <p v-if="getError('options')" class="text-danger text-xs mt-1">{{ getError('options') }}</p>
                    </div>

                    <!-- Validation Rules (conditional) -->
                    <div v-if="showsValidation">
                        <p class="text-xs font-semibold uppercase tracking-wider text-gray-400 dark:text-gray-500 mb-3">{{ $t('form_fields.validation') }}</p>
                        <div class="grid grid-cols-2 gap-4">
                            <div v-if="showsLengthValidation">
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">{{ $t('form_fields.min_length') }}</label>
                                <input type="number" v-model="form.min_length" class="form-input w-full" min="0" placeholder="0" />
                            </div>
                            <div v-if="showsLengthValidation">
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">{{ $t('form_fields.max_length') }}</label>
                                <input type="number" v-model="form.max_length" class="form-input w-full" min="0" placeholder="∞" />
                            </div>
                            <div v-if="showsNumberValidation">
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">{{ $t('form_fields.min_value') }}</label>
                                <input type="number" v-model="form.min_value" class="form-input w-full" />
                            </div>
                            <div v-if="showsNumberValidation">
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">{{ $t('form_fields.max_value') }}</label>
                                <input type="number" v-model="form.max_value" class="form-input w-full" />
                            </div>
                        </div>
                    </div>

                    <!-- Settings Row -->
                    <div class="pt-4 border-t border-gray-200 dark:border-[#253a5b]">
                        <div class="grid grid-cols-3 gap-4">
                            <!-- Sort Order -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                    {{ $t('form_fields.sort_order') }}
                                </label>
                                <input type="number" v-model="form.sort_order" class="form-input w-full" min="0" />
                                <p v-if="getError('sort_order')" class="text-danger text-xs mt-1">{{ getError('sort_order') }}</p>
                            </div>

                            <!-- Is Required Toggle -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                    {{ $t('form_fields.is_required') }}
                                </label>
                                <div class="flex items-center gap-2 mt-1">
                                    <button
                                        type="button"
                                        @click="form.is_required = !form.is_required"
                                        class="relative inline-flex h-6 w-11 shrink-0 items-center rounded-full transition-colors focus:outline-none"
                                        :class="form.is_required ? 'bg-primary' : 'bg-gray-300 dark:bg-gray-600'"
                                    >
                                        <span
                                            class="inline-block h-4 w-4 transform rounded-full bg-white shadow transition-transform"
                                            :class="form.is_required ? 'translate-x-6' : 'translate-x-1'"
                                        />
                                    </button>
                                    <span class="text-sm text-gray-500 dark:text-gray-400">
                                        {{ form.is_required ? $t('form_fields.yes') : $t('form_fields.no') }}
                                    </span>
                                </div>
                            </div>

                            <!-- Status Toggle -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                    {{ $t('status.title') }}
                                </label>
                                <div class="flex items-center gap-2 mt-1">
                                    <button
                                        type="button"
                                        @click="form.status = form.status === 'active' ? 'inactive' : 'active'"
                                        class="relative inline-flex h-6 w-11 shrink-0 items-center rounded-full transition-colors focus:outline-none"
                                        :class="form.status === 'active' ? 'bg-success' : 'bg-gray-300 dark:bg-gray-600'"
                                    >
                                        <span
                                            class="inline-block h-4 w-4 transform rounded-full bg-white shadow transition-transform"
                                            :class="form.status === 'active' ? 'translate-x-6' : 'translate-x-1'"
                                        />
                                    </button>
                                    <span class="text-sm text-gray-500 dark:text-gray-400">
                                        {{ form.status === 'active' ? $t('status.active') : $t('status.inactive') }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>

                <!-- Panel Footer -->
                <div class="px-6 py-4 border-t border-gray-200 dark:border-[#253a5b] flex items-center justify-end gap-3 shrink-0 bg-gray-50 dark:bg-[#0e1726]">
                    <button type="button" @click="closePanel" class="btn btn-outline-secondary" :disabled="saving">
                        {{ $t('common.cancel') }}
                    </button>
                    <button type="button" @click="saveField" class="btn btn-primary min-w-[120px]" :disabled="saving">
                        <svg v-if="saving" class="w-4 h-4 ltr:mr-2 rtl:ml-2 animate-spin inline" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" />
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z" />
                        </svg>
                        {{ saving ? $t('common.saving') : $t('form_fields.save_field') }}
                    </button>
                </div>

            </div>
        </Transition>

    </div>
</template>

<script setup lang="ts">
import { ref, computed, reactive, watch } from 'vue';
import { useI18n } from 'vue-i18n';
import { useMeta } from '@/composables/use-meta';
import { useDataGrid, type GridResponse } from '@/composables/use-data-grid';
import { useNotifications } from '@/composables/use-notifications';
import Vue3Datatable from '@bhplugin/vue3-datatable';
import '@bhplugin/vue3-datatable/dist/style.css';
import { route } from 'ziggy-js';
import axios from 'axios';

interface StatusOption {
    id: string;
    label: string;
}

interface FormField {
    id: number;
    field_type: string;
    name: string;
    label: string;
    label_ar: string | null;
    is_required: boolean;
    sort_order: number;
    status: string;
    created_at: string;
}

interface FormFieldFilters {
    label: string;
    field_type: string;
    status: string;
    search: string;
    [key: string]: string;
}

interface Props {
    fieldStatuses: StatusOption[];
    initialFields: GridResponse<FormField>;
}

const props = defineProps<Props>();

const i18n = useI18n();

useMeta({ title: i18n.t('form_fields.title') });

const fieldTypes: Record<string, string> = {
    text:     i18n.t('form_fields.types.text'),
    email:    i18n.t('form_fields.types.email'),
    phone:    i18n.t('form_fields.types.phone'),
    number:   i18n.t('form_fields.types.number'),
    textarea: i18n.t('form_fields.types.textarea'),
    select:   i18n.t('form_fields.types.select'),
    radio:    i18n.t('form_fields.types.radio'),
    checkbox: i18n.t('form_fields.types.checkbox'),
    date:     i18n.t('form_fields.types.date'),
    file:     i18n.t('form_fields.types.file'),
};

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
} = useDataGrid<FormField, FormFieldFilters>({
    columns: [
        { field: 'checkbox',    title: '',                                    sort: false },
        { field: 'id',          title: i18n.t('ID'),                          sort: true },
        { field: 'label',       title: i18n.t('form_fields.label'),           sort: true },
        { field: 'field_type',  title: i18n.t('form_fields.field_type'),      sort: true },
        { field: 'name',        title: i18n.t('form_fields.name'),            sort: true },
        { field: 'is_required', title: i18n.t('form_fields.is_required'),     sort: false },
        { field: 'sort_order',  title: i18n.t('form_fields.sort_order'),      sort: true },
        { field: 'status',      title: i18n.t('Status'),                      sort: true },
        { field: 'created_at',  title: i18n.t('Created At'),                  sort: true },
        { field: 'actions',     title: i18n.t('Actions'),                     sort: false },
    ],
    fetchUrl:      route('merchant.form-fields.list'),
    storageKey:    'form_fields',
    initialData:   props.initialFields,
    defaultSort:   { column: 'sort_order', direction: 'asc' },
    initialFilters: {
        label:      '',
        field_type: '',
        status:     '',
        search:     '',
    },
});

// Single delete
const showDeleteModal     = ref(false);
const fieldToDelete       = ref<FormField | null>(null);
const deleting            = ref(false);

// Status change
const updatingStatus      = ref<number | null>(null);
const showStatusModal     = ref(false);
const pendingStatusChange = ref<{ field: FormField; newStatus: string } | null>(null);
const statusSelectRefs    = ref<Record<number, HTMLSelectElement | null>>({});

// Bulk actions
const selectedIds         = ref<number[]>([]);
const showBulkStatusModal = ref(false);
const showBulkDeleteModal = ref(false);
const bulkNewStatus       = ref('');
const bulkProcessing      = ref(false);

const isAllSelected = computed(() =>
    rows.value.length > 0 && rows.value.every((row: FormField) => selectedIds.value.includes(row.id))
);

const toggleSelectAll = () => {
    const rowIds = rows.value.map((row: FormField) => row.id);
    if (isAllSelected.value) {
        selectedIds.value = selectedIds.value.filter(id => !rowIds.includes(id));
    } else {
        rowIds.forEach(id => {
            if (!selectedIds.value.includes(id)) selectedIds.value.push(id);
        });
    }
};

const toggleSelection = (id: number | undefined) => {
    if (id === undefined) return;
    const index = selectedIds.value.indexOf(id);
    if (index === -1) selectedIds.value.push(id);
    else selectedIds.value.splice(index, 1);
};

const clearSelection = () => { selectedIds.value = []; };

const setStatusSelectRef = (fieldId: number, el: HTMLSelectElement | null) => {
    statusSelectRefs.value[fieldId] = el;
};

const confirmStatusChange = (field: FormField, newStatus: string) => {
    if (field.status === newStatus) return;
    pendingStatusChange.value = { field, newStatus };
    showStatusModal.value = true;
};

const cancelStatusChange = () => {
    if (pendingStatusChange.value) {
        const selectEl = statusSelectRefs.value[pendingStatusChange.value.field.id];
        if (selectEl) selectEl.value = pendingStatusChange.value.field.status;
    }
    pendingStatusChange.value = null;
    showStatusModal.value = false;
};

const applyStatusChange = async () => {
    if (!pendingStatusChange.value) return;

    const { field, newStatus } = pendingStatusChange.value;
    updatingStatus.value = field.id;

    try {
        const response = await axios.patch(route('merchant.form-fields.update-status', field.id), {
            status: newStatus,
        });

        if (response.data.success) {
            const index = rows.value.findIndex((r: FormField) => r.id === field.id);
            if (index !== -1) rows.value[index] = response.data.field;
        }
    } catch (error) {
        console.error('Failed to update status:', error);
        const selectEl = statusSelectRefs.value[field.id];
        if (selectEl) selectEl.value = field.status;
    } finally {
        updatingStatus.value = null;
        pendingStatusChange.value = null;
        showStatusModal.value = false;
    }
};

const applyBulkStatusChange = async () => {
    if (!bulkNewStatus.value || selectedIds.value.length === 0) return;

    bulkProcessing.value = true;
    try {
        await axios.post(route('merchant.form-fields.bulk-update-status'), {
            ids: selectedIds.value,
            status: bulkNewStatus.value,
        });
        showBulkStatusModal.value = false;
        bulkNewStatus.value = '';
        clearSelection();
        fetchData();
    } catch (error) {
        console.error('Failed to bulk update status:', error);
    } finally {
        bulkProcessing.value = false;
    }
};

const confirmDelete = (field: FormField) => {
    fieldToDelete.value = field;
    showDeleteModal.value = true;
};

const deleteField = async () => {
    if (!fieldToDelete.value) return;

    deleting.value = true;
    try {
        await axios.delete(route('merchant.form-fields.destroy', fieldToDelete.value.id));
        showDeleteModal.value = false;
        fieldToDelete.value = null;
        fetchData();
    } catch (error) {
        console.error('Failed to delete field:', error);
    } finally {
        deleting.value = false;
    }
};

const applyBulkDelete = async () => {
    if (selectedIds.value.length === 0) return;

    bulkProcessing.value = true;
    try {
        await axios.post(route('merchant.form-fields.bulk-destroy'), {
            ids: selectedIds.value,
        });
        showBulkDeleteModal.value = false;
        clearSelection();
        fetchData();
    } catch (error) {
        console.error('Failed to bulk delete:', error);
    } finally {
        bulkProcessing.value = false;
    }
};

const getStatusSelectClass = (status: string): string => {
    const classes: Record<string, string> = {
        active:   'bg-success/20 text-success dark:text-green-300',
        inactive: 'bg-warning/20 text-warning dark:text-yellow-300',
    };
    return classes[status] ?? 'bg-secondary/20 text-secondary dark:text-gray-300';
};

const getStatusLabel = (status: string): string =>
    props.fieldStatuses.find(s => s.id === status)?.label ?? status;

const formatDateTime = (dateString: string | null): string => {
    if (!dateString) return '-';
    return new Date(dateString).toLocaleString('en-US', {
        year: 'numeric', month: 'short', day: 'numeric',
        hour: '2-digit', minute: '2-digit',
    });
};

// ─── Add Field Panel ────────────────────────────────────────────────────────

interface FieldOption {
    label: string;
    value: string;
}

interface FieldForm {
    field_type:   string;
    label:        string;
    label_ar:     string;
    name:         string;
    placeholder:  string;
    placeholder_ar: string;
    default_value: string;
    options:      FieldOption[];
    min_length:   number | null;
    max_length:   number | null;
    min_value:    number | null;
    max_value:    number | null;
    sort_order:   number;
    is_required:  boolean;
    status:       string;
}

const fieldTypeSymbols: Record<string, string> = {
    text:     '<svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h8m-8 6h16"/></svg>',
    email:    '<svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>',
    phone:    '<svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>',
    number:   '<svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 20l4-16m2 16l4-16M6 9h14M4 15h14"/></svg>',
    textarea: '<svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h7"/></svg>',
    select:   '<svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 9l4-4 4 4m0 6l-4 4-4-4"/></svg>',
    radio:    '<svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><circle cx="12" cy="12" r="9" stroke-width="2"/><circle cx="12" cy="12" r="4" fill="currentColor"/></svg>',
    checkbox: '<svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>',
    date:     '<svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>',
    file:     '<svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"/></svg>',
};

const { success: notifySuccess } = useNotifications();

const showAddPanel      = ref(false);
const saving            = ref(false);
const formErrors        = ref<Record<string, string[]>>({});
const nameManuallyEdited = ref(false);

const defaultForm = (): FieldForm => ({
    field_type:     'text',
    label:          '',
    label_ar:       '',
    name:           '',
    placeholder:    '',
    placeholder_ar: '',
    default_value:  '',
    options:        [],
    min_length:     null,
    max_length:     null,
    min_value:      null,
    max_value:      null,
    sort_order:     0,
    is_required:    false,
    status:         'active',
});

const form = reactive<FieldForm>(defaultForm());

const showsPlaceholder      = computed(() => ['text', 'email', 'phone', 'number', 'textarea'].includes(form.field_type));
const showsDefaultValue     = computed(() => ['text', 'email', 'phone', 'number', 'textarea', 'date'].includes(form.field_type));
const showsOptions          = computed(() => ['select', 'radio', 'checkbox'].includes(form.field_type));
const showsLengthValidation = computed(() => ['text', 'email', 'phone', 'textarea'].includes(form.field_type));
const showsNumberValidation = computed(() => form.field_type === 'number');
const showsValidation       = computed(() => showsLengthValidation.value || showsNumberValidation.value);

const slugify = (str: string): string =>
    str.toLowerCase().replace(/\s+/g, '_').replace(/[^a-z0-9_]/g, '');

watch(() => form.label, (newLabel) => {
    if (!nameManuallyEdited.value) {
        form.name = slugify(newLabel);
    }
});

const openAddPanel = () => {
    Object.assign(form, defaultForm());
    formErrors.value = {};
    nameManuallyEdited.value = false;
    showAddPanel.value = true;
};

const closePanel = () => { showAddPanel.value = false; };

const onNameInput = () => { nameManuallyEdited.value = true; };

const resetName = () => {
    nameManuallyEdited.value = false;
    form.name = slugify(form.label);
};

const addOption = () => { form.options.push({ label: '', value: '' }); };

const removeOption = (index: number) => { form.options.splice(index, 1); };

const onOptionLabelInput = (index: number) => {
    const opt = form.options[index];
    if (opt) opt.value = slugify(opt.label);
};

const getError = (field: string): string | null =>
    formErrors.value[field]?.[0] ?? null;

const buildValidationRules = (): Record<string, number> | null => {
    const rules: Record<string, number> = {};
    if (showsLengthValidation.value) {
        if (form.min_length !== null && form.min_length !== undefined && String(form.min_length) !== '') rules.min_length = Number(form.min_length);
        if (form.max_length !== null && form.max_length !== undefined && String(form.max_length) !== '') rules.max_length = Number(form.max_length);
    }
    if (showsNumberValidation.value) {
        if (form.min_value !== null && form.min_value !== undefined && String(form.min_value) !== '') rules.min_value = Number(form.min_value);
        if (form.max_value !== null && form.max_value !== undefined && String(form.max_value) !== '') rules.max_value = Number(form.max_value);
    }
    return Object.keys(rules).length > 0 ? rules : null;
};

const saveField = async () => {
    formErrors.value = {};
    saving.value = true;

    try {
        const payload = {
            field_type:       form.field_type,
            label:            form.label,
            label_ar:         form.label_ar || null,
            name:             form.name,
            placeholder:      form.placeholder || null,
            placeholder_ar:   form.placeholder_ar || null,
            default_value:    form.default_value || null,
            options:          showsOptions.value ? form.options : null,
            validation_rules: buildValidationRules(),
            sort_order:       form.sort_order,
            is_required:      form.is_required,
            status:           form.status,
        };

        await axios.post(route('merchant.form-fields.store'), payload);
        notifySuccess(i18n.t('form_fields.saved'));
        closePanel();
        fetchData();
    } catch (err: any) {
        if (err.response?.status === 422) {
            formErrors.value = err.response.data.errors ?? {};
        }
    } finally {
        saving.value = false;
    }
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

.bh-table-hover tbody tr:hover {
    background-color: rgba(67, 97, 238, 0.1);
}

.dark .bh-table-hover tbody tr:hover {
    background-color: rgba(67, 97, 238, 0.2);
}

.datatable .bh-datatable {
    @apply bg-white dark:bg-[#1b2e4b] rounded-lg;
}

.datatable .bh-datatable th {
    @apply bg-gray-100 dark:bg-[#253a5b] text-gray-700 dark:text-white-light font-semibold;
}

.datatable .bh-datatable td {
    @apply border-b border-gray-200 dark:border-[#253a5b] dark:text-gray-200;
}

.datatable .bh-pagination {
    @apply flex items-center justify-between mt-4;
}

.datatable .bh-pagination button {
    @apply px-3 py-1 rounded border border-gray-300 dark:border-[#253a5b] hover:bg-primary hover:text-white transition-colors;
}

.datatable .bh-pagination button.active {
    @apply bg-primary text-white border-primary;
}

.datatable .bh-pagination button:disabled {
    @apply opacity-50 cursor-not-allowed hover:bg-transparent hover:text-current;
}
</style>
