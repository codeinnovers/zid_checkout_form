<template>
    <div>
        <!-- Breadcrumb -->
        <ul class="flex space-x-2 rtl:space-x-reverse">
            <li>
                <Link href="/dashboard" class="text-primary hover:underline">Dashboard</Link>
            </li>
            <li class="before:content-['/'] ltr:before:mr-2 rtl:before:ml-2">
                <span>Profile</span>
            </li>
        </ul>

        <div class="pt-5 grid grid-cols-1 xl:grid-cols-3 gap-6">

            <!-- ── Left: Avatar card ───────────────────────────────── -->
            <div class="panel xl:col-span-1">
                <div class="flex flex-col items-center text-center py-6">
                    <!-- Avatar with initials -->
                    <div
                        class="w-24 h-24 rounded-full bg-primary flex items-center justify-center text-white text-3xl font-bold mb-4 ring-4 ring-primary/20"
                    >
                        {{ initials }}
                    </div>
                    <h4 class="text-xl font-semibold dark:text-white">{{ profileForm.name }}</h4>
                    <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">{{ profileForm.email }}</p>
                    <span class="mt-3 badge bg-success text-xs">Active</span>
                </div>

                <div class="border-t border-white-light dark:border-[#253a5b] pt-5 space-y-3 text-sm">
                    <div class="flex items-center gap-3 text-gray-500 dark:text-gray-400">
                        <svg class="w-4 h-4 shrink-0" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M21.75 6.75v10.5a2.25 2.25 0 01-2.25 2.25h-15a2.25 2.25 0 01-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25m19.5 0v.243a2.25 2.25 0 01-1.07 1.916l-7.5 4.615a2.25 2.25 0 01-2.36 0L3.32 8.91a2.25 2.25 0 01-1.07-1.916V6.75" />
                        </svg>
                        <span class="truncate">{{ profileForm.email }}</span>
                    </div>
                    <div class="flex items-center gap-3 text-gray-500 dark:text-gray-400">
                        <svg class="w-4 h-4 shrink-0" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z" />
                        </svg>
                        <span>{{ props.user.name }}</span>
                    </div>
                </div>
            </div>

            <!-- ── Right: Forms ────────────────────────────────────── -->
            <div class="xl:col-span-2 space-y-6">

                <!-- Profile Information -->
                <div class="panel">
                    <h5 class="font-semibold text-lg dark:text-white mb-5">Profile Information</h5>

                    <form @submit.prevent="submitProfile" class="space-y-4">
                        <div :class="{ 'has-error': profileForm.errors.name }">
                            <label for="name">Full Name</label>
                            <input
                                id="name"
                                type="text"
                                v-model="profileForm.name"
                                class="form-input"
                                placeholder="Your full name"
                                autocomplete="name"
                            />
                            <p v-if="profileForm.errors.name" class="text-danger text-sm mt-1">{{ profileForm.errors.name }}</p>
                        </div>

                        <div :class="{ 'has-error': profileForm.errors.email }">
                            <label for="email">Email Address</label>
                            <input
                                id="email"
                                type="email"
                                v-model="profileForm.email"
                                class="form-input"
                                placeholder="your@email.com"
                                autocomplete="email"
                            />
                            <p v-if="profileForm.errors.email" class="text-danger text-sm mt-1">{{ profileForm.errors.email }}</p>
                        </div>

                        <div class="flex justify-end pt-2">
                            <button
                                type="submit"
                                class="btn btn-primary"
                                :disabled="profileForm.processing"
                            >
                                <svg v-if="profileForm.processing" class="animate-spin w-4 h-4 ltr:mr-2 rtl:ml-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" />
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z" />
                                </svg>
                                Save Changes
                            </button>
                        </div>
                    </form>
                </div>

                <!-- Change Password -->
                <div class="panel">
                    <h5 class="font-semibold text-lg dark:text-white mb-5">Change Password</h5>

                    <form @submit.prevent="submitPassword" class="space-y-4">
                        <div :class="{ 'has-error': passwordForm.errors.current_password }">
                            <label for="current_password">Current Password</label>
                            <div class="relative">
                                <input
                                    id="current_password"
                                    :type="showCurrentPassword ? 'text' : 'password'"
                                    v-model="passwordForm.current_password"
                                    class="form-input ltr:pr-10 rtl:pl-10"
                                    placeholder="Enter current password"
                                    autocomplete="current-password"
                                />
                                <button
                                    type="button"
                                    class="absolute ltr:right-3 rtl:left-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600 dark:hover:text-gray-300"
                                    @click="showCurrentPassword = !showCurrentPassword"
                                >
                                    <svg v-if="!showCurrentPassword" xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                    </svg>
                                    <svg v-else xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M3.98 8.223A10.477 10.477 0 001.934 12C3.226 16.338 7.244 19.5 12 19.5c.993 0 1.953-.138 2.863-.395M6.228 6.228A10.45 10.45 0 0112 4.5c4.756 0 8.773 3.162 10.065 7.498a10.523 10.523 0 01-4.293 5.774M6.228 6.228L3 3m3.228 3.228l3.65 3.65m7.894 7.894L21 21m-3.228-3.228l-3.65-3.65m0 0a3 3 0 10-4.243-4.243m4.242 4.242L9.88 9.88" />
                                    </svg>
                                </button>
                            </div>
                            <p v-if="passwordForm.errors.current_password" class="text-danger text-sm mt-1">{{ passwordForm.errors.current_password }}</p>
                        </div>

                        <div :class="{ 'has-error': passwordForm.errors.password }">
                            <label for="password">New Password</label>
                            <div class="relative">
                                <input
                                    id="password"
                                    :type="showNewPassword ? 'text' : 'password'"
                                    v-model="passwordForm.password"
                                    class="form-input ltr:pr-10 rtl:pl-10"
                                    placeholder="Enter new password"
                                    autocomplete="new-password"
                                />
                                <button
                                    type="button"
                                    class="absolute ltr:right-3 rtl:left-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600 dark:hover:text-gray-300"
                                    @click="showNewPassword = !showNewPassword"
                                >
                                    <svg v-if="!showNewPassword" xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                    </svg>
                                    <svg v-else xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M3.98 8.223A10.477 10.477 0 001.934 12C3.226 16.338 7.244 19.5 12 19.5c.993 0 1.953-.138 2.863-.395M6.228 6.228A10.45 10.45 0 0112 4.5c4.756 0 8.773 3.162 10.065 7.498a10.523 10.523 0 01-4.293 5.774M6.228 6.228L3 3m3.228 3.228l3.65 3.65m7.894 7.894L21 21m-3.228-3.228l-3.65-3.65m0 0a3 3 0 10-4.243-4.243m4.242 4.242L9.88 9.88" />
                                    </svg>
                                </button>
                            </div>
                            <p v-if="passwordForm.errors.password" class="text-danger text-sm mt-1">{{ passwordForm.errors.password }}</p>
                        </div>

                        <div :class="{ 'has-error': passwordForm.errors.password_confirmation }">
                            <label for="password_confirmation">Confirm New Password</label>
                            <input
                                id="password_confirmation"
                                type="password"
                                v-model="passwordForm.password_confirmation"
                                class="form-input"
                                placeholder="Re-enter new password"
                                autocomplete="new-password"
                            />
                            <p v-if="passwordForm.errors.password_confirmation" class="text-danger text-sm mt-1">{{ passwordForm.errors.password_confirmation }}</p>
                        </div>

                        <div class="flex justify-end pt-2">
                            <button
                                type="submit"
                                class="btn btn-primary"
                                :disabled="passwordForm.processing"
                            >
                                <svg v-if="passwordForm.processing" class="animate-spin w-4 h-4 ltr:mr-2 rtl:ml-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" />
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z" />
                                </svg>
                                Update Password
                            </button>
                        </div>
                    </form>
                </div>

            </div>
        </div>
    </div>
</template>

<script setup lang="ts">
import { computed, ref } from 'vue';
import { Link, useForm, usePage } from '@inertiajs/vue3';
import { route } from 'ziggy-js';
import { useMeta } from '@/composables/use-meta';
import { useNotifications } from '@/composables/use-notifications';

useMeta({ title: 'Profile' });

const { success, error } = useNotifications();

interface User {
    id: number;
    name: string;
    email: string;
}

interface Props {
    user: User;
}

const props = defineProps<Props>();

// ── Profile Information Form ────────────────────────────
const profileForm = useForm({
    name:  props.user.name,
    email: props.user.email,
});

const submitProfile = () => {
    profileForm.put(route('profile.update'), {
        preserveScroll: true,
        onSuccess: () => success('Profile updated successfully.'),
        onError:   () => error('Please fix the errors below.'),
    });
};

// ── Change Password Form ────────────────────────────────
const passwordForm = useForm({
    current_password:      '',
    password:              '',
    password_confirmation: '',
});

const showCurrentPassword = ref(false);
const showNewPassword      = ref(false);

const submitPassword = () => {
    passwordForm.put(route('profile.password'), {
        preserveScroll: true,
        onSuccess: () => {
            passwordForm.reset();
            success('Password updated successfully.');
        },
        onError: () => error('Please fix the errors below.'),
    });
};

// ── Avatar initials ──────────────────────────────────────
const initials = computed(() => {
    return props.user.name
        .split(' ')
        .map((w) => w[0])
        .slice(0, 2)
        .join('')
        .toUpperCase();
});
</script>

<script lang="ts">
import appLayout from '@/layouts/app-layout.vue';

export default {
    layout: appLayout,
};
</script>
