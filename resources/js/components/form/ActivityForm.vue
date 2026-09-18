<template>
  <form ref="form" @submit.prevent="submitForm">
    <!-- Regular Activities Section -->
    <Card class="mb-6">
      <CardHeader>
        <CardTitle>Regular Activities</CardTitle>
      </CardHeader>
      <CardContent class="space-y-4">
        <div v-for="(activity, index) in activities" :key="index" class="grid grid-cols-1 gap-4 md:grid-cols-12 md:items-end">
          <div class="grid gap-2 md:col-span-3">
            <Label :for="`activity-name-${index}`">Activity Name</Label>
            <Select :id="`activity-name-${index}`" v-model="activity.name" required>
              <option value="" disabled>Select an activity</option>
              <option v-for="option in activityOptions" :key="option" :value="option">{{ option }}</option>
            </Select>
          </div>
          <div class="grid gap-2 md:col-span-2">
            <Label :for="`duration-value-${index}`">Duration Value</Label>
            <Input :id="`duration-value-${index}`" v-model="activity.duration_value" type="number" required />
          </div>
          <div class="grid gap-2 md:col-span-2">
            <Label :for="`duration-unit-${index}`">Duration Unit</Label>
            <Select :id="`duration-unit-${index}`" v-model="activity.duration_unit" required>
              <option value="" disabled>Select a unit</option>
              <option v-for="option in durationUnits" :key="option" :value="option">{{ option }}</option>
            </Select>
          </div>
          <div class="grid gap-2 md:col-span-2">
            <Label :for="`frequency-value-${index}`">Frequency Value</Label>
            <Input :id="`frequency-value-${index}`" v-model="activity.frequency_value" type="number" required />
          </div>
          <div class="grid gap-2 md:col-span-2">
            <Label :for="`frequency-unit-${index}`">Frequency Unit</Label>
            <Select :id="`frequency-unit-${index}`" v-model="activity.frequency_unit" required>
              <option value="" disabled>Select a unit</option>
              <option v-for="option in frequencyUnits" :key="option" :value="option">{{ option }}</option>
            </Select>
          </div>
          <div class="flex justify-end md:col-span-1">
            <Button v-if="activities.length > 1" type="button" variant="destructive" size="icon" @click="removeActivity(index)">
              <Trash2 class="size-4" />
            </Button>
          </div>
        </div>

        <Button type="button" variant="secondary" @click="addActivity"> Add Another Activity </Button>
      </CardContent>
    </Card>

    <!-- Daily Activity Log Section -->
    <Card class="mb-6">
      <CardHeader>
        <CardTitle>Daily Activity Log</CardTitle>
      </CardHeader>
      <CardContent>
        <div class="py-4 text-center text-muted-foreground">
          <CalendarDays class="mx-auto size-12" />
          <p class="mt-2">Daily Activity Log Component will be added here</p>
        </div>
      </CardContent>
    </Card>

    <!-- General Notes Section -->
    <Card>
      <CardHeader>
        <CardTitle>General Notes</CardTitle>
      </CardHeader>
      <CardContent>
        <Textarea v-model="generalNotes" rows="4" placeholder="Notes about pet's activities" />
      </CardContent>
    </Card>

    <!-- Add save button at the bottom -->
    <div class="mt-6 flex justify-end">
      <Button type="submit" class="px-6"> Save Activity Information </Button>
    </div>
  </form>
</template>

<script setup lang="ts">
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Select } from '@/components/ui/select';
import { Textarea } from '@/components/ui/textarea';
import axios from 'axios';
import { CalendarDays, Trash2 } from 'lucide-vue-next';
import { onMounted, ref } from 'vue';

interface Activity {
    id?: number;
    name: string;
    duration_value: string;
    duration_unit: string;
    frequency_value: string;
    frequency_unit: string;
}

const props = defineProps<{
    petId: number;
}>();

const form = ref<HTMLFormElement | null>(null);

// Form data
const activities = ref<Activity[]>([{
    name: '',
    duration_value: '',
    duration_unit: '',
    frequency_value: '',
    frequency_unit: ''
}]);
const generalNotes = ref('');

// Options
const activityOptions = [
    'Walking',
    'Playing',
    'Training',
    'Grooming',
    'Feeding',
    'Socializing',
    'Exercise',
    'Resting'
];

const durationUnits = [
    'minutes',
    'hours',
    'days'
];

const frequencyUnits = [
    'hour',
    'day',
    'week',
    'month',
    'year'
];

// Methods
const addActivity = () => {
    activities.value.push({
        name: '',
        duration_value: '',
        duration_unit: '',
        frequency_value: '',
        frequency_unit: ''
    });
};

const removeActivity = async (index: number) => {
    const activity = activities.value[index];
    if (activity.id) {
        try {
            await axios.delete(route('activities.destroy', { activity: activity.id }));
        } catch (error) {
            console.error('Error deleting activity:', error);
            return;
        }
    }
    activities.value.splice(index, 1);
};

const validate = async () => {
    return form.value?.reportValidity() ?? false;
};

const reset = () => {
    form.value?.reset();
    activities.value = [{
        name: '',
        duration_value: '',
        duration_unit: '',
        frequency_value: '',
        frequency_unit: ''
    }];
    generalNotes.value = '';
};

const submitForm = async () => {
    if (!await validate()) return;

    const formData = {
        pet_id: props.petId,
        activities: activities.value.map((activity: Activity) => ({
            name: activity.name,
            duration_value: parseInt(activity.duration_value),
            duration_unit: activity.duration_unit,
            frequency_value: parseInt(activity.frequency_value),
            frequency_unit: activity.frequency_unit
        })),
        notes: generalNotes.value
    };

    try {
        const response = await axios.post(route('activities.store', { pet: props.petId }), formData);

        // Update activities with all activities for the pet
        if (response.data && response.data.length > 0) {
            activities.value = response.data.map((activity: any) => ({
                id: activity.id,
                name: activity.activity,
                duration_value: activity.duration_value?.toString() || '',
                duration_unit: activity.duration_unit || '',
                frequency_value: activity.frequency_value?.toString() || '',
                frequency_unit: activity.frequency_unit || ''
            }));
            generalNotes.value = response.data[0].notes || '';
        }
    } catch (error) {
        console.error('Error saving activities:', error);
    }
};

// Load saved activities
const loadActivities = async () => {
    try {
        const response = await axios.get(route('activities.index', { pet: props.petId }));

        if (response.data && response.data.length > 0) {
            activities.value = response.data.map((activity: any) => ({
                name: activity.activity,
                duration_value: activity.duration_value?.toString() || '',
                duration_unit: activity.duration_unit || '',
                frequency_value: activity.frequency_value?.toString() || '',
                frequency_unit: activity.frequency_unit || ''
            }));
            generalNotes.value = response.data[0].notes || '';
        } else {
            // Reset to default state if no activities found
            activities.value = [{
                name: '',
                duration_value: '',
                duration_unit: '',
                frequency_value: '',
                frequency_unit: ''
            }];
            generalNotes.value = '';
        }
    } catch (error) {
        console.error('Error loading activities:', error);
    }
};

// Load activities when component is mounted
onMounted(() => {
    loadActivities();
});

// Expose methods to parent component
defineExpose({
    validate,
    reset,
    activities,
    submitForm
});
</script>
