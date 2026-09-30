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
    <div class="bg-card text-card-foreground p-4 rounded-lg shadow">
        <h3 class="text-lg font-semibold mb-4">Activity Details</h3>

        <!-- View Mode -->
        <div v-if="!isEditing && activityData" class="space-y-2">
            <div class="grid grid-cols-2 gap-2">
                <p class="text-muted-foreground">Last Activity:</p>
                <p>{{ activityData.lastActivity }}</p>
                
                <p class="text-muted-foreground">Activity Type:</p>
                <p>{{ activityData.activityType }}</p>
                
                <p class="text-muted-foreground">Duration:</p>
                <p>{{ activityData.duration }} minutes</p>
                
                <p class="text-muted-foreground">Intensity:</p>
                <p>{{ activityData.intensity }}</p>
                
                <p class="text-muted-foreground">Notes:</p>
                <p>{{ activityData.notes }}</p>
                
                <p class="text-muted-foreground">Time:</p>
                <p>{{ new Date(activityData.timestamp).toLocaleString() }}</p>
            </div>
        </div>

        <!-- Edit Mode -->
        <form v-else @submit.prevent="handleSubmit" class="space-y-4">
            <div class="space-y-2">
                <label class="block">
                    <span class="text-foreground">Activity Type</span>
                    <select 
                        v-model="formData.activityType"
                        class="mt-1 block w-full rounded-md border-input shadow-sm"
                        required
                    >
                        <option value="">Select activity type</option>
                        <option v-for="type in activityTypes" :key="type" :value="type">
                            {{ type }}
                        </option>
                    </select>
                </label>

                <label class="block">
                    <span class="text-foreground">Duration (minutes)</span>
                    <input
                        type="number"
                        v-model="formData.duration"
                        class="mt-1 block w-full rounded-md border-input shadow-sm"
                        min="0"
                        required
                    />
                </label>

                <label class="block">
                    <span class="text-foreground">Intensity</span>
                    <select
                        v-model="formData.intensity"
                        class="mt-1 block w-full rounded-md border-input shadow-sm"
                        required
                    >
                        <option v-for="level in intensityLevels" :key="level" :value="level">
                            {{ level }}
                        </option>
                    </select>
                </label>

                <label class="block">
                    <span class="text-foreground">Notes</span>
                    <textarea
                        v-model="formData.notes"
                        class="mt-1 block w-full rounded-md border-input shadow-sm"
                        rows="3"
                    ></textarea>
                </label>
            </div>

            <div class="flex justify-end space-x-2">
                <Button type="submit">
                    Save Activity
                </Button>
            </div>
        </form>
    </div>
</template>

<style scoped>

</style>
