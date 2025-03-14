<script setup lang="ts">
import { useForm } from '@inertiajs/inertia-vue3';
import AppLayout from "@/layouts/AppLayout.vue";
import { type BreadcrumbItem } from '@/types';

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
    <!-- Use the AppLayout component and pass breadcrumbs -->
    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="max-w-4xl mx-auto p-6 bg-white dark:bg-gray-800 rounded-lg shadow-md">
            <h1 class="text-2xl font-bold mb-6 text-gray-900 dark:text-gray-100">Create a New Pet</h1>
            <form @submit.prevent="submit" class="space-y-4">
                <!-- Form fields for pet details -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label for="name" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Name</label>
                        <input v-model="form.name" type="text" id="name" class="mt-1 block w-full border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm dark:bg-gray-700 dark:text-gray-100" />
                        <span v-if="form.errors.name" class="text-red-600 text-sm">{{ form.errors.name }}</span>
                    </div>

                    <div>
                        <label for="DOB" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Date of Birth</label>
                        <input v-model="form.DOB" type="date" id="DOB" class="mt-1 block w-full border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm dark:bg-gray-700 dark:text-gray-100" />
                        <span v-if="form.errors.DOB" class="text-red-600 text-sm">{{ form.errors.DOB }}</span>
                    </div>

                    <div>
                        <label for="type" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Type (e.g. Dog, Cat)</label>
                        <input v-model="form.type" type="text" id="type" class="mt-1 block w-full border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm dark:bg-gray-700 dark:text-gray-100" />
                        <span v-if="form.errors.type" class="text-red-600 text-sm">{{ form.errors.type }}</span>
                    </div>

                    <div>
                        <label for="species" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Species</label>
                        <input v-model="form.species" type="text" id="species" class="mt-1 block w-full border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm dark:bg-gray-700 dark:text-gray-100" />
                        <span v-if="form.errors.species" class="text-red-600 text-sm">{{ form.errors.species }}</span>
                    </div>

                    <div>
                        <label for="breed" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Breed</label>
                        <input v-model="form.breed" type="text" id="breed" class="mt-1 block w-full border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm dark:bg-gray-700 dark:text-gray-100" />
                        <span v-if="form.errors.breed" class="text-red-600 text-sm">{{ form.errors.breed }}</span>
                    </div>

                    <div>
                        <label for="neutered" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Neutered</label>
                        <input v-model="form.neutered" type="checkbox" id="neutered" class="mt-1 block dark:bg-gray-700 dark:border-gray-600" />
                        <span v-if="form.errors.neutered" class="text-red-600 text-sm">{{ form.errors.neutered }}</span>
                    </div>

                    <div>
                        <label for="color" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Color</label>
                        <input v-model="form.color" type="text" id="color" class="mt-1 block w-full border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm dark:bg-gray-700 dark:text-gray-100" />
                        <span v-if="form.errors.color" class="text-red-600 text-sm">{{ form.errors.color }}</span>
                    </div>

                    <div>
                        <label for="weight" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Weight</label>
                        <input v-model="form.weight" type="text" id="weight" class="mt-1 block w-full border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm dark:bg-gray-700 dark:text-gray-100" />
                        <span v-if="form.errors.weight" class="text-red-600 text-sm">{{ form.errors.weight }}</span>
                    </div>

                    <div>
                        <label for="height" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Height</label>
                        <input v-model="form.height" type="text" id="height" class="mt-1 block w-full border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm dark:bg-gray-700 dark:text-gray-100" />
                        <span v-if="form.errors.height" class="text-red-600 text-sm">{{ form.errors.height }}</span>
                    </div>

                    <div>
                        <label for="length" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Length</label>
                        <input v-model="form.length" type="text" id="length" class="mt-1 block w-full border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm dark:bg-gray-700 dark:text-gray-100" />
                        <span v-if="form.errors.length" class="text-red-600 text-sm">{{ form.errors.length }}</span>
                    </div>
                </div>

                <!-- Sex input -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Sex</label>
                    <div class="mt-1 flex items-center">
                        <input v-model="form.sex" type="radio" id="male" value="M" class="mr-2 dark:bg-gray-700 dark:border-gray-600" />
                        <label for="male" class="mr-4 dark:text-gray-300">Male</label>
                        <input v-model="form.sex" type="radio" id="female" value="F" class="mr-2 dark:bg-gray-700 dark:border-gray-600" />
                        <label for="female" class="dark:text-gray-300">Female</label>
                    </div>
                    <span v-if="form.errors.sex" class="text-red-600 text-sm">{{ form.errors.sex }}</span>
                </div>

                <button type="submit" class="w-full bg-indigo-600 text-white py-2 px-4 rounded-md shadow-sm hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 dark:bg-indigo-500 dark:hover:bg-indigo-600">Create Pet</button>
            </form>
        </div>
    </AppLayout>
</template>
