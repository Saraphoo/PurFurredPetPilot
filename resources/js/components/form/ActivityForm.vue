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
  </v-form>
</template>

<script setup lang="ts">
import { ref, watch, onMounted } from 'vue';
import { useForm } from '@inertiajs/inertia-vue3';
import axios from 'axios';

const props = defineProps({
    petId: {
        type: Number,
        required: true
    }
});

const valid = ref(false);
const form = ref(null);

// Form data
const activities = ref([{
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

const removeActivity = (index) => {
    activities.value.splice(index, 1);
};

const validate = async () => {
    const { valid } = await form.value.validate();
    return valid;
};

const reset = () => {
    form.value.reset();
    activities.value = [{
        name: '',
        duration_value: '',
        duration_unit: '',
        frequency_value: '',
        frequency_unit: ''
    }];
};

// Load saved activities
const loadActivities = async () => {
    try {
        console.log('Loading activities for pet:', props.petId);
        const response = await axios.get(`/pets/${props.petId}/activities`);
        console.log('Received activities:', response.data);

        if (response.data && response.data.length > 0) {
            activities.value = response.data.map(activity => ({
                name: activity.activity,
                duration_value: activity.duration_value?.toString() || '',
                duration_unit: activity.duration_unit || '',
                frequency_value: activity.frequency_value?.toString() || '',
                frequency_unit: activity.frequency_unit || ''
            }));
            console.log('Mapped activities:', activities.value);
        } else {
            console.log('No activities found');
        }
    } catch (error) {
        console.error('Error loading activities:', error);
    }
};

// Autosave functionality
const autosave = async () => {
    if (!await validate()) return;

    try {
        console.log('Saving activities:', activities.value);
        const response = await axios.post(`/pets/${props.petId}/activities`, {
            activities: activities.value
        });
        console.log('Save response:', response.data);
    } catch (error) {
        console.error('Error saving activities:', error);
    }
};

// Watch for changes and trigger autosave
watch(activities, () => {
    autosave();
}, { deep: true });

// Load activities when component is mounted
onMounted(() => {
    loadActivities();
});

// Expose methods to parent component
defineExpose({
    validate,
    reset,
    activities
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
