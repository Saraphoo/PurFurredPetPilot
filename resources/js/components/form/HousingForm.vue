<template>
  <!-- <v-form ref="form" v-model="valid" @submit.prevent="submitForm">
    ... existing code ...
  </v-form> -->
  <div class="text-center py-8">
    <v-icon size="64" color="grey">mdi-home</v-icon>
    <h2 class="text-h4 mt-4">Coming Soon</h2>
    <p class="text-grey mt-2">Housing tracking functionality is under development</p>
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
        total_space_value: string;
        total_space_unit: string;
        housing_type: string;
        flooring_type: string;
        bedding_type: string;
        accessories: Array<{
            type: string;
            name: string;
            size: string;
            brand: string;
            material: string;
            notes: string;
        }>;
        notes: string;
    };
}>(), {
    petInfo: () => [],
    initialData: () => ({
        total_space_value: '',
        total_space_unit: '',
        housing_type: '',
        flooring_type: '',
        bedding_type: '',
        accessories: [],
        notes: ''
    })
});

const valid = ref(false);
const form = ref<VForm | null>(null);

// Form data
const totalSpaceValue = ref(props.initialData?.total_space_value || '');
const totalSpaceUnit = ref(props.initialData?.total_space_unit || '');
const housingType = ref(props.initialData?.housing_type || '');
const flooringType = ref(props.initialData?.flooring_type || '');
const beddingType = ref(props.initialData?.bedding_type || '');
const accessories = ref(props.initialData?.accessories || [{
  type: '',
  name: '',
  size: '',
  brand: '',
  material: '',
  notes: ''
}]);
const generalNotes = ref(props.initialData?.notes || '');

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

const removeAccessory = (index: number) => {
  accessories.value.splice(index, 1);
};

const validate = async () => {
  if (!form.value) return false;
  const { valid } = await form.value.validate();
  return valid;
};

const reset = () => {
  if (!form.value) return;
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

const submitForm = () => {
    const formData = {
        pet_id: props.petId,
        total_space_value: totalSpaceValue.value,
        total_space_unit: totalSpaceUnit.value,
        housing_type: housingType.value,
        flooring_type: flooringType.value,
        bedding_type: beddingType.value,
        accessories: accessories.value.map(accessory => ({
            type: accessory.type,
            name: accessory.name || '',
            size: accessory.size || '',
            brand: accessory.brand || '',
            material: accessory.material || '',
            notes: accessory.notes || ''
        })),
        notes: generalNotes.value
    };

    useForm(formData).post(route('housing.store', { pet: props.petId }), {
        preserveScroll: true,
        onSuccess: () => {
            reset();
        }
    });
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
  accessories,
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
