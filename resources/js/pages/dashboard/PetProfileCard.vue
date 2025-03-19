<script setup lang="ts">

import {onMounted} from "vue";
import {Card} from "@/components/ui/card";

defineProps<{
    pet: {
        id: number;
        name: string;
        petImage: string | null;
        type: string;
        DOB: string;
    };
}>();

onMounted(() => {
    console.log(this);
})


</script>

<template>
<Card class ="items-center">
    <div class="relative mt-8 flex items-center gap-x-4">
        <div v-if="pet.petImage">
            <img :src="pet.petImage" alt="pet image" class="w-16 h-16 rounded-full" />
        </div>
            <div v-else class="w-16 h-16 rounded-full bg-gray-200">
            </div>
        <div class="text-sm/6">
            <p class="font-semibold text-gray-900">
                <a href="#">
                    <span class="absolute inset-0"></span>
                    {{pet.name}}
                </a>
            </p>
            <p class="text-gray-600">{{pet.type}} | {{pet.DOB}}</p>
        </div>
    </div>
</Card>
</template>

<style scoped>

</style>
