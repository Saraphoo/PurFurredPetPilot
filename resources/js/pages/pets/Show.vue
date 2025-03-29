<script setup lang="ts">
import {computed, defineProps, reactive, ref} from 'vue';
import {Button} from "@/components/ui/button";
import {router} from "@inertiajs/vue3";
import AddDetailsDrawer from '@/components/petProfile/addDetailsDrawer.vue';
import Chatbot from '@/components/chatbot/Chatbot.vue';
import ActivityDetails from '@/components/petProfile/details/ActivityDetails.vue';

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
    { key: 'exercise', label: 'Add Exercise Details', action: () => alert('Add Exercise Details button clicked') },
    { 
        key: 'activity', 
        label: 'Add Activity Details', 
        component: 'ActivityDetails',
        action: () => {
            addDetail({
                key: 'activity',
                value: 'Activity Log',
                component: 'ActivityDetails',
                componentData: {
                    lastActivity: '',
                    activityType: '',
                    duration: 0,
                    intensity: 'Moderate',
                    notes: '',
                    timestamp: new Date().toISOString(),
                }
            });
            closeDetailsDrawer();
        }
    },
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

// Function to add a new detail (update the existing function)
function addDetail(detail: { key: string; value: string; component?: string; componentData?: any }) {
    editablePetInfo.push(detail);
}

// Add new function to handle activity data updates
function handleActivityUpdate(key: string, newData: any) {
    const infoIndex = editablePetInfo.findIndex(info => info.key === key);
    if (infoIndex !== -1) {
        editablePetInfo[infoIndex].componentData = newData;
    }
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
            <span v-if="petInfo.length==0 && !isEditing" class="text text-gray-500">
                Edit pet's profile to add more information.
            </span>

            <!-- Display Mode -->
            <div v-if="!isEditing">
                <div v-for="(info, index) in editablePetInfo" :key="index">
                    <!-- Render ActivityDetails component if it exists -->
                    <ActivityDetails
                        v-if="info.key === 'activity'"
                        :is-editing="false"
                        :activity-data="info.componentData"
                    />
                    <!-- Render regular info if no component -->
                    <div v-else class="text-gray-600">
                        {{ info.key }}: {{ info.value }}
                    </div>
                </div>
            </div>

            <!-- Editable Mode -->
            <div v-else>
                <div v-for="(info, index) in editablePetInfo" :key="index">
                    <!-- Render ActivityDetails component in edit mode -->
                    <ActivityDetails
                        v-if="info.key === 'activity'"
                        :is-editing="true"
                        :activity-data="info.componentData"
                        @update:activity-data="(data) => handleActivityUpdate('activity', data)"
                    />
                    <!-- Render regular edit inputs if no component -->
                    <div v-else class="flex space-x-2 mb-2">
                        <input v-model="info.key" class="border rounded w-1/3" placeholder="Info Key" />
                        <input v-model="info.value" class="border rounded w-2/3" placeholder="Info Value" />
                    </div>
                </div>
                <Button class="bg-gray-400 text-white rounded" @click="openDetailsDrawer">
                    Add Pet Details
                </Button>
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
    <Chatbot/>
</template>



<style scoped>

</style>
