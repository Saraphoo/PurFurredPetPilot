<script setup lang="ts">
import { ref, onMounted } from 'vue';
import { Button } from '@/components/ui/button';
import { Dialog, DialogContent, DialogHeader, DialogTitle, DialogDescription } from '@/components/ui/dialog';
import { usePage } from '@inertiajs/vue3';

interface User {
    id: number;
    name: string;
    email: string;
}

interface Auth {
    user: User;
}

interface PageProps {
    auth: Auth;
    [key: string]: any;
}

const isOpen = ref(false);
const currentStep = ref(0);
const page = usePage<PageProps>();

const steps = [
    {
        title: "Welcome to PurFurred Pet Pilot! 🐾",
        description: "Let's take a quick tour of your new pet care companion!",
        position: "center",
    },
    {
        title: "Add Your Furry Friends 🐱",
        description: "Click the 'Add Pet' button in the sidebar to create profiles for all your pets. You can add their photos, birthdays, and special care needs!",
        position: "sidebar",
        target: "[data-sidebar='menu'] [href='/pets/create']",
    },
    {
        title: "Chat with Pet Care AI 🤖",
        description: "Need advice? Click the chat button in the bottom right corner to get instant help with pet care questions, training tips, and more!",
        position: "right",
        target: ".fixed.bottom-8.right-8",
    }
];

const startWalkthrough = () => {
    console.log('Starting walkthrough');
    isOpen.value = true;
    currentStep.value = 0;
};

const nextStep = () => {
    if (currentStep.value < steps.length - 1) {
        currentStep.value++;
    } else {
        isOpen.value = false;
        const userId = page.props.auth?.user?.id;
        if (userId) {
            localStorage.setItem(`walkthroughCompleted_${userId}`, 'true');
        }
    }
};

const skipWalkthrough = () => {
    isOpen.value = false;
    const userId = page.props.auth?.user?.id;
    if (userId) {
        localStorage.setItem(`walkthroughCompleted_${userId}`, 'true');
    }
};

onMounted(() => {
    const userId = page.props.auth?.user?.id;
    if (!userId) return; // Don't show walkthrough for non-authenticated users

    // Check if this is the user's first visit
    const walkthroughCompleted = localStorage.getItem(`walkthroughCompleted_${userId}`);
    console.log('Walkthrough completed status:', walkthroughCompleted);
    
    if (!walkthroughCompleted) {
        console.log('Walkthrough not completed, starting...');
        startWalkthrough();
    } else {
        console.log('Walkthrough already completed');
    }
});
</script>

<template>
    <Dialog :open="isOpen" @update:open="isOpen = $event">
        <DialogContent 
            :class="[
                'fixed z-[1000] max-w-md p-6 rounded-lg shadow-lg transition-all duration-300',
                'bg-white dark:bg-gray-800',
                steps[currentStep].position === 'center' ? 'left-1/2 top-1/2 -translate-x-1/2 -translate-y-1/2' :
                steps[currentStep].position === 'sidebar' ? 'left-[280px] top-1/2 -translate-y-1/2' :
                'right-4 top-1/2 -translate-y-1/2'
            ]"
        >
            <DialogHeader>
                <DialogTitle class="text-2xl font-bold text-[#2EC4B6] mb-2">
                    {{ steps[currentStep].title }}
                </DialogTitle>
                <DialogDescription class="text-gray-600 dark:text-gray-300">
                    {{ steps[currentStep].description }}
                </DialogDescription>
            </DialogHeader>

            <div class="flex justify-between mt-6">
                <Button 
                    variant="ghost" 
                    @click="skipWalkthrough"
                    class="text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200"
                >
                    Skip Tour
                </Button>
                <Button 
                    @click="nextStep"
                    class="bg-[#FF9F1C] hover:bg-[#FF9F1C]/90 text-white"
                >
                    {{ currentStep === steps.length - 1 ? 'Get Started' : 'Next' }}
                </Button>
            </div>
        </DialogContent>
    </Dialog>
</template>

<style scoped>
/* Add a pulsing effect to the target elements */
:deep([data-sidebar='menu'] [href='/pets/create']),
:deep(.fixed.bottom-8.right-8) {
    animation: pulse 2s infinite;
}

@keyframes pulse {
    0% {
        box-shadow: 0 0 0 0 rgba(46, 196, 182, 0.4);
    }
    70% {
        box-shadow: 0 0 0 10px rgba(46, 196, 182, 0);
    }
    100% {
        box-shadow: 0 0 0 0 rgba(46, 196, 182, 0);
    }
}
</style> 