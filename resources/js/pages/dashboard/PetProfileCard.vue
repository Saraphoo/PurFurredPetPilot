<script setup lang="ts">

import {onMounted} from "vue";

defineProps<{
    pet: {
        id: number;
        name: string;
        petImage: string;
        type: string;
        DOB: string;
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
                    {{pet.name}}
                </a>
            </p>
            <p class="text-gray-600">{{pet.type}} | {{pet.age}}</p>
        </div>
    </div>
</Card>
</template>

<style scoped>

</style>
