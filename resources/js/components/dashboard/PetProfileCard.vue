<script setup lang="ts">

import {computed, onMounted} from "vue";
import {Card} from "@/components/ui/card";
import { router } from '@inertiajs/vue3';

const props = defineProps<{
    pet: {
        id: number;
        name: string;
        petImage: string | null;
        type: string;
        DOB: string | null;
    };
}>();

onMounted(() => {
    console.log(this);
})

const petAge = computed(() => {
    if(!props.pet.DOB) {
        return null;
    }

    const dob = new Date(props.pet.DOB);
    const today = new Date();

    let years = today.getFullYear() - dob.getFullYear();
    let months = today.getMonth() - dob.getMonth();

    if (months < 0 || (months === 0 && today.getDate() < dob.getDate())) {
        years--;
        months += 12;
    }

    return {
        years,
        months
    };
});

const formattedAge = computed(() => {
    if(!petAge.value){
        return;
    }

    const { years, months } = petAge.value;
    const yearText = years === 1 ? '1 year' : `${years} years`;
    const monthText = months === 1 ? '1 month' : `${months} months`;

    return `${yearText} & ${monthText}`;
});

function goToPetProfile(petId : number) {
    router.get(`/pets/show/${petId}`);
}

</script>

<template>
    <Card  :key="pet.id"
           @click="goToPetProfile(pet.id)"
           class="flex flex-col items-center p-6 space-y-4">
        <!-- Image Section -->
        <div v-if="pet.petImage" class="flex justify-center w-full">
            <img :src="pet.petImage" alt="pet image" class="w-24 h-24 rounded-full" />
        </div>
        <div v-else class="w-24 h-24 rounded-full bg-gray-200 flex items-center justify-center">
            <!-- Placeholder for pet image -->
        </div>

        <!-- Pet Information Section -->
        <div class="text-center">
            <p class="font-semibold text-lg text-gray-900">{{ pet.name }}</p>
            <p class="text-gray-600">{{ pet.type }} | {{ formattedAge }}</p>
        </div>
    </Card>
</template>


<style scoped>

</style>
