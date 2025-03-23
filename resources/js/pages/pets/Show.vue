<script setup lang="ts">
import {computed, defineProps, reactive, ref} from 'vue';
import {Button} from "@/components/ui/button";
import {router} from "@inertiajs/vue3";
import AddDetailsDrawer from '@/components/petProfile/addDetailsDrawer.vue';

// Define props for pet
const props = defineProps<{
    pet: {
        id: number;
        name: string;
        species: string;
        petImage: string | null;
        type: string;
        DOB: string | null;
    };
    petInfo: Array<{ key: string, value: string }>
}>();

// Drawer visibility state
const isDrawerVisible = ref(false);
// Toggle between view and edit mode
const isEditing = ref(false);

// Create a reactive copy of the pet and petInfo for editing
const editablePet = reactive({ ...props.pet });
const editablePetInfo = reactive([...props.petInfo]);

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

// Computed function to handle the display mode for pet info
const displayPetInfo = computed(() => {
    if (!isEditing.value) {
        // View Mode: plain text display of key-value pairs
        return props.petInfo;
    }
    // Edit Mode: editable form fields
    return editablePetInfo;
});

// Possible button options for adding details
const possibleDetails = ref([
    { key: 'meal', label: 'Add Meal Details', action: () => alert('Add Meal Details button clicked') },
    { key: 'habitat', label: 'Add Habitat Details', action: () => alert('Add Habitat Details button clicked') },
    { key: 'health', label: 'Add Health Details', action: () => alert('Add Health Details button clicked') },
    { key: 'behavior', label: 'Add Behavior Details', action: () => alert('Add Behavior Details button clicked') },
    { key: 'exercise', label: 'Add Exercise Details', action: () => alert('Add Exercise Details button clicked') }
]);

// Computed to filter out the buttons that are already present in `petInfo`
const drawerButtons = computed(() => {
    // Filter out possible details where the key is already present in petInfo
    return possibleDetails.value.filter(detail => !(detail.key in props.petInfo));
});

function toggleEdit() {
    isEditing.value = !isEditing.value;
}

// Function to show the drawer
function openDetailsDrawer() {
    isDrawerVisible.value = true;
}

// Function to close the drawer
function closeDetailsDrawer() {
    isDrawerVisible.value = false;
}

function cancelEdit() {
    isEditing.value = false;
    Object.assign(editablePet, props.pet);
    Object.assign(editablePetInfo, [...props.petInfo]);
}

function submitEdit() {
    // Handle form submission (API or database call) here
    console.log('Edited Pet Data:', editablePet);
    console.log('Edited Additional Pet Info:', editablePetInfo);
    isEditing.value = false;
}

// Function to add a new detail (triggered from the drawer)
function addDetail(detail: { key: string; value: string }) {
    editablePetInfo.push(detail);
}

function goToDashboard() {
    router.get(`/dashboard`);
}


</script>

<template>
    <div class="p-6 max-w-6xl mx-auto bg-white rounded-xl shadow-md space-y-4">
        <Button @click="goToDashboard()" class="text-white bg-gray-400 rounded px-2">Back to Dashboard</Button>
        <Button @click="toggleEdit()" class="text-white bg-gray-400 rounded px-2">{{ isEditing ? 'Cancel' : 'Edit' }}</Button>

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


        <!-- Additional Information -->
        <div class="mt-4 p-4 bg-gray-50 rounded-lg">
            <h2 class="text-xl font-semibold text-gray-700">Additional Information</h2>
            <span v-if="petInfo.length==0 && !isEditing" class="text text-gray-500">Edit pet's profile to add more information.</span>

            <!-- Display Mode -->
            <ul v-if="!isEditing">
                <li v-for="(info, index) in editablePetInfo" :key="index" class="text-gray-600">
                    {{ info.key }}: {{ info.value }}
                </li>
            </ul>

            <!-- Editable Mode -->
            <div v-else>
                <ul>
                    <li v-for="(info, index) in editablePetInfo" :key="index" class="flex space-x-2 mb-2">
                        <input v-model="info.key" class="border rounded w-1/3" placeholder="Info Key" />
                        <input v-model="info.value" class="border rounded w-2/3" placeholder="Info Value" />
                    </li>
                </ul>
                <Button class="bg-gray-400 text-white rounded" @click="openDetailsDrawer" >Add Pet Details</Button>
            </div>
        </div>

        <!-- Submit and Cancel buttons for edit mode -->
        <div v-if="isEditing" class="flex justify-end space-x-2">
            <Button @click="submitEdit" class="bg-green-500 text-white rounded">Save</Button>
            <Button @click="cancelEdit" class="bg-red-500 text-white rounded">Cancel</Button>
        </div>
        <!-- Drawer Component -->
        <AddDetailsDrawer
            v-if="isDrawerVisible"
            :buttons="drawerButtons"
            @close="closeDetailsDrawer"
        />
    </div>
</template>



<style scoped>

</style>
