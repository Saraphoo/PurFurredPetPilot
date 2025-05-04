<script setup lang="ts">
import { useForm } from '@inertiajs/inertia-vue3';
import AppLayout from "@/layouts/AppLayout.vue";
import { type BreadcrumbItem } from '@/types';
import {computed, ref} from "vue";
import { petTypes, speciesOptions, colors, sexOptions, type PetType } from '@/data/petOptions';

interface PetForm {
    name: string;
    DOB: string;
    type: PetType | '';
    species: string;
    breed: string;
    neutered: boolean;
    color: string;
    weight: string;
    sex: string;
}

// Initialize the form with default values
const form = useForm<PetForm>({
    name: '',
    DOB: '',
    type: '',
    species: '',
    breed: '',
    neutered: false,
    color: '',
    weight: '',
    sex: '',
});

// Computed property to get species based on selected type
const availableSpecies = computed(() => {
    return form.type ? speciesOptions[form.type] : [];
});

// Define the breadcrumbs for navigation
const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Dashboard',
        href: '/dashboard',
    },
    {
        title: 'Create Pet',
        href: '/pets/create',
    },
];

// Function to handle form submission
const submit = () => {
    form.post(route('pets.store'));
};

// Define component props
defineProps<{
    errors: Record<string, string>;
}>();
</script>

<template>
    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="max-w-4xl mx-auto p-6 bg-white dark:bg-gray-800 rounded-lg shadow-md">
            <h1 class="text-2xl font-bold mb-6 text-gray-900 dark:text-gray-100">Create a New Pet</h1>
            <form @submit.prevent="submit" class="space-y-4">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <!-- Name and Date of Birth fields -->
                    <div>
                        <label for="name" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Name</label>
                        <input v-model="form.name" type="text" id="name" class="mt-1 block w-full" />
                        <span v-if="form.errors.name" class="text-red-600 text-sm">{{ form.errors.name }}</span>
                    </div>
                    <div>
                        <label for="DOB" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Date of Birth</label>
                        <input v-model="form.DOB" type="date" id="DOB" class="mt-1 block w-full" />
                        <span v-if="form.errors.DOB" class="text-red-600 text-sm">{{ form.errors.DOB }}</span>
                    </div>

                    <!-- Pet Type and Species Dropdowns -->
                    <div>
                        <label for="type" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Type</label>
                        <select v-model="form.type" id="type" class="mt-1 block w-full">
                            <option value="" disabled>Select Type</option>
                            <option v-for="type in petTypes" :key="type.value" :value="type.value">{{ type.label }}</option>
                        </select>
                        <span v-if="form.errors.type" class="text-red-600 text-sm">{{ form.errors.type }}</span>
                    </div>

                    <div>
                        <label for="species" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Species</label>
                        <select v-model="form.species" id="species" class="mt-1 block w-full" :disabled="!form.type">
                            <option value="" disabled>Select Species</option>
                            <option v-for="species in availableSpecies" :key="species" :value="species">{{ species }}</option>
                        </select>
                        <span v-if="form.errors.species" class="text-red-600 text-sm">{{ form.errors.species }}</span>
                    </div>

                    <!-- Color and Sex -->
                    <div>
                        <label for="color" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Color</label>
                        <select v-model="form.color" id="color" class="mt-1 block w-full">
                            <option value="" disabled>Select Color</option>
                            <option v-for="color in colors" :key="color" :value="color">{{ color }}</option>
                        </select>
                        <span v-if="form.errors.color" class="text-red-600 text-sm">{{ form.errors.color }}</span>
                    </div>

                    <div>
                        <label for="sex" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Sex</label>
                        <select v-model="form.sex" id="sex" class="mt-1 block w-full">
                            <option value="" disabled>Select Sex</option>
                            <option v-for="option in sexOptions" :key="option.value" :value="option.value">
                                {{ option.label }}
                            </option>
                        </select>
                        <span v-if="form.errors.sex" class="text-red-600 text-sm">{{ form.errors.sex }}</span>
                    </div>

                    <!-- Neutered Checkbox -->
                    <div>
                        <label for="neutered" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Neutered</label>
                        <input v-model="form.neutered" type="checkbox" id="neutered" class="mt-1 block" />
                        <span v-if="form.errors.neutered" class="text-red-600 text-sm">{{ form.errors.neutered }}</span>
                    </div>

                    <!-- Other fields (e.g., breed, weight, etc.) go here -->
                </div>

                <button type="submit" class="px-4 py-2 bg-[#2EC4B6] text-white rounded-md shadow hover:bg-[#CBF3F0] transition-colors">Create Pet</button>
            </form>
        </div>
    </AppLayout>
</template>

<style scoped>
input:focus, select:focus {
    outline: none;
    border-color: #FF9F1C;
    box-shadow: 0 0 0 2px #FFBF69;
}

input, select {
    border: 1px solid #2EC4B6;
    border-radius: 0.375rem;
    padding: 0.5rem;
    transition: all 0.2s;
}

input:hover, select:hover {
    border-color: #CBF3F0;
}

button {
    transition: all 0.2s;
}

button:hover {
    transform: translateY(-1px);
}
</style>

