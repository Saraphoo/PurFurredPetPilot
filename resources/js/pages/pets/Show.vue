<script setup lang="ts">
import { defineProps } from 'vue';

// Define props for pet
defineProps({
    pet: {
        type: Object,
        required: true,
    },
});
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
