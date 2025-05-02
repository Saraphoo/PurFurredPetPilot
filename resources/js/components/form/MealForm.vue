<template>
  <v-form ref="form" v-model="valid">
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
  </v-form>
</template>

<script setup lang="ts">
import { ref } from 'vue';

const valid = ref(false);
const form = ref(null);

// Form data
const meals = ref([{
  feed_time: '',
  food_name: '',
  brand: '',
  meal_type: '',
  serving_value: '',
  serving_unit: ''
}]);

const generalNotes = ref('');

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

const removeMeal = (index) => {
  meals.value.splice(index, 1);
};

const validate = async () => {
  const { valid } = await form.value.validate();
  return valid;
};

const reset = () => {
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
  meals
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
