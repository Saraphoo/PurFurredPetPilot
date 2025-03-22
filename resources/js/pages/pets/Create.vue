<script setup lang="ts">
import { useForm } from '@inertiajs/inertia-vue3';
import AppLayout from "@/layouts/AppLayout.vue";
import { type BreadcrumbItem } from '@/types';
import {computed, ref} from "vue";

// Initialize the form with default values
const form = useForm({
    name: '',
    DOB: '',
    type: '',
    species: '',
    breed: '',
    neutered: false,
    color: '',
    weight: '',
    height: '',
    length: '',
    sex: '',
});


// Dropdown options for pet types and species
const petTypes = ref([
    { label: 'Dog', value: 'dog' },
    { label: 'Cat', value: 'cat' },
    { label: 'Bird', value: 'bird' },
    { label: 'Reptile', value: 'reptile' },
    { label: 'Amphibian', value: 'amphibian' },
    { label: 'Rodent', value: 'rodent' },
    { label: 'Rabbit', value: 'rabbit' },
    { label: 'Fish', value: 'fish' },
    { label: 'Invertebrate', value: 'invertebrate' },
    { label: 'Other', value: 'other' },
]);

const speciesOptions = ref({
    dog: ['Golden Retriever', 'Bulldog', 'Poodle'],
    cat: ['Siamese', 'Persian', 'Maine Coon'],
    bird: ['Parrot', 'Canary', 'Sparrow'],
    reptile: ['Iguana', 'Gecko', 'Snake'],
    amphibian:['frog','turtle'],
    rodent: ['mouse','hamster', 'guinea pig', 'gerbil'],
    rabbit: ['English Spot', 'Rex', 'mini rex', 'holland lop'],
    fish: ['guppy', 'betta', 'Cory Catfish'],
    invertebrate: ['snail', 'hermit crab', 'shrimp', 'spider'],
    other: ['exotic']
});

// Computed property to get species based on selected type
const availableSpecies = computed(() => {
    return speciesOptions.value[form.type] || [];
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
    flash: Record<string, string>;
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

                    <!-- Pet Type Dropdown -->
                    <div>
                        <label for="type" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Type</label>
                        <select v-model="form.type" id="type" class="mt-1 block w-full">
                            <option value="" disabled>Select Type</option>
                            <option v-for="type in petTypes" :key="type.value" :value="type.value">{{ type.label }}</option>
                        </select>
                        <span v-if="form.errors.type" class="text-red-600 text-sm">{{ form.errors.type }}</span>
                    </div>

                    <!-- Species Dropdown (Dependent on Type) -->
                    <div>
                        <label for="species" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Species</label>
                        <select v-model="form.species" id="species" class="mt-1 block w-full" :disabled="!form.type">
                            <option value="" disabled>Select Species</option>
                            <option v-for="species in availableSpecies" :key="species">{{ species }}</option>
                        </select>
                        <span v-if="form.errors.species" class="text-red-600 text-sm">{{ form.errors.species }}</span>
                    </div>

                    <!-- Sex Dropdown -->
                    <div>
                        <label for="sex" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Sex</label>
                        <select v-model="form.sex" id="sex" class="mt-1 block w-full">
                            <option value="" disabled>Select Sex</option>
                            <option value="M">Male</option>
                            <option value="F">Female</option>
                        </select>
                        <span v-if="form.errors.sex" class="text-red-600 text-sm">{{ form.errors.sex }}</span>
                    </div>

                    <!-- Neutered Checkbox -->
                    <div>
                        <label for="neutered" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Neutered</label>
                        <input v-model="form.neutered" type="checkbox" id="neutered" class="mt-1 block" />
                        <span v-if="form.errors.neutered" class="text-red-600 text-sm">{{ form.errors.neutered }}</span>
                    </div>

                    <!-- Other fields (e.g., breed, color, etc.) go here -->
                </div>

                <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-md shadow">Create Pet</button>
            </form>
        </div>
    </AppLayout>
</template>

