<template>
    <div>
        <div class="panel pb-0 mt-6">
            <div class="flex justify-between items-center mb-5">
                <h5 class="font-semibold text-lg dark:text-white-light">{{ $t('users.title') }}</h5>
                <button @click="openCreateModal" class="btn btn-primary">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 ltr:mr-2 rtl:ml-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    {{ $t('users.add') }}
                </button>
            </div>

            <!-- Filters Panel -->
            <div class="mb-5 p-4 border border-gray-200 dark:border-[#253a5b] rounded-lg shadow-sm">
                <h6 class="font-semibold text-md mb-3">{{ $t('filters') }}</h6>

                <div class="flex flex-wrap gap-4 mb-4">
                    <!-- Name -->
                    <div class="flex-1 min-w-[200px]">
                        <label for="name" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                            {{ $t('users.name') }}
                        </label>
                        <input
                            type="text"
                            id="name"
                            v-model="filterData.name"
                            class="form-input w-full"
                            :placeholder="$t('users.name')"
                        >
                    </div>

                    <!-- Email -->
                    <div class="flex-1 min-w-[200px]">
                        <label for="email" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                            {{ $t('users.email') }}
                        </label>
                        <input
                            type="text"
                            id="email"
                            v-model="filterData.email"
                            class="form-input w-full"
                            :placeholder="$t('users.email')"
                        >
                    </div>

                    <!-- Role Dropdown -->
                    <div class="flex-1 min-w-[200px]">
                        <label for="role" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                            {{ $t('users.role') }}
                        </label>
                        <select id="role" v-model="filterData.role" class="form-select w-full">
                            <option value="">{{ $t('users.all_roles') }}</option>
                            <option v-for="role in userRoles" :key="role.id" :value="role.id">
                                {{ role.label }}
                            </option>
                        </select>
                    </div>

                    <!-- Status Dropdown -->
                    <div class="flex-1 min-w-[200px]">
                        <label for="status" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                            {{ $t('status.title') }}
                        </label>
                        <select id="status" v-model="filterData.status" class="form-select w-full">
                            <option value="">{{ $t('status.all_statuses') }}</option>
                            <option v-for="status in userStatuses" :key="status.id" :value="status.id">
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
                            :placeholder="$t('users.search_placeholder')"
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
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 ltr:mr-1 rtl:ml-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                        </svg>
                        {{ $t('common.change_status') }}
                    </button>
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
                    <button
                        @click="toggleColumnChooser"
                        class="btn btn-outline-secondary p-2"
                        :title="$t('choose_columns')"
                    >
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 6.75V5.25A2.25 2.25 0 0111.25 3h5.25A2.25 2.25 0 0118.75 5.25v13.5A2.25 2.25 0 0116.5 21H11.25A2.25 2.25 0 019 18.75v-1.5M15 12H3m0 0l3-3m-3 3l3 3" />
                        </svg>
                    </button>

                    <!-- Column Dropdown -->
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
                            <input
                                type="checkbox"
                                class="grid-checkbox"
                                :checked="isAllSelected"
                                @change="toggleSelectAll"
                            />
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

                    <template #role="data">
                        <span
                            class="badge"
                            :class="getRoleBadgeClass(data.value.role)"
                        >
                            {{ getRoleLabel(data.value.role) }}
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
                                    v-for="status in userStatuses"
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
                                fill="none"
                                stroke="currentColor"
                                viewBox="0 0 24 24"
                            >
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                            </svg>
                            <svg
                                v-else
                                class="absolute right-1 top-1/2 -translate-y-1/2 w-3 h-3 animate-spin pointer-events-none"
                                fill="none"
                                viewBox="0 0 24 24"
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
                                @click="openEditModal(data.value)"
                                class="btn btn-sm btn-outline-primary"
                                :title="$t('common.edit')"
                            >
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                </svg>
                            </button>
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

        <!-- Create User Modal -->
        <div v-if="showCreateModal" class="fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center">
            <div class="panel p-6 max-w-lg w-full mx-4">
                <div class="flex justify-between items-center mb-5">
                    <h3 class="text-lg font-semibold">{{ $t('users.add_title') }}</h3>
                    <button @click="closeCreateModal" class="text-gray-500 hover:text-gray-700 dark:hover:text-gray-300">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
                <form @submit.prevent="createUser" class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">{{ $t('users.name') }} *</label>
                        <input v-model="createForm.name" type="text" class="form-input w-full" required />
                        <p v-if="createErrors.name" class="text-danger text-xs mt-1">{{ createErrors.name }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">{{ $t('users.email') }} *</label>
                        <input v-model="createForm.email" type="email" class="form-input w-full" required />
                        <p v-if="createErrors.email" class="text-danger text-xs mt-1">{{ createErrors.email }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">{{ $t('users.password') }} *</label>
                        <input v-model="createForm.password" type="password" class="form-input w-full" required />
                        <p v-if="createErrors.password" class="text-danger text-xs mt-1">{{ createErrors.password }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">{{ $t('users.role') }} *</label>
                        <select v-model="createForm.role" class="form-select w-full" required>
                            <option value="">{{ $t('users.select_role') }}</option>
                            <option v-for="role in userRoles" :key="role.id" :value="role.id">{{ role.label }}</option>
                        </select>
                        <p v-if="createErrors.role" class="text-danger text-xs mt-1">{{ createErrors.role }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">{{ $t('status.title') }} *</label>
                        <select v-model="createForm.status" class="form-select w-full" required>
                            <option value="">{{ $t('status.all_statuses') }}</option>
                            <option v-for="status in userStatuses" :key="status.id" :value="status.id">{{ status.label }}</option>
                        </select>
                        <p v-if="createErrors.status" class="text-danger text-xs mt-1">{{ createErrors.status }}</p>
                    </div>
                    <div class="flex justify-end gap-3 pt-2">
                        <button type="button" class="btn btn-outline-secondary" @click="closeCreateModal">
                            {{ $t('common.cancel') }}
                        </button>
                        <button type="submit" class="btn btn-primary" :disabled="creating">
                            {{ creating ? $t('common.saving') : $t('common.save') }}
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Edit User Modal -->
        <div v-if="showEditModal" class="fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center">
            <div class="panel p-6 max-w-lg w-full mx-4">
                <div class="flex justify-between items-center mb-5">
                    <h3 class="text-lg font-semibold">{{ $t('users.edit_title') }}</h3>
                    <button @click="closeEditModal" class="text-gray-500 hover:text-gray-700 dark:hover:text-gray-300">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
                <form @submit.prevent="updateUser" class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">{{ $t('users.name') }} *</label>
                        <input v-model="editForm.name" type="text" class="form-input w-full" required />
                        <p v-if="editErrors.name" class="text-danger text-xs mt-1">{{ editErrors.name }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">{{ $t('users.email') }} *</label>
                        <input v-model="editForm.email" type="email" class="form-input w-full" required />
                        <p v-if="editErrors.email" class="text-danger text-xs mt-1">{{ editErrors.email }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">{{ $t('users.role') }} *</label>
                        <select v-model="editForm.role" class="form-select w-full" required>
                            <option value="">{{ $t('users.select_role') }}</option>
                            <option v-for="role in userRoles" :key="role.id" :value="role.id">{{ role.label }}</option>
                        </select>
                        <p v-if="editErrors.role" class="text-danger text-xs mt-1">{{ editErrors.role }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">{{ $t('status.title') }} *</label>
                        <select v-model="editForm.status" class="form-select w-full" required>
                            <option value="">{{ $t('status.all_statuses') }}</option>
                            <option v-for="status in userStatuses" :key="status.id" :value="status.id">{{ status.label }}</option>
                        </select>
                        <p v-if="editErrors.status" class="text-danger text-xs mt-1">{{ editErrors.status }}</p>
                    </div>
                    <div class="flex justify-end gap-3 pt-2">
                        <button type="button" class="btn btn-outline-secondary" @click="closeEditModal">
                            {{ $t('common.cancel') }}
                        </button>
                        <button type="submit" class="btn btn-primary" :disabled="updating">
                            {{ updating ? $t('common.saving') : $t('common.save') }}
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Delete Confirmation Modal -->
        <div v-if="showDeleteModal" class="fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center">
            <div class="panel p-6 max-w-md w-full mx-4">
                <h3 class="text-lg font-semibold mb-4">{{ $t('common.confirm_delete') }}</h3>
                <p class="text-gray-600 dark:text-gray-400 mb-6">
                    {{ $t('users.delete_confirmation', { name: userToDelete?.name }) }}
                </p>
                <div class="flex justify-end gap-3">
                    <button type="button" class="btn btn-outline-secondary" @click="showDeleteModal = false">
                        {{ $t('common.cancel') }}
                    </button>
                    <button type="button" class="btn btn-danger" @click="deleteUser" :disabled="deleting">
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
                    {{ $t('users.status_change_confirmation', {
                        name: pendingStatusChange?.user?.name,
                        from: getStatusLabel(pendingStatusChange?.user?.status || ''),
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
                    {{ $t('users.bulk_status_change_confirmation', { count: selectedIds.length }) }}
                </p>
                <div class="mb-6">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        {{ $t('common.select_status') }}
                    </label>
                    <select v-model="bulkNewStatus" class="form-select w-full">
                        <option value="">{{ $t('common.select_status') }}</option>
                        <option v-for="status in userStatuses" :key="status.id" :value="status.id">
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
                    {{ $t('users.bulk_delete_confirmation', { count: selectedIds.length }) }}
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

interface RoleOption {
    id: string;
    label: string;
}

interface StatusOption {
    id: string;
    label: string;
}

interface User {
    id: number;
    name: string;
    email: string;
    role: string;
    status: string;
    created_at: string;
}

interface UserFilters {
    name: string;
    email: string;
    role: string;
    status: string;
    search: string;
    [key: string]: string;
}

interface Props {
    userStatuses: StatusOption[];
    userRoles: RoleOption[];
    initialUsers: GridResponse<User>;
}

const props = defineProps<Props>();

const i18n = useI18n();

useMeta({ title: i18n.t('users.title') });

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
} = useDataGrid<User, UserFilters>({
    columns: [
        { field: 'checkbox', title: '', sort: false },
        { field: 'id', title: i18n.t('ID'), sort: true },
        { field: 'name', title: i18n.t('users.name'), sort: true },
        { field: 'email', title: i18n.t('users.email'), sort: true },
        { field: 'role', title: i18n.t('users.role'), sort: true },
        { field: 'status', title: i18n.t('Status'), sort: true },
        { field: 'created_at', title: i18n.t('Created At'), sort: true },
        { field: 'actions', title: i18n.t('Actions'), sort: false },
    ],
    fetchUrl: route('admin.users.list'),
    storageKey: 'admin_users',
    initialData: props.initialUsers,
    defaultSort: { column: 'created_at', direction: 'desc' },
    initialFilters: {
        name: '',
        email: '',
        role: '',
        status: '',
        search: '',
    },
});

// --- Delete ---
const showDeleteModal = ref(false);
const userToDelete = ref<User | null>(null);
const deleting = ref(false);

// --- Status change ---
const updatingStatus = ref<number | null>(null);
const showStatusModal = ref(false);
const pendingStatusChange = ref<{ user: User; newStatus: string } | null>(null);
const statusSelectRefs = ref<Record<number, HTMLSelectElement | null>>({});

// --- Bulk actions ---
const selectedIds = ref<number[]>([]);
const showBulkStatusModal = ref(false);
const showBulkDeleteModal = ref(false);
const bulkNewStatus = ref('');
const bulkProcessing = ref(false);

// --- Create user ---
const showCreateModal = ref(false);
const creating = ref(false);
const createForm = ref({ name: '', email: '', password: '', role: '', status: 'active' });
const createErrors = ref<Record<string, string>>({});

// --- Edit user ---
const showEditModal = ref(false);
const updating = ref(false);
const editingUser = ref<User | null>(null);
const editForm = ref({ name: '', email: '', role: '', status: '' });
const editErrors = ref<Record<string, string>>({});

const isAllSelected = computed(() => {
    return rows.value.length > 0 && rows.value.every((row: User) => selectedIds.value.includes(row.id));
});

const toggleSelectAll = () => {
    if (isAllSelected.value) {
        const rowIds = rows.value.map((row: User) => row.id);
        selectedIds.value = selectedIds.value.filter(id => !rowIds.includes(id));
    } else {
        const rowIds = rows.value.map((row: User) => row.id);
        const newSelectedIds = [...selectedIds.value];
        rowIds.forEach(id => {
            if (!newSelectedIds.includes(id)) newSelectedIds.push(id);
        });
        selectedIds.value = newSelectedIds;
    }
};

const toggleSelection = (id: number | undefined) => {
    if (id === undefined) return;
    const index = selectedIds.value.indexOf(id);
    if (index === -1) selectedIds.value.push(id);
    else selectedIds.value.splice(index, 1);
};

const clearSelection = () => { selectedIds.value = []; };

// Create user
const openCreateModal = () => {
    createForm.value = { name: '', email: '', password: '', role: '', status: 'active' };
    createErrors.value = {};
    showCreateModal.value = true;
};

const closeCreateModal = () => { showCreateModal.value = false; };

const createUser = async () => {
    creating.value = true;
    createErrors.value = {};
    try {
        await axios.post(route('admin.users.store'), createForm.value);
        closeCreateModal();
        fetchData();
    } catch (error: any) {
        if (error.response?.status === 422) {
            const errs = error.response.data.errors;
            Object.keys(errs).forEach(k => { createErrors.value[k] = errs[k][0]; });
        }
    } finally {
        creating.value = false;
    }
};

// Edit user
const openEditModal = (user: User) => {
    editingUser.value = user;
    editForm.value = { name: user.name, email: user.email, role: user.role, status: user.status };
    editErrors.value = {};
    showEditModal.value = true;
};

const closeEditModal = () => { showEditModal.value = false; editingUser.value = null; };

const updateUser = async () => {
    if (!editingUser.value) return;
    updating.value = true;
    editErrors.value = {};
    try {
        const response = await axios.put(route('admin.users.update', editingUser.value.id), editForm.value);
        if (response.data.success) {
            const index = rows.value.findIndex((r: User) => r.id === editingUser.value!.id);
            if (index !== -1) rows.value[index] = response.data.user;
        }
        closeEditModal();
    } catch (error: any) {
        if (error.response?.status === 422) {
            const errs = error.response.data.errors;
            Object.keys(errs).forEach(k => { editErrors.value[k] = errs[k][0]; });
        }
    } finally {
        updating.value = false;
    }
};

// Bulk actions
const applyBulkStatusChange = async () => {
    if (!bulkNewStatus.value || selectedIds.value.length === 0) return;
    bulkProcessing.value = true;
    try {
        await axios.post(route('admin.users.bulk-update-status'), {
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

const applyBulkDelete = async () => {
    if (selectedIds.value.length === 0) return;
    bulkProcessing.value = true;
    try {
        await axios.post(route('admin.users.bulk-destroy'), { ids: selectedIds.value });
        showBulkDeleteModal.value = false;
        clearSelection();
        fetchData();
    } catch (error) {
        console.error('Failed to bulk delete:', error);
    } finally {
        bulkProcessing.value = false;
    }
};

// Status select refs
const setStatusSelectRef = (userId: number, el: HTMLSelectElement | null) => {
    statusSelectRefs.value[userId] = el;
};

const confirmStatusChange = (user: User, newStatus: string) => {
    if (user.status === newStatus) return;
    pendingStatusChange.value = { user, newStatus };
    showStatusModal.value = true;
};

const cancelStatusChange = () => {
    if (pendingStatusChange.value) {
        const selectEl = statusSelectRefs.value[pendingStatusChange.value.user.id];
        if (selectEl) selectEl.value = pendingStatusChange.value.user.status;
    }
    pendingStatusChange.value = null;
    showStatusModal.value = false;
};

const applyStatusChange = async () => {
    if (!pendingStatusChange.value) return;
    const { user, newStatus } = pendingStatusChange.value;
    updatingStatus.value = user.id;
    try {
        const response = await axios.patch(route('admin.users.update-status', user.id), { status: newStatus });
        if (response.data.success) {
            const index = rows.value.findIndex((r: User) => r.id === user.id);
            if (index !== -1) rows.value[index] = response.data.user;
        }
    } catch (error) {
        console.error('Failed to update status:', error);
        const selectEl = statusSelectRefs.value[user.id];
        if (selectEl) selectEl.value = user.status;
    } finally {
        updatingStatus.value = null;
        pendingStatusChange.value = null;
        showStatusModal.value = false;
    }
};

// Delete
const confirmDelete = (user: User) => {
    userToDelete.value = user;
    showDeleteModal.value = true;
};

const deleteUser = async () => {
    if (!userToDelete.value) return;
    deleting.value = true;
    try {
        await axios.delete(route('admin.users.destroy', userToDelete.value.id));
        showDeleteModal.value = false;
        userToDelete.value = null;
        fetchData();
    } catch (error) {
        console.error('Failed to delete user:', error);
    } finally {
        deleting.value = false;
    }
};

// Helpers
const getStatusSelectClass = (status: string): string => {
    const classes: Record<string, string> = {
        active: 'bg-success/20 text-success dark:text-green-300',
        inactive: 'bg-warning/20 text-warning dark:text-yellow-300',
        suspended: 'bg-danger/20 text-danger dark:text-red-300',
    };
    return classes[status] || 'bg-secondary/20 text-secondary dark:text-gray-300';
};

const getStatusLabel = (status: string): string => {
    return props.userStatuses.find(s => s.id === status)?.label || status;
};

const getRoleBadgeClass = (role: string): string => {
    return role === 'admin'
        ? 'badge-outline-primary'
        : 'badge-outline-success';
};

const getRoleLabel = (role: string): string => {
    return props.userRoles.find(r => r.id === role)?.label || role;
};

const formatDateTime = (dateString: string | null): string => {
    if (!dateString) return '-';
    return new Date(dateString).toLocaleString('en-US', {
        year: 'numeric',
        month: 'short',
        day: 'numeric',
        hour: '2-digit',
        minute: '2-digit',
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
