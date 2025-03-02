<script setup lang="ts">

defineProps<{
    pet: {
        id: number;
        name: string;
        type: string;
        age: number;
        petImage: string;
    };
}>();

</script>

<template>
<Card>
    <div class="relative mt-8 flex items-center gap-x-4">
        <img src="{{pet.petImage}}" alt="pet.name" class="w-16 h-16 rounded-full" />
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
