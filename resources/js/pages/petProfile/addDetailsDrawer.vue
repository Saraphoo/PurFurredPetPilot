<script setup lang="ts">
import {ref} from 'vue';
import {defineProps, defineEmits} from 'vue';

// Props to accept data for buttons
const props = defineProps<{
    buttons: Array<{ label: string, action: () => void }>
}>();

const emit = defineEmits(['close']);


// Ref to control drawer visibility
const isDrawerOpen = ref(true);

// Function to close the drawer
function closeDrawer() {
    isDrawerOpen.value = false;
    emit('close');
}


</script>

<template>
    <!-- Drawer Background Overlay -->
    <div v-if="isDrawerOpen" class="fixed inset-0 bg-black bg-opacity-50 z-40" @click="closeDrawer"></div>

    <!-- Drawer Component -->
    <div class="fixed right-0 top-0 h-full w-64 bg-white shadow-lg z-50 transform transition-transform duration-300"
         :class="{'translate-x-0': isDrawerOpen, 'translate-x-full': !isDrawerOpen}">
        <div class="flex justify-between items-center px-4 py-2 border-b">
            <h2 class="text-lg font-bold">Additional Details</h2>
            <button @click="closeDrawer" class="text-gray-600 hover:text-gray-900">
                <!-- Simple close "X" icon -->
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>

        <!-- Scrollable List of Buttons -->
        <div class="overflow-y-auto p-4 space-y-2">
            <button
                v-for="(button, index) in props.buttons"
                :key="index"
                @click="button.action"
                class="w-full text-left bg-gray-200 hover:bg-gray-300 px-4 py-2 rounded-lg"
            >
                {{ button.label }}
            </button>
        </div>
    </div>
</template>

<style scoped>
/* Animation for sliding drawer */
.transform {
    transform: translateX(100%);
}
.translate-x-0 {
    transform: translateX(0);
}
</style>
