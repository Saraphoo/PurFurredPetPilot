<template>
  <v-form ref="form" v-model="valid" @submit.prevent="submitForm">
    <!-- Regular Meals Section -->
    <v-card class="mb-6">
      <v-card-title class="text-h6">Regular Meals</v-card-title>
      <v-card-text>
        <v-row v-for="(meal, index) in meals" :key="index" class="mb-4">
          <v-col cols="2">
            <v-text-field
              v-model="meal.feed_time"
              type="time"
              label="Feed Time"
              :rules="[v => !!v || 'Feed time is required']"
              required
            ></v-text-field>
          </v-col>
          <v-col cols="2">
            <v-autocomplete
              v-model="meal.food_name"
              :items="foodOptions"
              label="Food Name"
              :rules="[v => !!v || 'Food name is required']"
              required
            ></v-autocomplete>
          </v-col>
          <v-col cols="2">
            <v-autocomplete
              v-model="meal.brand"
              :items="brandOptions"
              label="Brand"
              :rules="[v => !!v || 'Brand is required']"
              required
            ></v-autocomplete>
          </v-col>
          <v-col cols="2">
            <v-select
              v-model="meal.meal_type"
              :items="mealTypes"
              label="Meal Type"
              :rules="[v => !!v || 'Meal type is required']"
              required
            ></v-select>
          </v-col>
          <v-col cols="2">
            <v-text-field
              v-model="meal.serving_value"
              label="Serving Value"
              type="number"
              :rules="[v => !!v || 'Serving value is required']"
              required
            ></v-text-field>
          </v-col>
          <v-col cols="2">
            <v-select
              v-model="meal.serving_unit"
              :items="servingUnits"
              label="Serving Unit"
              :rules="[v => !!v || 'Serving unit is required']"
              required
            ></v-select>
          </v-col>
          <v-col cols="12" class="text-right">
            <v-btn
              v-if="meals.length > 1"
              color="error"
              icon
              @click="removeMeal(index)"
            >
              <v-icon>mdi-delete</v-icon>
            </v-btn>
          </v-col>
        </v-row>

        <v-btn
          color="#2EC4B6"
          @click="addMeal"
          class="mt-2"
        >
          Add Another Meal
        </v-btn>
      </v-card-text>
    </v-card>

      <!-- Daily Meal Log Section -->
      <v-card class="mb-6">
          <v-card-title class="text-h6">Daily Meal Log</v-card-title>
          <v-card-text>
              <!-- This will be replaced with the DailyMealLog component -->
              <div class="text-center py-4">
                  <v-icon size="48" color="grey">mdi-calendar-text</v-icon>
                  <p class="text-grey mt-2">Daily Meal Log Component will be added here</p>
              </div>
          </v-card-text>
      </v-card>

    <!-- General Notes Section -->
    <v-card>
      <v-card-title class="text-h6">General Notes</v-card-title>
      <v-card-text>
        <v-textarea
          v-model="generalNotes"
          label="Notes about pet's meals"
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
        Save Meal Schedule
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
    initialData?: {
        meals: Array<{
            feed_time: string;
            food_name: string;
            brand: string;
            meal_type: string;
            serving_value: string;
            serving_unit: string;
        }>;
        notes: string;
    };
}>();

const valid = ref(false);
const form = ref<VForm | null>(null);

// Form data
const meals = ref(props.initialData?.meals || [{
  feed_time: '',
  food_name: '',
  brand: '',
  meal_type: '',
  serving_value: '',
  serving_unit: ''
}]);

const generalNotes = ref(props.initialData?.notes || '');

// Options
const foodOptions = [
  'Chicken & Rice',
  'Beef & Potato',
  'Salmon & Sweet Potato',
  'Turkey & Vegetables',
  'Lamb & Rice',
  'Duck & Pea',
  'Fish & Potato',
  'Venison & Sweet Potato'
];

const brandOptions = [
  'Royal Canin',
  'Hill\'s Science Diet',
  'Purina Pro Plan',
  'Blue Buffalo',
  'Merrick',
  'Wellness',
  'Orijen',
  'Acana'
];

const mealTypes = [
  'Dry Kibble',
  'Freeze Dried Kibble',
  'Raw',
  'Refrigerated',
  'Canned',
  'Dehydrated',
  'Fresh',
  'Homemade'
];

const servingUnits = [
  'cups',
  'ounces',
  'grams',
  'pounds',
  'milliliters',
  'liters'
];

// Methods
const addMeal = () => {
  meals.value.push({
    feed_time: '',
    food_name: '',
    brand: '',
    meal_type: '',
    serving_value: '',
    serving_unit: ''
  });
};

const removeMeal = (index: number) => {
  meals.value.splice(index, 1);
};

const submitForm = () => {
    const formData = {
        pet_id: props.petId,
        meals: meals.value,
        notes: generalNotes.value
    };

    useForm(formData).post(route('meals.store', { pet: props.petId }), {
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
    meals.value = [{
        feed_time: '',
        food_name: '',
        brand: '',
        meal_type: '',
        serving_value: '',
        serving_unit: ''
    }];
};

// Expose methods to parent component
defineExpose({
    validate,
    reset,
    meals,
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
