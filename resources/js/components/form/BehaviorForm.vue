<template>
  <div>
    <div v-if="loading" class="py-8 text-center text-muted-foreground">Loading behavior information&hellip;</div>

    <form v-else class="space-y-6" @submit.prevent="submitForm">
      <Card>
        <CardHeader>
          <CardTitle>Known Behaviors</CardTitle>
        </CardHeader>
        <CardContent>
          <div class="grid grid-cols-1 gap-3 sm:grid-cols-2 md:grid-cols-3">
            <label
              v-for="option in behaviorOptions"
              :key="option"
              class="flex items-center gap-2 text-sm"
            >
              <Checkbox v-model:checked="checkedMap[option]" />
              {{ option }}
            </label>
          </div>
        </CardContent>
      </Card>

      <Card>
        <CardHeader>
          <CardTitle>Notes</CardTitle>
        </CardHeader>
        <CardContent class="space-y-4">
          <div class="grid gap-2">
            <Label for="behavior-notes">Behavior Notes</Label>
            <Textarea
              id="behavior-notes"
              v-model="behaviorNotes"
              rows="4"
              placeholder="Add notes about your pet's behaviors..."
            />
          </div>
          <div class="grid gap-2">
            <Label for="general-notes">General Notes</Label>
            <Textarea id="general-notes" v-model="generalNotes" rows="4" />
          </div>
        </CardContent>
      </Card>

      <p v-if="error" class="text-sm text-destructive">{{ error }}</p>

      <div class="flex justify-end">
        <Button type="submit" class="px-6" :disabled="saving">Save Behavior Information</Button>
      </div>
    </form>
  </div>
</template>

<script setup lang="ts">
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Checkbox } from '@/components/ui/checkbox';
import { Label } from '@/components/ui/label';
import { Textarea } from '@/components/ui/textarea';
import axios from 'axios';
import { onMounted, reactive, ref } from 'vue';

const props = defineProps<{
    petId: number;
}>();

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

const loading = ref(true);
const saving = ref(false);
const error = ref('');
const hasExistingRecord = ref(false);

const checkedMap = reactive<Record<string, boolean>>(
    Object.fromEntries(behaviorOptions.map((option) => [option, false]))
);
const behaviorNotes = ref('');
const generalNotes = ref('');

const loadBehavior = async () => {
    loading.value = true;
    try {
        const response = await axios.get(route('behaviors.index', { pet: props.petId }));
        const behavior = response.data;

        if (behavior?.id) {
            hasExistingRecord.value = true;
            behaviorOptions.forEach((option) => {
                checkedMap[option] = (behavior.behaviors || []).includes(option);
            });
            behaviorNotes.value = behavior.behavior_notes ?? '';
            generalNotes.value = behavior.general_notes ?? '';
        }
    } catch (err) {
        console.error('Error loading behavior information:', err);
    } finally {
        loading.value = false;
    }
};

const submitForm = async () => {
    saving.value = true;
    error.value = '';

    const payload = {
        behaviors: behaviorOptions.filter((option) => checkedMap[option]),
        behavior_notes: behaviorNotes.value,
        general_notes: generalNotes.value
    };

    try {
        if (hasExistingRecord.value) {
            await axios.put(route('behaviors.update', { pet: props.petId }), payload);
        } else {
            await axios.post(route('behaviors.store', { pet: props.petId }), payload);
            hasExistingRecord.value = true;
        }
    } catch (err: any) {
        error.value = err.response?.data?.message || 'Something went wrong saving behavior information.';
    } finally {
        saving.value = false;
    }
};

onMounted(() => {
    loadBehavior();
});
</script>
