<script setup lang="ts">
import { ref, watch, nextTick, onMounted } from 'vue';
import { Button } from "@/components/ui/button";
import { router } from '@inertiajs/vue3';

// Reactive state for chat messages, loading state, and drawer state
const userMessage = ref('');
const messages = ref<Array<{ role: 'user' | 'assistant', content: string }>>([]);
const isLoading = ref(false);
const isDrawerOpen = ref(false);
const messagesContainer = ref<HTMLElement | null>(null);
const selectedPetId = ref<number | null>(null);

// Define props for Inertia data
const props = defineProps<{
    pets?: Array<{ id: number, name: string }>
}>();

// Initialize userPets with props data
const userPets = ref<Array<{ id: number, name: string }>>(props.pets || []);

// Fetch user's pets when component mounts
const fetchUserPets = async () => {
    try {
        console.log('Fetching pets...');
        const response = await fetch('/api/user/pets', {
            method: 'GET',
            headers: {
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
            },
            credentials: 'same-origin'
        });
        
        if (!response.ok) {
            throw new Error('Failed to fetch pets');
        }
        
        const data = await response.json();
        console.log('Pets data:', data);
        
        if (data.pets && Array.isArray(data.pets)) {
            userPets.value = data.pets;
            console.log('Updated userPets:', userPets.value);
        }
    } catch (error: any) {
        console.error('Error in fetchUserPets:', error);
    }
};

// Call fetchUserPets when component mounts
onMounted(() => {
    fetchUserPets();
});

// Function to scroll to bottom of messages
const scrollToBottom = () => {
    if (messagesContainer.value) {
        messagesContainer.value.scrollTop = messagesContainer.value.scrollHeight;
    }
};

// Watch for changes in messages and scroll to bottom
watch(messages, () => {
    nextTick(() => {
        scrollToBottom();
    });
}, { deep: true });

// Function to send a message to the OpenAI API endpoint
const sendMessage = async () => {
    if (!userMessage.value.trim()) return;

    const messageContent = userMessage.value;
    userMessage.value = '';
    messages.value.push({ role: 'user', content: messageContent });
    isLoading.value = true;

    try {
        const response = await fetch('/api/chat', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
            },
            credentials: 'same-origin',
            body: JSON.stringify({
                message: messageContent,
                pet_id: selectedPetId.value
            })
        });

        const data = await response.json();

        if (!response.ok) {
            throw new Error(data.error || 'Failed to send message');
        }

        if (data.message) {
            messages.value.push({ role: 'assistant', content: data.message });
        } else {
            throw new Error('No message in response');
        }
    } catch (error: any) {
        console.error('Error in sendMessage:', error);
        messages.value.push({ 
            role: 'assistant', 
            content: error.message || 'Sorry, I encountered an error. Please try again.' 
        });
    } finally {
        isLoading.value = false;
    }
};
</script>

<template>
    <!-- Floating Action Button -->
    <button
        @click="isDrawerOpen = true"
        class="fixed bottom-8 right-8 w-14 h-14 rounded-full bg-[#FF9F1C] text-white shadow-lg hover:bg-[#FF9F1C]/90 flex items-center justify-center z-[100] transition-colors"
    >
        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-4l-4 4z" />
        </svg>
    </button>

    <!-- Drawer Overlay -->
    <div v-if="isDrawerOpen"
         class="fixed inset-0 bg-black/50 z-[101]"
         @click="isDrawerOpen = false">
    </div>

    <!-- Chat Drawer -->
    <div
        :class="[
            'fixed top-0 right-0 h-full w-[400px] bg-background shadow-lg transform transition-transform duration-300 z-[102]',
            isDrawerOpen ? 'translate-x-0' : 'translate-x-full'
        ]"
    >
        <div class="p-6 h-full flex flex-col">
            <!-- Header -->
            <div class="flex justify-between items-center mb-6">
                <h2 class="text-2xl font-semibold text-foreground">Pet Care Assistant</h2>
                <button
                    @click="isDrawerOpen = false"
                    class="p-2 rounded-full hover:bg-[#2EC4B6]/10 transition-colors text-[#2EC4B6]"
                >
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <!-- Pet Selection -->
            <div class="mb-4">
                <select
                    v-model="selectedPetId"
                    class="w-full p-2 border border-input rounded-lg bg-background text-foreground focus:outline-none focus:ring-2 focus:ring-[#FF9F1C] focus:border-[#FF9F1C]"
                >
                    <option :value="null">Select a pet</option>
                    <option v-for="pet in userPets" :key="pet.id" :value="pet.id">
                        {{ pet.name }}
                    </option>
                </select>
            </div>

            <!-- Chat Messages -->
            <div ref="messagesContainer" class="flex-grow overflow-y-auto mb-4">
                <div v-for="(message, index) in messages" :key="index" class="mb-4">
                    <div :class="[
                        'p-3 rounded-lg max-w-[85%]',
                        message.role === 'user'
                            ? 'bg-[#FF9F1C] text-white ml-auto'
                            : 'bg-muted text-black'
                    ]">
                        {{ message.content }}
                    </div>
                </div>
                <!-- Loading Indicator -->
                <div v-if="isLoading" class="mb-4">
                    <div class="p-3 rounded-lg max-w-[85%] bg-muted text-black flex items-center space-x-2">
                        <div class="animate-spin rounded-full h-4 w-4 border-2 border-[#2EC4B6] border-t-transparent"></div>
                        <span>Thinking...</span>
                    </div>
                </div>
            </div>

            <!-- Input Area -->
            <div class="mt-auto">
                <textarea
                    v-model="userMessage"
                    class="w-full p-3 border border-input rounded-lg resize-none bg-background text-foreground placeholder:text-muted-foreground focus:outline-none focus:ring-2 focus:ring-[#FF9F1C] focus:border-[#FF9F1C]"
                    rows="3"
                    placeholder="Ask about pet care..."
                    @keyup.enter.exact.prevent="sendMessage(); userMessage = ''"
                    :disabled="isLoading"
                ></textarea>

                <Button
                    class="w-full mt-2 bg-[#2EC4B6] text-white hover:bg-[#2EC4B6]/90"
                    :disabled="isLoading || !userMessage.trim()"
                    @click="sendMessage"
                >
                    <span v-if="isLoading" class="flex items-center justify-center">
                        <div class="animate-spin rounded-full h-4 w-4 border-2 border-white border-t-transparent mr-2"></div>
                        Sending...
                    </span>
                    <span v-else>Send Message</span>
                </Button>
            </div>
        </div>
    </div>
</template>

<style scoped>
.overflow-y-auto {
    scrollbar-width: thin;
    scrollbar-color: hsl(var(--muted-foreground) / 0.5) transparent;
}

.overflow-y-auto::-webkit-scrollbar {
    width: 6px;
}

.overflow-y-auto::-webkit-scrollbar-track {
    background: transparent;
}

.overflow-y-auto::-webkit-scrollbar-thumb {
    background-color: hsl(var(--muted-foreground) / 0.5);
    border-radius: 3px;
}
</style>
