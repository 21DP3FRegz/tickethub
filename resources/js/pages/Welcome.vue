<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';

const concerts = [
    { id: 1, artist: 'Artist 1', location: 'Concert Hall', date: '2025-06-01' },
    { id: 2, artist: 'Artist 2', location: 'Theater', date: '2025-06-15' },
];
</script>

<template>
    <Head title="Welcome">
        <link rel="preconnect" href="https://rsms.me/" />
        <link rel="stylesheet" href="https://rsms.me/inter/inter.css" />
    </Head>
    <div class="flex min-h-screen flex-col items-center bg-[#FDFDFC] p-6 text-[#1b1b18] dark:bg-[#0a0a0a] lg:justify-center lg:p-8">
        <header class="mb-6 w-full max-w-[335px] text-sm lg:max-w-4xl">
            <nav class="flex items-center justify-end gap-4">
                <Link v-if="$page.props.auth.user" :href="route('dashboard')" class="inline-block rounded-sm border border-[#19140035] px-5 py-1.5 text-sm leading-normal text-[#1b1b18] hover:border-[#1915014a] dark:border-[#3E3E3A] dark:text-[#EDEDEC] dark:hover:border-[#62605b]">
                    Dashboard
                </Link>
                <template v-else>
                    <Link :href="route('login')" class="inline-block rounded-sm border border-transparent px-5 py-1.5 text-sm leading-normal text-[#1b1b18] hover:border-[#19140035] dark:text-[#EDEDEC] dark:hover:border-[#3E3E3A]">
                        Log in
                    </Link>
                    <Link :href="route('register')" class="inline-block rounded-sm distance-[#19140035] px-5 py-1.5 text-sm leading-normal text-[#1b1b18] hover:border-[#1915014a] dark:border-[#3E3E3A] dark:text-[#EDEDEC] dark:hover:border-[#62605b]">
                        Register
                    </Link>
                </template>
            </nav>
        </header>
        <div class="duration-750 starting:opacity-0 flex w-full items-center justify-center opacity-100 transition-opacity lg:grow">
            <main class="flex w-full max-w-[335px] flex-col-reverse overflow-hidden rounded-lg lg:max-w-4xl lg:flex-row">
                <div class="flex-1 rounded-bl-lg rounded-br-lg bg-white p-6 pb-12 text-[13px] leading-[20px] shadow-[inset_0px_0px_0px_1px_rgba(26,26,0,0.16)] dark:bg-[#161615] dark:text-[#EDEDEC] dark:shadow-[inset_0px_0px_0px_1px_#fffaed2d] lg:rounded-br-none lg:rounded-tl-lg lg:p-20">
                    <h1 class="mb-1 font-medium">Upcoming Concerts</h1>
                    <p class="mb-2 text-[#706f6c] dark:text-[#A1A09A]">
                        Browse our upcoming concerts and reserve your seats today.
                    </p>
                    <ul class="mb-4 flex flex-col lg:mb-6">
                        <li v-for="concert in concerts" :key="concert.id" class="py-2">
                            <Link :href="route('concerts.show', concert.id)" class="font-medium text-[#f53003] underline underline-offset-4 dark:text-[#FF4433]">
                                {{ concert.artist }} - {{ concert.location }} ({{ concert.date }})
                            </Link>
                        </li>
                    </ul>
                    <a href="https://cloud.laravel.com" target="_blank" class="inline-block rounded-sm border border-black bg-[#1b1b18] px-5 py-1.5 text-sm leading-normal text-white hover:border-black hover:bg-black dark:border-[#eeeeec] dark:bg-[#eeeeec] dark:text-[#1C1C1A] dark:hover:border-white dark:hover:bg-white">
                        Explore More
                    </a>
                </div>
                <div class="relative -mb-px aspect-335/376 w-full shrink-0 overflow-hidden rounded-t-lg bg-[#fff2f2] dark:bg-[#1D0002] lg:-ml-px lg:mb-0 lg:aspect-auto lg:w-[438px] lg:rounded-r-lg lg:rounded-t-none">
                    <!-- Keep existing SVG logo -->
                    <svg class="duration-750 starting:translate-y-6 starting:opacity-0 w-full max-w-none translate-y-0 text-[#F53003] opacity-100 transition-all dark:text-[#F61500]" viewBox="0 0 438 104" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <!-- Existing SVG paths -->
                    </svg>
                    <div class="absolute inset-0 rounded-t-lg shadow-[inset_0px_0px_0px_1px_rgba(26,26,0,0.16)] dark:shadow-[inset_0px_0px_0px_1px_#fffaed2d] lg:overflow-hidden lg:rounded-r-lg lg:rounded-t-none" />
                </div>
            </main>
        </div>
    </div>
</template>
