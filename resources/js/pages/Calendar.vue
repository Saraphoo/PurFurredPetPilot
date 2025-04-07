<script setup lang="ts">
import Chatbot from "@/components/chatbot/Chatbot.vue";
import Calendar from "@/components/Calendar.vue";
import {BreadcrumbItem} from "@/types";
import AppLayout from "@/layouts/AppLayout.vue";



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
        petImage: string | null;
        type: string;
        DOB: string;
    }[] | null;
}>();

</script>

<template>
    <Head title="Dashboard" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-4 rounded-xl p-4">
            <div class="grid auto-rows-min gap-4 md:grid-cols-3">
                <div>
                    <Calendar :pets="pets" />
                </div>
            </div>
        </div>
        <Chatbot/>
    </AppLayout>
</template>
