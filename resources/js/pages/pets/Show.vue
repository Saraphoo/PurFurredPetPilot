<script setup lang="ts">
import {computed, defineProps, ref} from 'vue';
import Chatbot from '@/components/chatbot/Chatbot.vue';
import ActivityForm from '@/components/form/ActivityForm.vue';
import MealForm from '@/components/form/MealForm.vue';
import MedicalForm from '@/components/form/MedicalForm.vue';
import HousingForm from '@/components/form/HousingForm.vue';
import BehaviorForm from '@/components/form/BehaviorForm.vue';
import AppLayout from "@/layouts/AppLayout.vue";
import {BreadcrumbItem} from "@/types";

// Add a type for the pet info items
interface PetInfoItem {
    key: string;
    value: string;
    component?: string;
    componentData?: any;
}

// Update the props type to include possible component data
const props = defineProps<{
    pet: {
        id: number;
        name: string;
        species: string;
        petImage: string | null;
        type: string;
        DOB: string | null;
    };
    petInfo: Array<PetInfoItem>;
}>();


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

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Dashboard',
        href: '/dashboard',
    },
    {
        title: 'Pet Profile',
        href: `/pets/${props.pet.id}`,
    }
];

const formattedAge = computed(() => {
    if(!petAge.value){
        return;
    }

    const { years, months } = petAge.value;
    const yearText = years === 1 ? '1 year' : `${years} years`;
    const monthText = months === 1 ? '1 month' : `${months} months`;

    return `${yearText} & ${monthText}`;
});

// Add new ref for form selection
const selectedForm = ref('activity');

// Add new computed property for form component
const currentForm = computed(() => {
  switch (selectedForm.value) {
    case 'activity':
      return ActivityForm;
    case 'meal':
      return MealForm;
    case 'medical':
      return MedicalForm;
    case 'housing':
      return HousingForm;
    case 'behavior':
      return BehaviorForm;
    default:
      return ActivityForm;
  }
});

</script>

<template>
    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="max-w-4xl mx-auto p-6 bg-white dark:bg-gray-800 rounded-lg shadow-md">
    <div class="p-6 max-w-6xl mx-auto bg-white rounded-xl shadow-md space-y-4">

        <!-- Profile Section -->
        <div class="flex items-center justify-center">
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
        </div>
        <div class="flex items-center justify-center text-center">
            <!-- Profile Information -->
            <div>
                <h1 class="text-2xl font-bold text-gray-800">{{ pet.name }}</h1>
                <p class="text-gray-600">{{ pet.type }} <span v-if="pet.species"> | {{pet.species}}</span> </p>
                <p class="text-gray-600">{{ formattedAge }}</p>
            </div>
        </div>

        <!-- Form Selection Toggle -->
        <v-btn-toggle
            v-model="selectedForm"
            mandatory
            class="mb-4 custom-toggle"
        >
            <v-btn value="activity" class="custom-btn">
                Activities
            </v-btn>
            <v-btn value="meal" class="custom-btn">
                Meals
            </v-btn>
            <v-btn value="medical" class="custom-btn">
                Medical
            </v-btn>
            <v-btn value="housing" class="custom-btn">
                Housing
            </v-btn>
            <v-btn value="behavior" class="custom-btn">
                Behavior
            </v-btn>
        </v-btn-toggle>

        <!-- Dynamic Form Component -->
        <component
            :is="currentForm"
            ref="currentFormRef"
            :pet-id="pet.id"
        />

    </div>
        </div>
    </AppLayout>
    <Chatbot/>
</template>

<style scoped>
.v-btn-toggle {
    width: 100%;
    display: flex;
    flex-wrap: wrap;
    gap: 8px;
}

.v-btn-toggle .v-btn {
    flex: 1;
    min-width: 120px;
}

.custom-toggle :deep(.v-btn) {
    background-color: #FFBF69 !important;
    color: white !important;
    transition: all 0.2s !important;
}

.custom-toggle :deep(.v-btn:hover) {
    background-color: #FF9F1C !important;
}

.custom-toggle :deep(.v-btn--active) {
    background-color: #27b99c !important;
    color: white !important;
    font-weight: 600 !important;
}

.custom-toggle :deep(.v-btn--active:hover) {
    background-color: #27b99c !important;
    opacity: 0.9;
}
</style>
