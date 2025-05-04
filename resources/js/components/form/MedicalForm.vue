<template>
  <!-- <v-form ref="form" v-model="valid" @submit.prevent="submitForm">
    ... existing code ...
  </v-form> -->
  <div class="text-center py-8">
    <v-icon size="64" color="grey">mdi-medical-bag</v-icon>
    <h2 class="text-h4 mt-4">Coming Soon</h2>
    <p class="text-grey mt-2">Medical tracking functionality is under development</p>
  </div>
</template>

<script setup lang="ts">
/* Commenting out all the existing code
import { ref } from 'vue';
import { useForm } from '@inertiajs/vue3';
import type { VForm } from 'vuetify/components';
import { withDefaults } from 'vue';

const props = withDefaults(defineProps<{
    petId: number;
    petInfo?: Array<{
        key: string;
        value: string;
    }>;
    initialData?: {
        special_needs: Array<{
            name: string;
            affects: string;
            notes: string;
        }>;
        medications: Array<{
            name: string;
            prescribed_on: string;
            notes: string;
        }>;
        notes: string;
    };
}>(), {
    petInfo: () => [],
    initialData: () => ({
        special_needs: [],
        medications: [],
        notes: ''
    })
});

const valid = ref(false);
const form = ref<VForm | null>(null);

// Form data
const specialNeeds = ref(props.initialData?.special_needs || [{
  name: '',
  affects: '',
  notes: ''
}]);

const medications = ref(props.initialData?.medications || [{
  name: '',
  prescribed_on: '',
  notes: ''
}]);

const generalNotes = ref(props.initialData?.notes || '');

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

const removeSpecialNeed = (index: number) => {
  specialNeeds.value.splice(index, 1);
};

const addMedication = () => {
  medications.value.push({
    name: '',
    prescribed_on: '',
    notes: ''
  });
};

const removeMedication = (index: number) => {
  medications.value.splice(index, 1);
};

const submitForm = () => {
    const formData = {
        pet_id: props.petId,
        special_needs: specialNeeds.value,
        medications: medications.value,
        notes: generalNotes.value
    };

    useForm(formData).post(route('medical.store', { pet: props.petId }), {
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
    generalNotes.value = '';
};

// Expose methods to parent component
defineExpose({
    validate,
    reset,
    specialNeeds,
    medications,
    submitForm
});
*/
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
