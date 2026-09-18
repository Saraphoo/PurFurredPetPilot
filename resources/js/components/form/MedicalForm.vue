<template>
  <div>
    <div v-if="loading" class="py-8 text-center text-muted-foreground">Loading medical information&hellip;</div>

    <form v-else class="space-y-6" @submit.prevent="submitForm">
      <Card>
        <CardHeader>
          <CardTitle>Special Needs</CardTitle>
        </CardHeader>
        <CardContent class="space-y-4">
          <div v-if="specialNeeds.length === 0" class="text-sm text-muted-foreground">No special needs added yet.</div>

          <div
            v-for="(need, index) in specialNeeds"
            :key="index"
            class="grid grid-cols-1 gap-4 rounded-md border p-3 md:grid-cols-12 md:items-end"
          >
            <div class="grid gap-2 md:col-span-4">
              <Label :for="`need-name-${index}`">Condition</Label>
              <Input :id="`need-name-${index}`" v-model="need.name" required />
            </div>
            <div class="grid gap-2 md:col-span-3">
              <Label :for="`need-affects-${index}`">Affects</Label>
              <Input :id="`need-affects-${index}`" v-model="need.affects" required />
            </div>
            <div class="grid gap-2 md:col-span-4">
              <Label :for="`need-notes-${index}`">Notes</Label>
              <Input :id="`need-notes-${index}`" v-model="need.notes" />
            </div>
            <div class="flex justify-end md:col-span-1">
              <Button type="button" variant="destructive" size="icon" @click="specialNeeds.splice(index, 1)">
                <Trash2 class="size-4" />
              </Button>
            </div>
          </div>

          <Button type="button" variant="secondary" @click="specialNeeds.push({ name: '', affects: '', notes: '' })">
            Add a Special Need
          </Button>
        </CardContent>
      </Card>

      <Card>
        <CardHeader>
          <CardTitle>Medications</CardTitle>
        </CardHeader>
        <CardContent class="space-y-4">
          <div v-if="medications.length === 0" class="text-sm text-muted-foreground">No medications added yet.</div>

          <div
            v-for="(medication, index) in medications"
            :key="index"
            class="grid grid-cols-1 gap-4 rounded-md border p-3 md:grid-cols-12 md:items-end"
          >
            <div class="grid gap-2 md:col-span-4">
              <Label :for="`medication-name-${index}`">Medication</Label>
              <Input :id="`medication-name-${index}`" v-model="medication.name" required />
            </div>
            <div class="grid gap-2 md:col-span-3">
              <Label :for="`medication-prescribed-${index}`">Prescribed On</Label>
              <Input :id="`medication-prescribed-${index}`" v-model="medication.prescribed_on" type="date" required />
            </div>
            <div class="grid gap-2 md:col-span-4">
              <Label :for="`medication-notes-${index}`">Notes</Label>
              <Input :id="`medication-notes-${index}`" v-model="medication.notes" />
            </div>
            <div class="flex justify-end md:col-span-1">
              <Button type="button" variant="destructive" size="icon" @click="medications.splice(index, 1)">
                <Trash2 class="size-4" />
              </Button>
            </div>
          </div>

          <Button
            type="button"
            variant="secondary"
            @click="medications.push({ name: '', prescribed_on: '', notes: '' })"
          >
            Add a Medication
          </Button>
        </CardContent>
      </Card>

      <p v-if="error" class="text-sm text-destructive">{{ error }}</p>

      <div class="flex justify-end">
        <Button type="submit" class="px-6" :disabled="saving">Save Medical Information</Button>
      </div>
    </form>
  </div>
</template>

<script setup lang="ts">
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import axios from 'axios';
import { Trash2 } from 'lucide-vue-next';
import { onMounted, ref } from 'vue';

interface SpecialNeed {
    name: string;
    affects: string;
    notes: string;
}

interface MedicationEntry {
    name: string;
    prescribed_on: string;
    notes: string;
}

const props = defineProps<{
    petId: number;
}>();

const loading = ref(true);
const saving = ref(false);
const error = ref('');

const specialNeeds = ref<SpecialNeed[]>([]);
const medications = ref<MedicationEntry[]>([]);

const loadMedicalInformation = async () => {
    loading.value = true;
    try {
        const response = await axios.get(route('medical.index', { pet: props.petId }));
        specialNeeds.value = response.data.special_needs.map((need: any) => ({
            name: need.name,
            affects: need.affects,
            notes: need.notes ?? ''
        }));
        medications.value = response.data.medications.map((medication: any) => ({
            name: medication.name,
            prescribed_on: medication.prescribed_on ?? '',
            notes: medication.notes ?? ''
        }));
    } catch (err) {
        console.error('Error loading medical information:', err);
    } finally {
        loading.value = false;
    }
};

const submitForm = async () => {
    saving.value = true;
    error.value = '';

    try {
        const response = await axios.post(route('medical.store', { pet: props.petId }), {
            special_needs: specialNeeds.value,
            medications: medications.value
        });
        specialNeeds.value = response.data.special_needs.map((need: any) => ({
            name: need.name,
            affects: need.affects,
            notes: need.notes ?? ''
        }));
        medications.value = response.data.medications.map((medication: any) => ({
            name: medication.name,
            prescribed_on: medication.prescribed_on ?? '',
            notes: medication.notes ?? ''
        }));
    } catch (err: any) {
        error.value = err.response?.data?.message || 'Something went wrong saving medical information.';
    } finally {
        saving.value = false;
    }
};

onMounted(() => {
    loadMedicalInformation();
});
</script>
