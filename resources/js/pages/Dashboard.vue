<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head } from '@inertiajs/vue3';
import PetProfileCard from "@/pages/dashboard/PetProfileCard.vue";



const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Dashboard',
        href: '/dashboard',
    },
];

defineProps<{
    pets: {
        id: number;
        name: string;

    };
}>();




</script>

<template>
    <Head title="Dashboard" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-4 rounded-xl p-4">
            <div class="grid auto-rows-min gap-4 md:grid-cols-3"
                 v-if="pets.length !== 0">
                <div v-for ="pet in pets" :key="pet.id">
                    <PetProfileCard :pet = pet />
                </div>
            </div>
            <div class="grid auto-rows-min gap-4 md:grid-cols-3" v-else>
                <div>No pets to display</div>
            </div>
        </div>
    </AppLayout>
</template>
