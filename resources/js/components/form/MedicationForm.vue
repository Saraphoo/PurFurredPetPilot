<template>
  <v-form ref="form" v-model="valid" @submit.prevent="submitForm">
    <v-card class="mb-6">
      <v-card-title class="text-h6">Medication Details</v-card-title>
      <v-card-text>
        <v-row>
          <v-col cols="6">
            <v-text-field
              v-model="medicationName"
              label="Medication Name"
              :rules="[v => !!v || 'Medication name is required']"
              required
              color="#2EC4B6"
              class="custom-input"
            ></v-text-field>
          </v-col>
          <v-col cols="6">
            <v-text-field
              v-model="dosage"
              label="Dosage"
              :rules="[v => !!v || 'Dosage is required']"
              required
              color="#2EC4B6"
              class="custom-input"
            ></v-text-field>
          </v-col>
          <v-col cols="6">
            <v-select
              v-model="frequency"
              :items="frequencyOptions"
              label="Frequency"
              :rules="[v => !!v || 'Frequency is required']"
              required
              color="#2EC4B6"
              class="custom-input"
            ></v-select>
          </v-col>
          <v-col cols="6">
            <v-select
              v-model="timeOfDay"
              :items="timeOfDayOptions"
              label="Preferred Time of Day"
              :rules="[v => !!v || 'Time of day is required']"
              required
              color="#2EC4B6"
              class="custom-input"
            ></v-select>
          </v-col>
          <v-col cols="12">
            <v-textarea
              v-model="notes"
              label="Medication Notes"
              rows="4"
              auto-grow
              color="#2EC4B6"
              class="custom-input"
            ></v-textarea>
          </v-col>
        </v-row>
      </v-card-text>
    </v-card>

    <v-card>
      <v-card-title class="text-h6">Prescription Information</v-card-title>
      <v-card-text>
        <v-row>
          <v-col cols="6">
            <v-text-field
              v-model="prescribingVet"
              label="Prescribing Veterinarian"
              color="#2EC4B6"
              class="custom-input"
            ></v-text-field>
          </v-col>
          <v-col cols="6">
            <v-text-field
              v-model="pharmacy"
              label="Pharmacy"
              color="#2EC4B6"
              class="custom-input"
            ></v-text-field>
          </v-col>
          <v-col cols="6">
            <v-text-field
              v-model="refillDate"
              label="Refill Date"
              type="date"
              color="#2EC4B6"
              class="custom-input"
            ></v-text-field>
          </v-col>
          <v-col cols="6">
            <v-text-field
              v-model="expirationDate"
              label="Expiration Date"
              type="date"
              color="#2EC4B6"
              class="custom-input"
            ></v-text-field>
          </v-col>
        </v-row>
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
        Save Medication Information
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
const medicationName = ref('');
const dosage = ref('');
const frequency = ref('');
const timeOfDay = ref('');
const notes = ref('');
const prescribingVet = ref('');
const pharmacy = ref('');
const refillDate = ref('');
const expirationDate = ref('');

// Options
const frequencyOptions = [
  'Once daily',
  'Twice daily',
  'Three times daily',
  'Four times daily',
  'Every other day',
  'Weekly',
  'As needed'
];

const timeOfDayOptions = [
  'Morning',
  'Afternoon',
  'Evening',
  'Night',
  'With meals',
  'Flexible'
];

const submitForm = () => {
    const formData = {
        pet_id: props.petId,
        medication_name: medicationName.value,
        dosage: dosage.value,
        frequency: frequency.value,
        time_of_day: timeOfDay.value,
        notes: notes.value,
        prescribing_vet: prescribingVet.value,
        pharmacy: pharmacy.value,
        refill_date: refillDate.value,
        expiration_date: expirationDate.value
    };

    useForm(formData).post(route('medications.store', { pet: props.petId }), {
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
    medicationName.value = '';
    dosage.value = '';
    frequency.value = '';
    timeOfDay.value = '';
    notes.value = '';
    prescribingVet.value = '';
    pharmacy.value = '';
    refillDate.value = '';
    expirationDate.value = '';
};

// Expose methods to parent component
defineExpose({
    validate,
    reset,
    medicationName,
    dosage,
    frequency,
    timeOfDay,
    notes,
    prescribingVet,
    pharmacy,
    refillDate,
    expirationDate,
    submitForm
});
</script>

<style scoped>
.custom-input :deep(.v-field--focused) {
  border-color: #FF9F1C !important;
}

.custom-input :deep(.v-field__outline) {
  border-color: #2EC4B6 !important;
}

.custom-input :deep(.v-field--focused .v-field__outline) {
  border-color: #FF9F1C !important;
}

.custom-input :deep(.v-field--focused .v-label) {
  color: #FF9F1C !important;
}

.v-btn {
  text-transform: none !important;
}
</style> 