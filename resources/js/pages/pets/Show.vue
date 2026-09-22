<script setup lang="ts">
import {computed, defineProps, ref} from 'vue';
import Chatbot from '@/components/chatbot/Chatbot.vue';
import ActivityForm from '@/components/form/ActivityForm.vue';
import MealForm from '@/components/form/MealForm.vue';
import MedicalForm from '@/components/form/MedicalForm.vue';
import HousingForm from '@/components/form/HousingForm.vue';
import BehaviorForm from '@/components/form/BehaviorForm.vue';
import { Button } from '@/components/ui/button';
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
    initialData: {
        medical: any;
        meals: any;
        behavior: any;
        housing: any;
    };
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

const formTabs = [
    { value: 'activity', label: 'Activity' },
    { value: 'meal', label: 'Meal' },
    { value: 'medical', label: 'Medical' },
    { value: 'housing', label: 'Housing' },
    { value: 'behavior', label: 'Behavior' },
];

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

// Add computed property for initial data
const currentFormInitialData = computed(() => {
  switch (selectedForm.value) {
    case 'medical':
      return {
        special_needs: props.initialData.medical?.special_needs || [],
        medications: props.initialData.medical?.medications || [],
        notes: props.initialData.medical?.notes || ''
      };
    case 'meals':
      return props.initialData.meals;
    case 'behavior':
      return props.initialData.behavior;
    case 'housing':
      return props.initialData.housing;
    default:
      return null;
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
        <div class="mb-4 flex flex-wrap gap-2">
            <Button
                v-for="tab in formTabs"
                :key="tab.value"
                type="button"
                class="min-w-[120px] flex-1"
                :variant="selectedForm === tab.value ? 'default' : 'secondary'"
                @click="selectedForm = tab.value"
            >
                {{ tab.label }}
            </Button>
        </div>

        <!-- Dynamic Form Component -->
        <component
            :is="currentForm"
            :pet-id="pet.id"
            :initial-data="currentFormInitialData"
        />

    </div>
        </div>
    </AppLayout>
    <Chatbot/>
</template>
