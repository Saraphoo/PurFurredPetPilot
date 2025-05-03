<template>
  <v-form ref="form" v-model="valid" @submit.prevent="submitForm">
    <!-- Special Needs Section -->
    <v-card class="mb-6">
      <v-card-title class="text-h6">Special Needs</v-card-title>
      <v-card-text>
        <v-row v-for="(need, index) in specialNeeds" :key="index" class="mb-4">
          <v-col cols="4">
            <v-autocomplete
              v-model="need.name"
              :items="specialNeedOptions"
              label="Special Need Name"
              :rules="[v => !!v || 'Special need name is required']"
              required
            ></v-autocomplete>
          </v-col>
          <v-col cols="4">
            <v-autocomplete
              v-model="need.affects"
              :items="affectOptions"
              label="Affects"
              :rules="[v => !!v || 'Affects is required']"
              required
            ></v-autocomplete>
          </v-col>
          <v-col cols="3">
            <v-text-field
              v-model="need.notes"
              label="Notes"
            ></v-text-field>
          </v-col>
          <v-col cols="1" class="text-right">
            <v-btn
              v-if="specialNeeds.length > 1"
              color="error"
              icon
              @click="removeSpecialNeed(index)"
            >
              <v-icon>mdi-delete</v-icon>
            </v-btn>
          </v-col>
        </v-row>

        <v-btn
          color="#2EC4B6"
          @click="addSpecialNeed"
          class="mt-2"
        >
          Add Another Special Need
        </v-btn>
      </v-card-text>
    </v-card>

    <!-- Medications Section -->
    <v-card class="mb-6">
      <v-card-title class="text-h6">Medications</v-card-title>
      <v-card-text>
        <v-row v-for="(medication, index) in medications" :key="index" class="mb-4">
          <v-col cols="4">
            <v-autocomplete
              v-model="medication.name"
              :items="medicationOptions"
              label="Medication Name"
              :rules="[v => !!v || 'Medication name is required']"
              required
            ></v-autocomplete>
          </v-col>
          <v-col cols="3">
            <v-text-field
              v-model="medication.prescribed_on"
              type="date"
              label="Prescribed On"
              :rules="[v => !!v || 'Prescription date is required']"
              required
            ></v-text-field>
          </v-col>
          <v-col cols="4">
            <v-text-field
              v-model="medication.notes"
              label="Notes"
            ></v-text-field>
          </v-col>
          <v-col cols="1" class="text-right">
            <v-btn
              v-if="medications.length > 1"
              color="error"
              icon
              @click="removeMedication(index)"
            >
              <v-icon>mdi-delete</v-icon>
            </v-btn>
          </v-col>
        </v-row>

        <v-btn
          color="#2EC4B6"
          @click="addMedication"
          class="mt-2"
        >
          Add Another Medication
        </v-btn>
      </v-card-text>
    </v-card>

      <!-- Daily Medication Log Section -->
      <v-card class="mb-6">
          <v-card-title class="text-h6">Daily Medication Log</v-card-title>
          <v-card-text>
              <!-- This will be replaced with the DailyMedicaitonLog component -->
              <div class="text-center py-4">
                  <v-icon size="48" color="grey">mdi-calendar-text</v-icon>
                  <p class="text-grey mt-2">Daily Behavior Log Component will be added here</p>
              </div>
          </v-card-text>
      </v-card>

    <!-- General Notes Section -->
    <v-card>
      <v-card-title class="text-h6">General Notes</v-card-title>
      <v-card-text>
        <v-textarea
          v-model="generalNotes"
          label="Notes about pet's medical needs"
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
      >
        Save Medical Information
      </v-btn>
    </div>
  </v-form>
</template>

<script setup lang="ts">
import { ref } from 'vue';
import { useForm } from '@inertiajs/vue3';
import type { VForm } from 'vuetify/components';

const props = defineProps<{
    petId: number;
}>();

const valid = ref(false);
const form = ref<VForm | null>(null);

// Form data
const specialNeeds = ref([{
  name: '',
  affects: '',
  notes: ''
}]);

const medications = ref([{
  name: '',
  prescribed_on: '',
  notes: ''
}]);

const generalNotes = ref('');

// Options
const specialNeedOptions = [
  'Allergies',
  'Arthritis',
  'Diabetes',
  'Heart Condition',
  'Kidney Disease',
  'Liver Disease',
  'Seizures',
  'Thyroid Issues'
];

const affectOptions = [
  'Diet',
  'Exercise',
  'Medication',
  'Environment',
  'Behavior',
  'Grooming',
  'Socialization',
  'Sleep'
];

const medicationOptions = [
  'Apoquel',
  'Cephalexin',
  'Gabapentin',
  'Prednisone',
  'Rimadyl',
  'Tramadol',
  'Vetmedin',
  'Zyrtec'
];

// Methods
const addSpecialNeed = () => {
  specialNeeds.value.push({
    name: '',
    affects: '',
    notes: ''
  });
};

const removeSpecialNeed = (index) => {
  specialNeeds.value.splice(index, 1);
};

const addMedication = () => {
  medications.value.push({
    name: '',
    prescribed_on: '',
    notes: ''
  });
};

const removeMedication = (index) => {
  medications.value.splice(index, 1);
};

const submitForm = () => {
    const formData = {
        pet_id: props.petId,
        special_needs: specialNeeds.value,
        medications: medications.value,
        notes: generalNotes.value
    };

    useForm(formData).post(route('medical.store'), {
        preserveScroll: true,
        onSuccess: () => {
            reset();
        }
    });
};

const validate = async () => {
    if (!form.value) return false;
    const { valid } = await form.value.validate();
    return valid;
};

const reset = () => {
    if (!form.value) return;
    form.value.reset();
    specialNeeds.value = [{
        name: '',
        affects: '',
        notes: ''
    }];
    medications.value = [{
        name: '',
        prescribed_on: '',
        notes: ''
    }];
};

// Expose methods to parent component
defineExpose({
    validate,
    reset,
    specialNeeds,
    medications,
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
