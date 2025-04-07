<template>
  <v-form ref="form" v-model="valid">
    <!-- Daily Living Space Section -->
    <v-card class="mb-6">
      <v-card-title class="text-h6">Daily Living Space</v-card-title>
      <v-card-text>
        <v-row>
          <v-col cols="6">
            <v-text-field
              v-model="totalSpaceValue"
              label="Total Space Value"
              type="number"
              :rules="[v => !!v || 'Total space value is required']"
              required
            ></v-text-field>
          </v-col>
          <v-col cols="6">
            <v-select
              v-model="totalSpaceUnit"
              :items="spaceUnits"
              label="Space Unit"
              :rules="[v => !!v || 'Space unit is required']"
              required
            ></v-select>
          </v-col>
        </v-row>

        <v-row>
          <v-col cols="12">
            <v-select
              v-model="housingType"
              :items="housingTypes"
              label="Housing Type"
              :rules="[v => !!v || 'Housing type is required']"
              required
            ></v-select>
          </v-col>
        </v-row>

        <v-row>
          <v-col cols="12">
            <v-select
              v-model="flooringType"
              :items="flooringTypes"
              label="Flooring Type"
              :rules="[v => !!v || 'Flooring type is required']"
              required
            ></v-select>
          </v-col>
        </v-row>

        <v-row>
          <v-col cols="12">
            <v-select
              v-model="beddingType"
              :items="beddingTypes"
              label="Bedding Type"
              :rules="[v => !!v || 'Bedding type is required']"
              required
            ></v-select>
          </v-col>
        </v-row>
      </v-card-text>
    </v-card>

    <!-- Housing Accessories Section -->
    <v-card class="mb-6">
      <v-card-title class="text-h6">Housing Accessories</v-card-title>
      <v-card-text>
        <v-row v-for="(accessory, index) in accessories" :key="index" class="mb-4">
          <v-col cols="3">
            <v-autocomplete
              v-model="accessory.type"
              :items="accessoryTypes"
              label="Accessory Type"
              :rules="[v => !!v || 'Accessory type is required']"
              required
            ></v-autocomplete>
          </v-col>
          <v-col cols="2">
            <v-text-field
              v-model="accessory.name"
              label="Accessory Name"
            ></v-text-field>
          </v-col>
          <v-col cols="2">
            <v-text-field
              v-model="accessory.size"
              label="Accessory Size"
            ></v-text-field>
          </v-col>
          <v-col cols="2">
            <v-autocomplete
              v-model="accessory.brand"
              :items="brandOptions"
              label="Brand"
            ></v-autocomplete>
          </v-col>
          <v-col cols="2">
            <v-select
              v-model="accessory.material"
              :items="materialOptions"
              label="Material"
            ></v-select>
          </v-col>
          <v-col cols="1" class="text-right">
            <v-btn
              v-if="accessories.length > 1"
              color="error"
              icon
              @click="removeAccessory(index)"
            >
              <v-icon>mdi-delete</v-icon>
            </v-btn>
          </v-col>
        </v-row>

        <v-btn
          color="primary"
          @click="addAccessory"
          class="mt-2"
        >
          Add Another Accessory
        </v-btn>
      </v-card-text>
    </v-card>

    <!-- General Notes Section -->
    <v-card>
      <v-card-title class="text-h6">General Notes</v-card-title>
      <v-card-text>
        <v-textarea
          v-model="generalNotes"
          label="Notes about pet's housing"
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
const totalSpaceValue = ref('');
const totalSpaceUnit = ref('');
const housingType = ref('');
const flooringType = ref('');
const beddingType = ref('');
const accessories = ref([{
  type: '',
  name: '',
  size: '',
  brand: '',
  material: '',
  notes: ''
}]);
const generalNotes = ref('');

// Options
const spaceUnits = [
  'inches',
  'feet',
  'yards',
  'acres',
  'gallons',
  'liters',
  'cubic feet',
  'cubic meters'
];

const housingTypes = [
  'Free Roam Indoors',
  'Free Roam Outdoors',
  'Cage',
  'Aquarium',
  'Terrarium',
  'Aviary',
  'Kennel',
  'Hutch'
];

const flooringTypes = [
  'Wire',
  'Solid',
  'Dirt',
  'Wood',
  'Glass',
  'Metal',
  'Plastic',
  'Concrete'
];

const beddingTypes = [
  'Wood Shavings',
  'Paper Shavings',
  'Fleece',
  'Straw',
  'Sand',
  'Bioactive Substrate',
  'Pine Pellets',
  'Aspen Shavings'
];

const accessoryTypes = [
  'House',
  'Bed',
  'Feeder',
  'Bowl',
  'Water Bottle',
  'Hay Feeder',
  'Hide',
  'Tunnel',
  'Perch',
  'Litter Box'
];

const brandOptions = [
  'Kaytee',
  'Oxbow',
  'Ware Manufacturing',
  'Living World',
  'Midwest Homes',
  'Prevue Pet',
  'Ferplast',
  'Savic'
];

const materialOptions = [
  'Wood',
  'Plastic',
  'Metal',
  'Glass',
  'Ceramic',
  'Fabric',
  'Natural Fiber',
  'Synthetic'
];

// Methods
const addAccessory = () => {
  accessories.value.push({
    type: '',
    name: '',
    size: '',
    brand: '',
    material: '',
    notes: ''
  });
};

const removeAccessory = (index) => {
  accessories.value.splice(index, 1);
};

const validate = async () => {
  const { valid } = await form.value.validate();
  return valid;
};

const reset = () => {
  form.value.reset();
  totalSpaceValue.value = '';
  totalSpaceUnit.value = '';
  housingType.value = '';
  flooringType.value = '';
  beddingType.value = '';
  accessories.value = [{
    type: '',
    name: '',
    size: '',
    brand: '',
    material: '',
    notes: ''
  }];
};

// Expose methods to parent component
defineExpose({
  validate,
  reset,
  totalSpaceValue,
  totalSpaceUnit,
  housingType,
  flooringType,
  beddingType,
  accessories
});
</script>
