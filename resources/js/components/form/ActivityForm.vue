<template>
  <v-form ref="form" v-model="valid">
    <!-- Regular Activities Section -->
    <v-card class="mb-6">
      <v-card-title class="text-h6">Regular Activities</v-card-title>
      <v-card-text>
        <v-row v-for="(activity, index) in activities" :key="index" class="mb-4">
          <v-col cols="3">
            <v-autocomplete
              v-model="activity.name"
              :items="activityOptions"
              label="Activity Name"
              :rules="[v => !!v || 'Activity name is required']"
              required
            ></v-autocomplete>
          </v-col>
          <v-col cols="2">
            <v-text-field
              v-model="activity.duration_value"
              label="Duration Value"
              type="number"
              :rules="[v => !!v || 'Duration value is required']"
              required
            ></v-text-field>
          </v-col>
          <v-col cols="2">
            <v-select
              v-model="activity.duration_unit"
              :items="durationUnits"
              label="Duration Unit"
              :rules="[v => !!v || 'Duration unit is required']"
              required
            ></v-select>
          </v-col>
          <v-col cols="2">
            <v-text-field
              v-model="activity.frequency_value"
              label="Frequency Value"
              type="number"
              :rules="[v => !!v || 'Frequency value is required']"
              required
            ></v-text-field>
          </v-col>
          <v-col cols="2">
            <v-select
              v-model="activity.frequency_unit"
              :items="frequencyUnits"
              label="Frequency Unit"
              :rules="[v => !!v || 'Frequency unit is required']"
              required
            ></v-select>
          </v-col>
          <v-col cols="1" class="text-right">
            <v-btn
              v-if="activities.length > 1"
              color="error"
              icon
              @click="removeActivity(index)"
            >
              <v-icon>mdi-delete</v-icon>
            </v-btn>
          </v-col>
        </v-row>

        <v-btn
          color="#2EC4B6"
          @click="addActivity"
          class="mt-2"
        >
          Add Another Activity
        </v-btn>
      </v-card-text>
    </v-card>

      <!-- Daily Activity Log Section -->
      <v-card class="mb-6">
          <v-card-title class="text-h6">Daily Activity Log</v-card-title>
          <v-card-text>
              <!-- This will be replaced with the DailyActivityLog component -->
              <div class="text-center py-4">
                  <v-icon size="48" color="grey">mdi-calendar-text</v-icon>
                  <p class="text-grey mt-2">Daily Activity Log Component will be added here</p>
              </div>
          </v-card-text>
      </v-card>

    <!-- General Notes Section -->
    <v-card>
      <v-card-title class="text-h6">General Notes</v-card-title>
      <v-card-text>
        <v-textarea
          v-model="generalNotes"
          label="Notes about pet's activities"
          rows="4"
          auto-grow
        ></v-textarea>
      </v-card-text>
    </v-card>

    <!-- Add save button at the bottom -->
    <div class="mt-6 flex justify-end">
      <v-btn
        type="submit"
        color="#2EC4B6"
        :disabled="!valid"
        class="px-6"
        @click="submitForm"
      >
        Save Activity Information
      </v-btn>
    </div>
  </v-form>
</template>

<script setup lang="ts">
import { ref, watch, onMounted } from 'vue';
import { useForm } from '@inertiajs/inertia-vue3';
import axios from 'axios';
import type { VForm } from 'vuetify/components';

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
    initialData?: {
        activities: Activity[];
        notes: string;
    };
}>();

const valid = ref(false);
const form = ref<VForm | null>(null);

// Form data
const activities = ref<Activity[]>(props.initialData?.activities || [{
    name: '',
    duration_value: '',
    duration_unit: '',
    frequency_value: '',
    frequency_unit: ''
}]);
const generalNotes = ref(props.initialData?.notes || '');

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
    if (!form.value) return false;
    const { valid } = await form.value.validate();
    return valid;
};

const reset = () => {
    if (!form.value) return;
    form.value.reset();
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
        console.log('Save response:', response.data);
        
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
        console.log('Loading activities for pet:', props.petId);
        const response = await axios.get(route('activities.index', { pet: props.petId }));
        console.log('Received activities:', response.data);

        if (response.data && response.data.length > 0) {
            activities.value = response.data.map((activity: any) => ({
                name: activity.activity,
                duration_value: activity.duration_value?.toString() || '',
                duration_unit: activity.duration_unit || '',
                frequency_value: activity.frequency_value?.toString() || '',
                frequency_unit: activity.frequency_unit || ''
            }));
            generalNotes.value = response.data[0].notes || '';
            console.log('Mapped activities:', activities.value);
        } else {
            console.log('No activities found');
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

<style scoped>
.v-btn {
  text-transform: none !important;
}

:deep(.v-field--focused) {
  color: #FF9F1C !important;
}

:deep(.v-field--focused .v-label) {
  color: #FF9F1C !important;
}

:deep(.v-field--focused .v-field__outline) {
  border-color: #FF9F1C !important;
}
</style>
