<script setup lang="ts">
import Chatbot from "@/components/chatbot/Chatbot.vue";
import Calendar from "@/components/Calendar.vue";
import {BreadcrumbItem} from "@/types";
import AppLayout from "@/layouts/AppLayout.vue";

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Calendar',
        href: '/calendar',
    },
];

defineProps<{
    pets: {
        id: number;
        name: string;
        petImage: string | null;
        type: string;
        DOB: string;
    }[] | null;
}>();
</script>

<template>
    <Head title="Calendar" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-4 rounded-xl p-4">
            <div class="grid auto-rows-min gap-4">
                <div class="bg-white rounded-xl shadow-sm p-4">
                    <h2 class="text-xl font-semibold mb-4">Pet Care Calendar</h2>
                    <Calendar :pets="pets" />
                </div>
            </div>
        </div>
        <Chatbot/>
    </AppLayout>
</template>
