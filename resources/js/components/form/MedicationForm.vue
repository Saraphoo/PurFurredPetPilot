<template>
  <form ref="form" @submit.prevent="submitForm">
    <Card class="mb-6">
      <CardHeader>
        <CardTitle>Medication Details</CardTitle>
      </CardHeader>
      <CardContent>
        <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
          <div class="grid gap-2">
            <Label for="medication-name">Medication Name</Label>
            <Input id="medication-name" v-model="medicationName" required />
          </div>
          <div class="grid gap-2">
            <Label for="dosage">Dosage</Label>
            <Input id="dosage" v-model="dosage" required />
          </div>
          <div class="grid gap-2">
            <Label for="frequency">Frequency</Label>
            <Select id="frequency" v-model="frequency" required>
              <option value="" disabled>Select a frequency</option>
              <option v-for="option in frequencyOptions" :key="option" :value="option">{{ option }}</option>
            </Select>
          </div>
          <div class="grid gap-2">
            <Label for="time-of-day">Preferred Time of Day</Label>
            <Select id="time-of-day" v-model="timeOfDay" required>
              <option value="" disabled>Select a time of day</option>
              <option v-for="option in timeOfDayOptions" :key="option" :value="option">{{ option }}</option>
            </Select>
          </div>
          <div class="grid gap-2 md:col-span-2">
            <Label for="medication-notes">Medication Notes</Label>
            <Textarea id="medication-notes" v-model="notes" rows="4" />
          </div>
        </div>
      </CardContent>
    </Card>

    <Card>
      <CardHeader>
        <CardTitle>Prescription Information</CardTitle>
      </CardHeader>
      <CardContent>
        <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
          <div class="grid gap-2">
            <Label for="prescribing-vet">Prescribing Veterinarian</Label>
            <Input id="prescribing-vet" v-model="prescribingVet" />
          </div>
          <div class="grid gap-2">
            <Label for="pharmacy">Pharmacy</Label>
            <Input id="pharmacy" v-model="pharmacy" />
          </div>
          <div class="grid gap-2">
            <Label for="refill-date">Refill Date</Label>
            <Input id="refill-date" v-model="refillDate" type="date" />
          </div>
          <div class="grid gap-2">
            <Label for="expiration-date">Expiration Date</Label>
            <Input id="expiration-date" v-model="expirationDate" type="date" />
          </div>
        </div>
      </CardContent>
    </Card>

    <!-- Add save button at the bottom -->
    <div class="mt-6 flex justify-end">
      <Button type="submit" class="px-6"> Save Medication Information </Button>
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
import { useForm } from '@inertiajs/vue3';
import { ref } from 'vue';

const props = defineProps<{
    petId: number;
}>();

const form = ref<HTMLFormElement | null>(null);

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
    if (!form.value?.reportValidity()) return;

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
    return form.value?.reportValidity() ?? false;
};

const reset = () => {
    form.value?.reset();
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
