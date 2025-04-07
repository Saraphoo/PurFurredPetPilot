<template>
  <v-form ref="form" v-model="valid">
    <!-- Regular Behaviors Section -->
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

    <!-- Daily Behavior Log Section -->
    <v-card class="mb-6">
      <v-card-title class="text-h6">Daily Behavior Log</v-card-title>
      <v-card-text>
        <!-- This will be replaced with the DailyBehaviorLog component -->
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
          label="Notes about pet's behaviors"
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
const selectedBehaviors = ref([]);
const behaviorNotes = ref('');
const generalNotes = ref('');

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

// Methods
const validate = async () => {
  const { valid } = await form.value.validate();
  return valid;
};

const reset = () => {
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
  behaviorNotes
});
</script> 