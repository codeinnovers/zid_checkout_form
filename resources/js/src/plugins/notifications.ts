import type { App, InjectionKey } from 'vue';
import { useNotifications, type Notifications } from '@/composables/use-notifications';

/**
 * Injection key for typed `inject()` usage in Composition API components:
 *
 *   import { inject } from 'vue';
 *   import { notificationsKey } from '@/plugins/notifications';
 *   const notify = inject(notificationsKey)!;
 *
 * Or, import and call the composable directly (simpler):
 *
 *   import { useNotifications } from '@/composables/use-notifications';
 *   const { success, confirm } = useNotifications();
 */
export const notificationsKey: InjectionKey<Notifications> = Symbol('notifications');

export default {
    install(app: App) {
        const notifications = useNotifications();
        app.provide(notificationsKey, notifications);
    },
};
