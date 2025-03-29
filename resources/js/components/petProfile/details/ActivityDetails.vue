<script setup lang="ts">
import { ref, defineProps, defineEmits } from 'vue';
import { Button } from "@/components/ui/button";

const props = defineProps<{
    isEditing: boolean;
    activityData?: {
        lastActivity: string;
        activityType: string;
        duration: number;
        intensity: 'Low' | 'Moderate' | 'High';
        notes: string;
        timestamp: string;
    };
}>();

const emit = defineEmits(['update:activityData']);

// Create reactive form data with default values
const formData = ref(props.activityData || {
    lastActivity: '',
    activityType: '',
    duration: 0,
    intensity: 'Moderate',
    notes: '',
    timestamp: new Date().toISOString(),
});

const activityTypes = [
    'Walking',
    'Playing',
    'Swimming',
    'Climbing',
    'Running',
    'Burrowing',
    'Basking',
    'Exploring',
    'Enrichment Activity',
    'Other'
];

const intensityLevels = ['Low', 'Moderate', 'High'];

function handleSubmit() {
    emit('update:activityData', formData.value);
}
</script>

<template>
    <div class="bg-white p-4 rounded-lg shadow">
        <h3 class="text-lg font-semibold mb-4">Activity Details</h3>

        <!-- View Mode -->
        <div v-if="!isEditing && activityData" class="space-y-2">
            <div class="grid grid-cols-2 gap-2">
                <p class="text-gray-600">Last Activity:</p>
                <p>{{ activityData.lastActivity }}</p>
                
                <p class="text-gray-600">Activity Type:</p>
                <p>{{ activityData.activityType }}</p>
                
                <p class="text-gray-600">Duration:</p>
                <p>{{ activityData.duration }} minutes</p>
                
                <p class="text-gray-600">Intensity:</p>
                <p>{{ activityData.intensity }}</p>
                
                <p class="text-gray-600">Notes:</p>
                <p>{{ activityData.notes }}</p>
                
                <p class="text-gray-600">Time:</p>
                <p>{{ new Date(activityData.timestamp).toLocaleString() }}</p>
            </div>
        </div>

        <!-- Edit Mode -->
        <form v-else @submit.prevent="handleSubmit" class="space-y-4">
            <div class="space-y-2">
                <label class="block">
                    <span class="text-gray-700">Activity Type</span>
                    <select 
                        v-model="formData.activityType"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm"
                        required
                    >
                        <option value="">Select activity type</option>
                        <option v-for="type in activityTypes" :key="type" :value="type">
                            {{ type }}
                        </option>
                    </select>
                </label>

                <label class="block">
                    <span class="text-gray-700">Duration (minutes)</span>
                    <input
                        type="number"
                        v-model="formData.duration"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm"
                        min="0"
                        required
                    />
                </label>

                <label class="block">
                    <span class="text-gray-700">Intensity</span>
                    <select
                        v-model="formData.intensity"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm"
                        required
                    >
                        <option v-for="level in intensityLevels" :key="level" :value="level">
                            {{ level }}
                        </option>
                    </select>
                </label>

                <label class="block">
                    <span class="text-gray-700">Notes</span>
                    <textarea
                        v-model="formData.notes"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm"
                        rows="3"
                    ></textarea>
                </label>
            </div>

            <div class="flex justify-end space-x-2">
                <Button type="submit" class="bg-blue-500 text-white">
                    Save Activity
                </Button>
            </div>
        </form>
    </div>
</template>

<style scoped>

</style>
