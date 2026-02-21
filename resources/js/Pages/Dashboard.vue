<template>
    <div>
        <ul class="flex space-x-2 rtl:space-x-reverse">
            <li>
                <a href="javascript:;" class="text-primary hover:underline">Dashboard</a>
            </li>
            <li class="before:content-['/'] ltr:before:mr-2 rtl:before:ml-2">
                <span>Sales</span>
            </li>
        </ul>

        <div class="pt-5">
            <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-6 mb-6">
                <!-- Users Visit -->
                <div class="panel h-full">
                    <div class="flex justify-between dark:text-white-light mb-5">
                        <h5 class="font-semibold text-lg">Users Visit</h5>
                        <svg class="w-5 h-5 text-primary" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path
                                opacity="0.5"
                                d="M3 10C3 6.22876 3 4.34315 4.17157 3.17157C5.34315 2 7.22876 2 11 2H13C16.7712 2 18.6569 2 19.8284 3.17157C21 4.34315 21 6.22876 21 10V14C21 17.7712 21 19.6569 19.8284 20.8284C18.6569 22 16.7712 22 13 22H11C7.22876 22 5.34315 22 4.17157 20.8284C3 19.6569 3 17.7712 3 14V10Z"
                                stroke="currentColor"
                                stroke-width="1.5"
                            />
                            <circle cx="12" cy="12" r="4" stroke="currentColor" stroke-width="1.5" />
                        </svg>
                    </div>
                    <div class="text-3xl font-bold text-[#e7515a]">
                        <span>15,763</span>
                    </div>
                </div>

                <!-- Sessions -->
                <div class="panel h-full">
                    <div class="flex justify-between dark:text-white-light mb-5">
                        <h5 class="font-semibold text-lg">Sessions</h5>
                        <svg class="w-5 h-5 text-success" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path
                                d="M20 12V8H6C4.89543 8 4 7.10457 4 6V6C4 4.89543 4.89543 4 6 4H18C19.1046 4 20 4.89543 20 6V12V18C20 19.1046 19.1046 20 18 20H6C4.89543 20 4 19.1046 4 18V12"
                                stroke="currentColor"
                                stroke-width="1.5"
                                stroke-linecap="round"
                            />
                        </svg>
                    </div>
                    <div class="text-3xl font-bold text-success">
                        <span>4,250</span>
                    </div>
                </div>

                <!-- Time On Site -->
                <div class="panel h-full">
                    <div class="flex justify-between dark:text-white-light mb-5">
                        <h5 class="font-semibold text-lg">Time On Site</h5>
                        <svg class="w-5 h-5 text-warning" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <circle opacity="0.5" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="1.5" />
                            <path d="M12 8V12L14.5 14.5" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                        </svg>
                    </div>
                    <div class="text-3xl font-bold text-warning">
                        <span>38m 45s</span>
                    </div>
                </div>

                <!-- Bounce Rate -->
                <div class="panel h-full">
                    <div class="flex justify-between dark:text-white-light mb-5">
                        <h5 class="font-semibold text-lg">Bounce Rate</h5>
                        <svg class="w-5 h-5 text-info" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path
                                opacity="0.5"
                                d="M2 12.5001L3.75159 10.9675C4.66286 10.1702 6.03628 10.2159 6.89249 11.0721L11.1822 15.3618C11.8694 16.0491 12.9512 16.1428 13.7464 15.5839L14.0446 15.3744C15.1888 14.5702 16.7369 14.6634 17.7765 15.599L21 18.5001"
                                stroke="currentColor"
                                stroke-width="1.5"
                                stroke-linecap="round"
                            />
                            <path
                                d="M22 12C22 16.714 22 19.0711 20.5355 20.5355C19.0711 22 16.714 22 12 22C7.28595 22 4.92893 22 3.46447 20.5355C2 19.0711 2 16.714 2 12C2 7.28595 2 4.92893 3.46447 3.46447C4.92893 2 7.28595 2 12 2C16.714 2 19.0711 2 20.5355 3.46447C21.5093 4.43821 21.8356 5.80655 21.9449 8"
                                stroke="currentColor"
                                stroke-width="1.5"
                                stroke-linecap="round"
                            />
                        </svg>
                    </div>
                    <div class="text-3xl font-bold text-info">
                        <span>49.1%</span>
                    </div>
                </div>
            </div>

            <div class="panel">
                <div class="flex items-center justify-between mb-5">
                    <h5 class="font-semibold text-lg dark:text-white-light">Welcome to Dashboard</h5>
                </div>
                <div class="mb-5">
                    <p class="text-white-dark">
                        You are now logged in. This is your dashboard where you can manage your application.
                    </p>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup lang="ts">
import { useMeta } from '@/composables/use-meta';

useMeta({ title: 'Dashboard' });
</script>

<script lang="ts">
import appLayout from '@/layouts/app-layout.vue';

export default {
    layout: appLayout,
};
</script>
