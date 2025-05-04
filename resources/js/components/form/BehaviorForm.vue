<template>
  <!-- <v-form ref="form" v-model="valid" @submit.prevent="submitForm">
    <v-card class="mb-6">
      <v-card-title class="text-h6">Regular Behaviors</v-card-title>
      <v-card-text>
        <v-row>
          <v-col cols="12" md="6">
            <v-checkbox
              v-for="behavior in behaviorOptions"
              :key="behavior"
              v-model="selectedBehaviors"
              :label="behavior"
              :value="behavior"
              class="mb-2"
            ></v-checkbox>
          </v-col>
          <v-col cols="12" md="6">
            <v-textarea
              v-model="behaviorNotes"
              label="Behavior Notes"
              rows="10"
              auto-grow
              placeholder="Add notes about your pet's behaviors..."
            ></v-textarea>
          </v-col>
        </v-row>
      </v-card-text>
    </v-card>

    <v-card class="mb-6">
      <v-card-title class="text-h6">Daily Behavior Log</v-card-title>
      <v-card-text>
        <div class="text-center py-4">
          <v-icon size="48" color="grey">mdi-calendar-text</v-icon>
          <p class="text-grey mt-2">Daily Behavior Log Component will be added here</p>
        </div>
      </v-card-text>
    </v-card>

    <v-card>
      <v-card-title class="text-h6">General Notes</v-card-title>
      <v-card-text>
        <v-textarea
          v-model="generalNotes"
          label="Notes about pet's behaviors"
          rows="4"
          auto-grow
        ></v-textarea>
      </v-card-text>
    </v-card>

    <div class="mt-6 flex justify-end">
      <v-btn
        type="submit"
        color="#2EC4B6"
        :disabled="!valid"
        class="px-6"
      >
        Save Behavior Information
      </v-btn>
    </div>
  </v-form> -->
  <div class="text-center py-8">
    <v-icon size="64" color="grey">mdi-paw</v-icon>
    <h2 class="text-h4 mt-4">Coming Soon</h2>
    <p class="text-grey mt-2">Behavior tracking functionality is under development</p>
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
        behaviors: string[];
        behavior_notes: string;
        general_notes: string;
    };
}>(), {
    petInfo: () => [],
    initialData: () => ({
        behaviors: [],
        behavior_notes: '',
        general_notes: ''
    })
});

const valid = ref(false);
const form = ref<VForm | null>(null);

// Form data
const selectedBehaviors = ref(props.initialData?.behaviors || []);
const behaviorNotes = ref(props.initialData?.behavior_notes || '');
const generalNotes = ref(props.initialData?.general_notes || '');

// Options
const behaviorOptions = [
  'Biting',
  'Kicking',
  'Spinning',
  'Chewing',
  'Digging',
  'Scratching',
  'Jumping',
  'Barking',
  'Meowing',
  'Hissing',
  'Growling',
  'Pacing',
  'Tail Chasing',
  'Feather Plucking',
  'Self-Mutilation',
  'Excessive Grooming',
  'Aggression',
  'Fearfulness',
  'Separation Anxiety',
  'Destructive Behavior'
];

const submitForm = () => {
    const formData = {
        pet_id: props.petId,
        behaviors: selectedBehaviors.value,
        behavior_notes: behaviorNotes.value,
        general_notes: generalNotes.value
    };

    useForm(formData).post(route('behaviors.store', { pet: props.petId }), {
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
    selectedBehaviors.value = [];
    behaviorNotes.value = '';
    generalNotes.value = '';
};

// Expose methods to parent component
defineExpose({
    validate,
    reset,
    selectedBehaviors,
    behaviorNotes,
    submitForm
});
*/
</script>
