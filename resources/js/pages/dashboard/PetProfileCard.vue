<script setup lang="ts">

import {onMounted} from "vue";

defineProps<{
    pet: {
        id: number;
        name: string;
        petImage: string;
    };
}>();

onMounted(() => {
    console.log(this);
})


</script>

<template>
<Card>
    <div class="relative mt-8 flex items-center gap-x-4">

        <div class="text-sm/6">
            <p class="font-semibold text-gray-900">
                <a href="#">
                    <span class="absolute inset-0"></span>
                    t{{pet.name}}
                </a>
            </p>
            <p class="text-gray-600">t{{pet.type}} | t{{pet.age}}</p>
        </div>
    </div>
</Card>
</template>

<style scoped>

</style>
