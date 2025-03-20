<script setup lang="ts">
import { defineProps } from 'vue';

// Define props for pet
const props = defineProps<{
    pet: {
        id: number;
        name: string;
        petImage: string | null;
        type: string;
        DOB: string;
    };
}>();
</script>

<template>
    <div>
        <h1>{{ pet.name }}</h1>
        <p>Type: {{ pet.type }}</p>
        <p>Date of Birth: {{ pet.DOB }}</p>
    </div>
</template>

<style scoped>

</style>
