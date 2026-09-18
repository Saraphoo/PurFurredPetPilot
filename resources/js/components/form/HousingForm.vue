<template>
  <div>
    <Card class="mb-6">
      <CardHeader>
        <CardTitle>Housing</CardTitle>
      </CardHeader>
      <CardContent class="space-y-4">
        <div v-if="loading" class="py-4 text-center text-muted-foreground">Loading housing information&hellip;</div>

        <div v-else-if="housingEntries.length === 0" class="py-4 text-center text-muted-foreground">
          No housing set up yet. Add one below.
        </div>

        <div v-else class="space-y-3">
          <div v-for="entry in housingEntries" :key="entry.id" class="rounded-md border p-3">
            <div class="flex flex-col gap-2 sm:flex-row sm:items-start sm:justify-between">
              <div>
                <p class="font-medium">{{ entry.housing_type }}</p>
                <p class="text-sm text-muted-foreground">
                  {{ entry.total_space_value }} {{ entry.total_space_unit }} &middot; {{ entry.flooring_type }} floor &middot; {{ entry.bedding_type }}
                </p>
                <p v-if="entry.notes" class="text-sm text-muted-foreground">{{ entry.notes }}</p>
              </div>
              <div class="flex gap-2">
                <Button type="button" variant="secondary" size="sm" @click="startEditing(entry)">Edit</Button>
                <Button type="button" variant="destructive" size="sm" @click="deleteHousing(entry)">Delete</Button>
              </div>
            </div>

            <div v-if="entry.accessories?.length" class="mt-3 space-y-1 border-t pt-3">
              <p class="text-sm font-medium">Accessories</p>
              <p v-for="accessory in entry.accessories" :key="accessory.id" class="text-sm text-muted-foreground">
                {{ accessory.accessory_type }}: {{ accessory.name }} ({{ accessory.brand }}, {{ accessory.accessory_size }}, {{ accessory.material }})
              </p>
            </div>
          </div>
        </div>
      </CardContent>
    </Card>

    <Card>
      <CardHeader>
        <CardTitle>{{ editingId ? 'Edit Housing' : 'Add Housing' }}</CardTitle>
      </CardHeader>
      <CardContent>
        <form class="space-y-4" @submit.prevent="submitForm">
          <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
            <div class="grid gap-2">
              <Label for="housing-type">Housing Type</Label>
              <Select id="housing-type" v-model="draft.housing_type" required>
                <option value="" disabled>Select a housing type</option>
                <option v-for="option in housingTypes" :key="option" :value="option">{{ option }}</option>
              </Select>
            </div>
            <div class="grid gap-2 md:col-span-2 md:grid-cols-2 md:grid md:gap-4">
              <div class="grid gap-2">
                <Label for="total-space-value">Total Space</Label>
                <Input id="total-space-value" v-model="draft.total_space_value" type="number" step="any" required />
              </div>
              <div class="grid gap-2">
                <Label for="total-space-unit">Space Unit</Label>
                <Select id="total-space-unit" v-model="draft.total_space_unit" required>
                  <option value="" disabled>Select a unit</option>
                  <option v-for="option in spaceUnits" :key="option" :value="option">{{ option }}</option>
                </Select>
              </div>
            </div>
            <div class="grid gap-2">
              <Label for="flooring-type">Flooring Type</Label>
              <Select id="flooring-type" v-model="draft.flooring_type" required>
                <option value="" disabled>Select a flooring type</option>
                <option v-for="option in flooringTypes" :key="option" :value="option">{{ option }}</option>
              </Select>
            </div>
            <div class="grid gap-2">
              <Label for="bedding-type">Bedding Type</Label>
              <Select id="bedding-type" v-model="draft.bedding_type" required>
                <option value="" disabled>Select a bedding type</option>
                <option v-for="option in beddingTypes" :key="option" :value="option">{{ option }}</option>
              </Select>
            </div>
          </div>

          <div class="grid gap-2">
            <Label for="housing-notes">Notes</Label>
            <Textarea id="housing-notes" v-model="draft.notes" rows="3" />
          </div>

          <div class="space-y-3 border-t pt-4">
            <p class="font-medium">Accessories</p>

            <div
              v-for="(accessory, index) in draft.accessories"
              :key="index"
              class="grid grid-cols-1 gap-3 rounded-md border p-3 md:grid-cols-12 md:items-end"
            >
              <div class="grid gap-2 md:col-span-3">
                <Label :for="`accessory-type-${index}`">Type</Label>
                <Select :id="`accessory-type-${index}`" v-model="accessory.type" required>
                  <option value="" disabled>Select a type</option>
                  <option v-for="option in accessoryTypes" :key="option" :value="option">{{ option }}</option>
                </Select>
              </div>
              <div class="grid gap-2 md:col-span-3">
                <Label :for="`accessory-name-${index}`">Name</Label>
                <Input :id="`accessory-name-${index}`" v-model="accessory.name" required />
              </div>
              <div class="grid gap-2 md:col-span-2">
                <Label :for="`accessory-size-${index}`">Size</Label>
                <Input :id="`accessory-size-${index}`" v-model="accessory.size" />
              </div>
              <div class="grid gap-2 md:col-span-2">
                <Label :for="`accessory-brand-${index}`">Brand</Label>
                <Input :id="`accessory-brand-${index}`" v-model="accessory.brand" />
              </div>
              <div class="grid gap-2 md:col-span-1">
                <Label :for="`accessory-material-${index}`">Material</Label>
                <Select :id="`accessory-material-${index}`" v-model="accessory.material">
                  <option value="" disabled>Select</option>
                  <option v-for="option in materialOptions" :key="option" :value="option">{{ option }}</option>
                </Select>
              </div>
              <div class="flex justify-end md:col-span-1">
                <Button type="button" variant="destructive" size="icon" @click="draft.accessories.splice(index, 1)">
                  <Trash2 class="size-4" />
                </Button>
              </div>
            </div>

            <Button type="button" variant="secondary" @click="addAccessory">Add an Accessory</Button>
          </div>

          <p v-if="error" class="text-sm text-destructive">{{ error }}</p>

          <div class="flex justify-end gap-2">
            <Button v-if="editingId" type="button" variant="secondary" @click="cancelEditing">Cancel</Button>
            <Button type="submit" :disabled="saving">{{ editingId ? 'Save Changes' : 'Add Housing' }}</Button>
          </div>
        </form>
      </CardContent>
    </Card>
  </div>
</template>

<script setup lang="ts">
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Select } from '@/components/ui/select';
import { Textarea } from '@/components/ui/textarea';
import axios from 'axios';
import { Trash2 } from 'lucide-vue-next';
import { onMounted, reactive, ref } from 'vue';

interface Accessory {
    id?: number;
    type: string;
    name: string;
    size: string;
    brand: string;
    material: string;
    notes?: string;
}

interface HousingEntry {
    id: number;
    total_space_value: string;
    total_space_unit: string;
    housing_type: string;
    flooring_type: string;
    bedding_type: string;
    notes: string | null;
    accessories: Array<{
        id: number;
        accessory_type: string;
        name: string;
        accessory_size: string;
        brand: string;
        material: string;
        notes: string | null;
    }>;
}

const props = defineProps<{
    petId: number;
}>();

const spaceUnits = ['inches', 'feet', 'yards', 'acres', 'gallons', 'liters', 'cubic feet', 'cubic meters'];
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
const flooringTypes = ['Wire', 'Solid', 'Dirt', 'Wood', 'Glass', 'Metal', 'Plastic', 'Concrete'];
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
const materialOptions = ['Wood', 'Plastic', 'Metal', 'Glass', 'Ceramic', 'Fabric', 'Natural Fiber', 'Synthetic'];

const housingEntries = ref<HousingEntry[]>([]);
const loading = ref(true);
const saving = ref(false);
const error = ref('');
const editingId = ref<number | null>(null);

const emptyDraft = () => ({
    housing_type: '',
    total_space_value: '',
    total_space_unit: '',
    flooring_type: '',
    bedding_type: '',
    notes: '',
    accessories: [] as Accessory[]
});

const draft = reactive(emptyDraft());

const addAccessory = () => {
    draft.accessories.push({ type: '', name: '', size: '', brand: '', material: '', notes: '' });
};

const loadHousing = async () => {
    loading.value = true;
    try {
        const response = await axios.get(route('housing.index', { pet: props.petId }));
        housingEntries.value = response.data;
    } catch (err) {
        console.error('Error loading housing information:', err);
    } finally {
        loading.value = false;
    }
};

const startEditing = (entry: HousingEntry) => {
    editingId.value = entry.id;
    Object.assign(draft, {
        housing_type: entry.housing_type,
        total_space_value: entry.total_space_value,
        total_space_unit: entry.total_space_unit,
        flooring_type: entry.flooring_type,
        bedding_type: entry.bedding_type,
        notes: entry.notes ?? '',
        accessories: entry.accessories.map((accessory) => ({
            type: accessory.accessory_type,
            name: accessory.name,
            size: accessory.accessory_size,
            brand: accessory.brand,
            material: accessory.material,
            notes: accessory.notes ?? ''
        }))
    });
    error.value = '';
};

const cancelEditing = () => {
    editingId.value = null;
    Object.assign(draft, emptyDraft());
    error.value = '';
};

const submitForm = async () => {
    saving.value = true;
    error.value = '';

    try {
        if (editingId.value) {
            const response = await axios.put(
                route('housing.update', { pet: props.petId, housing: editingId.value }),
                draft
            );
            const index = housingEntries.value.findIndex((entry) => entry.id === editingId.value);
            if (index !== -1) housingEntries.value[index] = response.data;
        } else {
            const response = await axios.post(route('housing.store', { pet: props.petId }), draft);
            housingEntries.value.push(response.data);
        }

        cancelEditing();
    } catch (err: any) {
        error.value = err.response?.data?.message || 'Something went wrong saving housing information.';
    } finally {
        saving.value = false;
    }
};

const deleteHousing = async (entry: HousingEntry) => {
    try {
        await axios.delete(route('housing.destroy', { pet: props.petId, housing: entry.id }));
        housingEntries.value = housingEntries.value.filter((h) => h.id !== entry.id);
        if (editingId.value === entry.id) cancelEditing();
    } catch (err) {
        console.error('Error deleting housing entry:', err);
    }
};

onMounted(() => {
    loadHousing();
});
</script>
