<template>
  <v-form ref="form" v-model="valid">
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
          color="primary"
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
          color="primary"
          @click="addMedication"
          class="mt-2"
        >
          Add Another Medication
        </v-btn>
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
  </v-form>
</template>

<script setup>
import { ref } from 'vue';

const valid = ref(false);
const form = ref(null);

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

const validate = async () => {
  const { valid } = await form.value.validate();
  return valid;
};

const reset = () => {
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
  medications
});
</script> 