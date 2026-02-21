<script setup lang="ts">
import { ref, computed } from 'vue';

export type AlertType = 'success' | 'warning' | 'danger' | 'info' | 'primary' | 'secondary';

interface Props {
    type?: AlertType;
    title?: string;
    message?: string;
    dismissable?: boolean;
    icon?: boolean;
}

const props = withDefaults(defineProps<Props>(), {
    type: 'info',
    dismissable: true,
    icon: true,
});

const emit = defineEmits<{ dismissed: [] }>();

const visible = ref(true);

const dismiss = () => {
    visible.value = false;
    emit('dismissed');
};

const colorMap: Record<AlertType, string> = {
    success:   'bg-success-light   border-success/30   text-success   dark:bg-success-dark-light   dark:border-success/40',
    warning:   'bg-warning-light   border-warning/30   text-warning   dark:bg-warning-dark-light   dark:border-warning/40',
    danger:    'bg-danger-light    border-danger/30    text-danger    dark:bg-danger-dark-light    dark:border-danger/40',
    info:      'bg-info-light      border-info/30      text-info      dark:bg-info-dark-light      dark:border-info/40',
    primary:   'bg-primary-light   border-primary/30   text-primary   dark:bg-primary-dark-light   dark:border-primary/40',
    secondary: 'bg-secondary-light border-secondary/30 text-secondary dark:bg-secondary-dark-light dark:border-secondary/40',
};

const wrapperClass = computed(() => colorMap[props.type]);
</script>

<template>
    <transition name="alert-fade">
        <div v-if="visible" :class="['flex items-start gap-3 rounded-lg border p-4', wrapperClass]" role="alert">

            <!-- Type Icon -->
            <span v-if="icon" class="mt-0.5 shrink-0">
                <!-- Success -->
                <svg v-if="type === 'success'" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 24 24" fill="currentColor">
                    <path fill-rule="evenodd" d="M2.25 12c0-5.385 4.365-9.75 9.75-9.75s9.75 4.365 9.75 9.75-4.365 9.75-9.75 9.75S2.25 17.385 2.25 12zm13.36-1.814a.75.75 0 10-1.22-.872l-3.236 4.53L9.53 12.22a.75.75 0 00-1.06 1.06l2.25 2.25a.75.75 0 001.14-.094l3.75-5.25z" clip-rule="evenodd" />
                </svg>
                <!-- Warning -->
                <svg v-else-if="type === 'warning'" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 24 24" fill="currentColor">
                    <path fill-rule="evenodd" d="M9.401 3.003c1.155-2 4.043-2 5.197 0l7.355 12.748c1.154 2-.29 4.5-2.599 4.5H4.645c-2.309 0-3.752-2.5-2.598-4.5L9.4 3.003zM12 8.25a.75.75 0 01.75.75v3.75a.75.75 0 01-1.5 0V9a.75.75 0 01.75-.75zm0 8.25a.75.75 0 100-1.5.75.75 0 000 1.5z" clip-rule="evenodd" />
                </svg>
                <!-- Danger -->
                <svg v-else-if="type === 'danger'" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 24 24" fill="currentColor">
                    <path fill-rule="evenodd" d="M12 2.25c-5.385 0-9.75 4.365-9.75 9.75s4.365 9.75 9.75 9.75 9.75-4.365 9.75-9.75S17.385 2.25 12 2.25zm-1.72 6.97a.75.75 0 10-1.06 1.06L10.94 12l-1.72 1.72a.75.75 0 101.06 1.06L12 13.06l1.72 1.72a.75.75 0 101.06-1.06L13.06 12l1.72-1.72a.75.75 0 10-1.06-1.06L12 10.94l-1.72-1.72z" clip-rule="evenodd" />
                </svg>
                <!-- Info -->
                <svg v-else-if="type === 'info'" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 24 24" fill="currentColor">
                    <path fill-rule="evenodd" d="M2.25 12c0-5.385 4.365-9.75 9.75-9.75s9.75 4.365 9.75 9.75-4.365 9.75-9.75 9.75S2.25 17.385 2.25 12zm8.706-1.442c1.146-.573 2.437.463 2.126 1.706l-.709 2.836.042-.02a.75.75 0 01.67 1.34l-.04.022c-1.147.573-2.438-.463-2.127-1.706l.71-2.836-.042.02a.75.75 0 11-.671-1.34l.041-.022zM12 9a.75.75 0 100-1.5.75.75 0 000 1.5z" clip-rule="evenodd" />
                </svg>
                <!-- Primary -->
                <svg v-else-if="type === 'primary'" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 24 24" fill="currentColor">
                    <path fill-rule="evenodd" d="M9 4.5a.75.75 0 01.721.544l.813 2.846a3.75 3.75 0 002.576 2.576l2.846.813a.75.75 0 010 1.442l-2.846.813a3.75 3.75 0 00-2.576 2.576l-.813 2.846a.75.75 0 01-1.442 0l-.813-2.846a3.75 3.75 0 00-2.576-2.576l-2.846-.813a.75.75 0 010-1.442l2.846-.813A3.75 3.75 0 007.466 7.89l.813-2.846A.75.75 0 019 4.5z" clip-rule="evenodd" />
                </svg>
                <!-- Secondary fallback -->
                <svg v-else xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 24 24" fill="currentColor">
                    <path fill-rule="evenodd" d="M4.848 2.771A49.144 49.144 0 0112 2.25c2.43 0 4.817.178 7.152.52 1.978.292 3.348 2.024 3.348 3.97v6.02c0 1.946-1.37 3.678-3.348 3.97a48.901 48.901 0 01-3.476.383.39.39 0 00-.297.17l-2.755 4.133a.75.75 0 01-1.248 0l-2.755-4.133a.39.39 0 00-.297-.17 48.9 48.9 0 01-3.476-.384c-1.978-.29-3.348-2.024-3.348-3.97V6.741c0-1.946 1.37-3.68 3.348-3.97z" clip-rule="evenodd" />
                </svg>
            </span>

            <!-- Content -->
            <div class="flex-1 text-sm leading-relaxed">
                <p v-if="title" class="mb-0.5 font-semibold">{{ title }}</p>
                <slot>{{ message }}</slot>
            </div>

            <!-- Dismiss button -->
            <button
                v-if="dismissable"
                type="button"
                aria-label="Dismiss"
                class="mt-0.5 shrink-0 opacity-60 transition-opacity hover:opacity-100"
                @click="dismiss"
            >
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>

        </div>
    </transition>
</template>
