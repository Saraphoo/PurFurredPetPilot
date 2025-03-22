<script setup lang="ts">
import {computed, defineProps} from 'vue';

// Define props for pet
const props = defineProps<{
    pet: {
        id: number;
        name: string;
        petImage: string | null;
        type: string;
        DOB: string | null;
    };
    petInfo: Array<{ key: string, value: string }>
}>();

const additionalPetInfo = computed(() => {
    return props.petInfo || [];
});

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

</script>

<template>
    <div class="p-6 max-w-4xl mx-auto bg-white rounded-xl shadow-md space-y-4">
        <!-- Profile Section -->
        <div class="flex items-center">
            <!-- Profile Image -->
            <div v-if="pet.petImage" class="w-40 h-40 rounded-full bg-gray-300 overflow-hidden">
                <img
                    class="w-full h-full object-cover"
                    :src="pet.petImage"
                    alt="Profile Picture"
                />
            </div>
            <div v-else class="w-40 h-40 rounded-full bg-gray-200 flex">
            </div>

            <!-- Profile Information -->
            <div class="ml-6">
                <h1 class="text-2xl font-bold text-gray-800">{{ pet.name }}</h1>
                <p class="text-gray-600">{{ pet.type }}</p>
                <p class="text-gray-600">{{ formattedAge }}</p>
            </div>
        </div>

        <!-- Additional Information -->
        <div class="mt-4 p-4 bg-gray-50 rounded-lg">
            <h2 class="text-xl font-semibold text-gray-700">Additional Information</h2>
            <ul>
                <li v-for="(info, index) in additionalPetInfo" :key="index" class="text-gray-600">
                    {{ info.key }}: {{ info.value }}
                </li>
            </ul>
        </div>
    </div>
</template>



<style scoped>

</style>
