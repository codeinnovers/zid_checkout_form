import { ref, reactive, computed, watch, onMounted, onUnmounted, type Ref, type ComputedRef } from 'vue';

export interface GridColumn {
    field: string;
    title: string;
    sort?: boolean;
}

export interface GridPagination {
    total: number;
    perPage: number;
    currentPage: number;
    lastPage: number;
}

export interface GridSorting {
    column: string;
    direction: 'asc' | 'desc';
}

export interface GridMeta {
    total: number;
    per_page: number;
    current_page: number;
    last_page: number;
}

export interface GridResponse<T> {
    data: T[];
    meta: GridMeta;
}

export interface UseDataGridOptions<T, F extends Record<string, unknown>> {
    columns: GridColumn[];
    fetchUrl: string;
    storageKey?: string;
    initialData?: GridResponse<T>;
    defaultSort?: GridSorting;
    initialFilters?: Partial<F>;
}

export interface UseDataGridReturn<T, F extends Record<string, unknown>> {
    rows: Ref<T[]>;
    loading: Ref<boolean>;
    pagination: GridPagination;
    sorting: GridSorting;
    filterData: F;
    allColumns: Ref<GridColumn[]>;
    visibleColumns: ComputedRef<GridColumn[]>;
    hiddenColumns: Ref<string[]>;
    showColumnDropdown: Ref<boolean>;
    columnDropdownRef: Ref<HTMLElement | null>;
    isUsingInitialData: Ref<boolean>;
    fetchData: () => Promise<void>;
    handleTableChange: (data: TableChangeEvent) => Promise<void>;
    applyFilters: () => void;
    resetFilters: () => void;
    toggleColumnChooser: () => void;
    closeColumnDropdown: () => void;
    isColumnVisible: (field: string) => boolean;
    toggleColumn: (field: string) => void;
    resetColumnVisibility: () => void;
}

interface TableChangeEvent {
    current_page?: number;
    pagesize?: number;
    sort_column?: string;
    sort_direction?: 'asc' | 'desc';
}

const STORAGE_PREFIX = 'datagrid_columns_';

/**
 * Load hidden columns from localStorage
 */
function loadHiddenColumns(storageKey: string | undefined): string[] {
    if (!storageKey) {
        return [];
    }

    try {
        const stored = localStorage.getItem(`${STORAGE_PREFIX}${storageKey}`);
        if (stored) {
            const parsed = JSON.parse(stored);
            if (Array.isArray(parsed)) {
                return parsed;
            }
        }
    } catch (e) {
        console.warn('Failed to load column preferences from localStorage:', e);
    }

    return [];
}

/**
 * Save hidden columns to localStorage
 */
function saveHiddenColumns(storageKey: string | undefined, hiddenColumns: string[]): void {
    if (!storageKey) {
        return;
    }

    try {
        localStorage.setItem(`${STORAGE_PREFIX}${storageKey}`, JSON.stringify(hiddenColumns));
    } catch (e) {
        console.warn('Failed to save column preferences to localStorage:', e);
    }
}

export function useDataGrid<T, F extends Record<string, unknown>>(
    options: UseDataGridOptions<T, F>
): UseDataGridReturn<T, F> {
    const {
        columns,
        fetchUrl,
        storageKey,
        initialData,
        defaultSort = { column: 'created_at', direction: 'desc' },
        initialFilters = {} as Partial<F>,
    } = options;

    const loading = ref(false);
    const rows = ref<T[]>(initialData?.data ?? []) as Ref<T[]>;
    const showColumnDropdown = ref(false);
    const columnDropdownRef = ref<HTMLElement | null>(null);
    const isUsingInitialData = ref(!!initialData);
    const allColumns = ref<GridColumn[]>(columns);

    // Load hidden columns from localStorage or use empty array
    const hiddenColumns = ref<string[]>(loadHiddenColumns(storageKey));

    // Watch for changes to hidden columns and persist to localStorage
    watch(
        hiddenColumns,
        (newValue) => {
            saveHiddenColumns(storageKey, newValue);
        },
        { deep: true }
    );

    const pagination = reactive<GridPagination>({
        total: initialData?.meta.total ?? 0,
        perPage: initialData?.meta.per_page ?? 10,
        currentPage: initialData?.meta.current_page ?? 1,
        lastPage: initialData?.meta.last_page ?? 1,
    });

    const sorting = reactive<GridSorting>({
        column: defaultSort.column,
        direction: defaultSort.direction,
    });

    const filterData = reactive({ ...initialFilters }) as F;

    const visibleColumns = computed(() => {
        return allColumns.value.filter(col => !hiddenColumns.value.includes(col.field));
    });

    const closeColumnDropdown = () => {
        showColumnDropdown.value = false;
    };

    const toggleColumnChooser = () => {
        showColumnDropdown.value = !showColumnDropdown.value;
    };

    // Handle click outside to close dropdown
    const handleClickOutside = (event: MouseEvent) => {
        if (
            showColumnDropdown.value &&
            columnDropdownRef.value &&
            !columnDropdownRef.value.contains(event.target as Node)
        ) {
            closeColumnDropdown();
        }
    };

    onMounted(() => {
        document.addEventListener('click', handleClickOutside);
    });

    onUnmounted(() => {
        document.removeEventListener('click', handleClickOutside);
    });

    const isColumnVisible = (field: string): boolean => {
        return !hiddenColumns.value.includes(field);
    };

    const toggleColumn = (field: string) => {
        const index = hiddenColumns.value.indexOf(field);
        if (index > -1) {
            hiddenColumns.value.splice(index, 1);
        } else {
            hiddenColumns.value.push(field);
        }
    };

    const resetColumnVisibility = (): void => {
        hiddenColumns.value = [];
    };

    const buildQueryParams = (): URLSearchParams => {
        const params = new URLSearchParams();
        params.append('current_page', pagination.currentPage.toString());
        params.append('per_page', pagination.perPage.toString());
        params.append('sort_column', sorting.column);
        params.append('sort_direction', sorting.direction);

        for (const [key, value] of Object.entries(filterData)) {
            if (value !== undefined && value !== null && value !== '') {
                params.append(key, String(value));
            }
        }

        return params;
    };

    const fetchData = async (): Promise<void> => {
        loading.value = true;

        try {
            const params = buildQueryParams();
            const response = await fetch(`${fetchUrl}?${params.toString()}`);
            const result: GridResponse<T> = await response.json();

            rows.value = result.data;
            pagination.total = result.meta.total;
            pagination.currentPage = result.meta.current_page;
            pagination.perPage = result.meta.per_page;
            pagination.lastPage = result.meta.last_page;
        } catch (error) {
            console.error('Error fetching data:', error);
        } finally {
            loading.value = false;
        }
    };

    const handleTableChange = async (data: TableChangeEvent): Promise<void> => {
        const newPage = data.current_page ?? 1;
        const newPageSize = data.pagesize ?? 10;
        const newSortColumn = data.sort_column ?? sorting.column;
        const newSortDirection = data.sort_direction ?? sorting.direction;

        const canUseInitialData = isUsingInitialData.value
            && newPage === 1
            && newPageSize === (initialData?.meta.per_page ?? 10)
            && newSortColumn === defaultSort.column
            && newSortDirection === defaultSort.direction;

        if (canUseInitialData) {
            return;
        }

        isUsingInitialData.value = false;

        pagination.currentPage = newPage;
        pagination.perPage = newPageSize;
        sorting.column = newSortColumn;
        sorting.direction = newSortDirection;

        await fetchData();
    };

    const applyFilters = (): void => {
        isUsingInitialData.value = false;
        pagination.currentPage = 1;
        fetchData();
    };

    const resetFilters = (): void => {
        for (const key of Object.keys(filterData)) {
            (filterData as Record<string, unknown>)[key] = (initialFilters as Record<string, unknown>)[key] ?? '';
        }
        pagination.currentPage = 1;
        fetchData();
    };

    return {
        rows,
        loading,
        pagination,
        sorting,
        filterData,
        allColumns,
        visibleColumns,
        hiddenColumns,
        showColumnDropdown,
        columnDropdownRef,
        isUsingInitialData,
        fetchData,
        handleTableChange,
        applyFilters,
        resetFilters,
        toggleColumnChooser,
        closeColumnDropdown,
        isColumnVisible,
        toggleColumn,
        resetColumnVisibility,
    };
}
