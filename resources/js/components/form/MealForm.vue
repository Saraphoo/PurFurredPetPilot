<template>
  <div>
    <Card class="mb-6">
      <CardHeader>
        <CardTitle>Meal Schedule</CardTitle>
      </CardHeader>
      <CardContent class="space-y-4">
        <div v-if="loading" class="py-4 text-center text-muted-foreground">Loading meals&hellip;</div>

        <div v-else-if="meals.length === 0" class="py-4 text-center text-muted-foreground">
          No meals scheduled yet. Add one below.
        </div>

        <div v-else class="space-y-3">
          <div
            v-for="meal in meals"
            :key="meal.id"
            class="flex flex-col gap-2 rounded-md border p-3 sm:flex-row sm:items-center sm:justify-between"
          >
            <div>
              <p class="font-medium">{{ meal.feed_time }} &mdash; {{ meal.name }}</p>
              <p class="text-sm text-muted-foreground">{{ meal.brand }} &middot; {{ meal.meal_type }} &middot; {{ meal.portion_size }}</p>
              <p v-if="meal.notes" class="text-sm text-muted-foreground">{{ meal.notes }}</p>
            </div>
            <div class="flex gap-2">
              <Button type="button" variant="secondary" size="sm" @click="startEditing(meal)">Edit</Button>
              <Button type="button" variant="destructive" size="sm" @click="deleteMeal(meal)">Delete</Button>
            </div>
          </div>
        </div>
      </CardContent>
    </Card>

    <Card>
      <CardHeader>
        <CardTitle>{{ editingId ? 'Edit Meal' : 'Add a Meal' }}</CardTitle>
      </CardHeader>
      <CardContent>
        <form class="space-y-4" @submit.prevent="submitForm">
          <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
            <div class="grid gap-2">
              <Label for="feed-time">Feed Time</Label>
              <Input id="feed-time" v-model="draft.feed_time" type="time" required />
            </div>
            <div class="grid gap-2">
              <Label for="meal-name">Food Name</Label>
              <Input id="meal-name" v-model="draft.name" required />
            </div>
            <div class="grid gap-2">
              <Label for="meal-brand">Brand</Label>
              <Input id="meal-brand" v-model="draft.brand" required />
            </div>
            <div class="grid gap-2">
              <Label for="meal-type">Meal Type</Label>
              <Select id="meal-type" v-model="draft.meal_type" required>
                <option value="" disabled>Select a meal type</option>
                <option v-for="option in mealTypes" :key="option" :value="option">{{ option }}</option>
              </Select>
            </div>
            <div class="grid gap-2">
              <Label for="portion-size">Portion Size</Label>
              <Input id="portion-size" v-model="draft.portion_size" placeholder="e.g. 1 cup" required />
            </div>
          </div>
          <div class="grid gap-2">
            <Label for="meal-notes">Notes</Label>
            <Textarea id="meal-notes" v-model="draft.notes" rows="3" />
          </div>

          <p v-if="error" class="text-sm text-destructive">{{ error }}</p>

          <div class="flex justify-end gap-2">
            <Button v-if="editingId" type="button" variant="secondary" @click="cancelEditing">Cancel</Button>
            <Button type="submit" :disabled="saving">{{ editingId ? 'Save Changes' : 'Add Meal' }}</Button>
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
import { onMounted, reactive, ref } from 'vue';

interface Meal {
    id: number;
    feed_time: string;
    name: string;
    brand: string;
    meal_type: string;
    portion_size: string;
    notes: string | null;
}

const props = defineProps<{
    petId: number;
}>();

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

const meals = ref<Meal[]>([]);
const loading = ref(true);
const saving = ref(false);
const error = ref('');
const editingId = ref<number | null>(null);

const emptyDraft = () => ({
    feed_time: '',
    name: '',
    brand: '',
    meal_type: '',
    portion_size: '',
    notes: ''
});

const draft = reactive(emptyDraft());

const loadMeals = async () => {
    loading.value = true;
    try {
        const response = await axios.get(route('meals.index', { pet: props.petId }));
        meals.value = response.data;
    } catch (err) {
        console.error('Error loading meals:', err);
    } finally {
        loading.value = false;
    }
};

const startEditing = (meal: Meal) => {
    editingId.value = meal.id;
    Object.assign(draft, {
        feed_time: meal.feed_time,
        name: meal.name,
        brand: meal.brand,
        meal_type: meal.meal_type,
        portion_size: meal.portion_size,
        notes: meal.notes ?? ''
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
                route('meals.update', { pet: props.petId, meal: editingId.value }),
                draft
            );
            const index = meals.value.findIndex((meal) => meal.id === editingId.value);
            if (index !== -1) meals.value[index] = response.data;
        } else {
            const response = await axios.post(route('meals.store', { pet: props.petId }), draft);
            meals.value.push(response.data);
        }

        cancelEditing();
    } catch (err: any) {
        error.value = err.response?.data?.message || 'Something went wrong saving this meal.';
    } finally {
        saving.value = false;
    }
};

const deleteMeal = async (meal: Meal) => {
    try {
        await axios.delete(route('meals.destroy', { pet: props.petId, meal: meal.id }));
        meals.value = meals.value.filter((m) => m.id !== meal.id);
        if (editingId.value === meal.id) cancelEditing();
    } catch (err) {
        console.error('Error deleting meal:', err);
    }
};

onMounted(() => {
    loadMeals();
});
</script>
