# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Stack

Laravel 11 (PHP 8.2+) + Vue 3 + Inertia.js 2 + TypeScript + Tailwind CSS + Pinia + Vite.

## Commands

```bash
# Start full dev environment (Laravel + queue + log watcher + Vite HMR)
composer dev

# Or run independently:
php artisan serve
npm run dev

# Build frontend for production
npm run build

# Run all tests
php artisan test

# Run a single test file
php artisan test tests/Feature/ExampleTest.php

# Database
php artisan migrate
php artisan db:seed   # Creates admin@example.com (password: password) + 25 sample users
```

## Architecture

### Inertia.js Pattern
There is no separate API for the frontend. Laravel controllers return `Inertia::render('ComponentName', $props)`, which renders the corresponding Vue component at `resources/js/Pages/`. Data flows directly from controller to page component as props. Ziggy (`route()` helper) is available in Vue for generating Laravel route URLs.

Shared data (auth user, CSRF) is provided to all pages via `app/Http/Middleware/HandleInertiaRequests.php`.

### Routes
```
GET/POST /login          guest — LoginController
GET      /dashboard      auth  — DashboardController
GET/PUT  /profile        auth  — ProfileController (name/email, password)
GET      /admin/users             auth — UserController@index (initial page + data)
GET      /admin/users/list        auth — UserController@list (AJAX data grid)
PATCH    /admin/users/{id}/status auth — UserController@updateStatus
POST     /admin/users/bulk-status auth — UserController@bulkUpdateStatus
DELETE   /admin/users/{id}        auth — UserController@destroy
POST     /admin/users/bulk-delete auth — UserController@bulkDestroy
```

### Controllers
Controllers are organized into namespaced subdirectories under `app/Http/Controllers/`:
- `Auth/LoginController` — login/logout
- `DashboardController` — dashboard page
- `Profile/ProfileController` — profile and password update
- `Admin/Users/UserController` — full CRUD + bulk actions for user management

### Data Grid System
A custom reusable pattern handles server-side filtering, sorting, searching, and pagination:

**Backend** — The `HasDataGrid` trait (`app/Traits/HasDataGrid.php`) is added to Eloquent models. Models configure:
```php
protected array $gridFilterable = [
    'status' => 'exact',       // exact match
    'name'   => 'like',        // LIKE %value%
    // also supported: 'date_from', 'date_to', 'relationship'
];
protected array $gridSortable   = ['id', 'name', 'email', 'created_at'];
protected array $gridSearchable = ['name', 'email'];
```
Controllers call `Model::applyDataGrid($request)->paginateForGrid()` for AJAX list endpoints. Use `formatGridResponse()` to return a consistent JSON shape.

**Frontend** — The `use-data-grid.ts` composable (`resources/js/src/composables/use-data-grid.ts`) manages column visibility (persisted to localStorage with key prefix `datagrid_columns_`), pagination state, and fires POST requests to the list endpoint on filter/sort/page changes. Data tables use the third-party `vue3-datatable` component.

### Layouts
Two layouts live in `resources/js/src/layouts/`:
- `app-layout.vue` — authenticated pages; includes Sidebar, Header, Footer, screen loader, back-to-top, and ThemeCustomizer
- `auth-layout.vue` — unauthenticated pages (login, etc.)

### State Management (Pinia)
`useAppStore` (`resources/js/src/stores/`) handles global UI state: theme (light/dark/system), locale (16 languages including Arabic with RTL auto-switch), sidebar layout. Preferences persist to localStorage.

### Path Alias
`@` maps to `resources/js/src/`. Component imports use `@/components/...`, stores use `@/stores/...`, etc.

### Localization
Vue i18n with 16 locale JSON files in `resources/js/src/locales/`. Use `$t('key')` in templates. Arabic (`sa`) automatically enables RTL layout.
DO not add localization except for Arabic and English.

### Authentication
Session-based auth via Laravel Sanctum. Login/register are guest-only routes. Protected routes use the `auth` middleware. After login, users are redirected to `/dashboard`.

### UserStatusEnum
`app/Enums/UserStatusEnum.php` is a PHP 8.1 backed enum (`Active`, `Inactive`, `Suspended`) cast on the `User` model. Use it for status filters and validation. The enum provides:
- `label()` — human-readable string for display
- `options()` — array suitable for select/dropdown inputs

### Notification System (SweetAlert2)

**Composable** — `resources/js/src/composables/use-notifications.ts` wraps SweetAlert2. Import directly in any component:

```typescript
import { useNotifications } from '@/composables/use-notifications';
const { success, error, warning, info, toast, alert, confirm } = useNotifications();

// Toasts (non-blocking, auto-dismiss, top-right)
success('Saved successfully');
error('Something went wrong', 'Optional title');

// Confirm dialog — returns Promise<boolean>
const ok = await confirm({ title: 'Delete user?', type: 'warning', confirmBtnClass: 'btn-danger' });

// Alert dialog (one button)
await alert({ type: 'info', title: 'Notice', message: 'Read-only info' });
```

**Alert component** — `resources/js/src/components/Alert.vue` is a dismissable inline alert registered globally as `<Alert>`:

```vue
<Alert type="success" title="Saved!" message="Your changes were saved." />
<Alert type="danger" :dismissable="false">Custom <strong>slot</strong> content</Alert>
```

Types: `success | warning | danger | info | primary | secondary`. Props: `type`, `title`, `message`, `dismissable` (default `true`), `icon` (default `true`). Emits `dismissed` when closed.

Toast color classes (`color-success`, `color-danger`, etc.) and alert fade transition are defined in `tailwind.css`.

