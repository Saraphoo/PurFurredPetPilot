<script setup lang="ts">
import { ref } from 'vue';
import { Button } from "@/components/ui/button";
import axios from 'axios'; // Make sure axios is installed

// Reactive state for chat messages, loading state, and drawer state
const userMessage = ref('');
const messages = ref<Array<{ role: 'user' | 'assistant', content: string }>>([]);
const isLoading = ref(false);
const isDrawerOpen = ref(false);

// Function to send a message to the OpenAI API endpoint
const sendMessage = async () => {
    if (!userMessage.value.trim()) return;

    try {
        isLoading.value = true;
        // Add user message to chat
        messages.value.push({
            role: 'user',
            content: userMessage.value
        });

        // Using axios instead of fetch
        const response = await axios.post('/chat', {
            message: userMessage.value
        });

        const data = response.data;

        if (data.status === 'success') {
            messages.value.push({
                role: 'assistant',
                content: data.message
            });
        } else {
            throw new Error(data.message);
        }
    } catch (error) {
        console.error('Error fetching OpenAI response:', error);
        messages.value.push({
            role: 'assistant',
            content: 'Sorry, I encountered an error processing your request.'
        });
    } finally {
        userMessage.value = '';
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

            <!-- Chat Messages -->
            <div class="flex-grow overflow-y-auto mb-4">
                <div v-for="(message, index) in messages" :key="index" class="mb-4">
                    <div :class="[
                        'p-3 rounded-lg max-w-[85%]',
                        message.role === 'user'
                            ? 'bg-[#FF9F1C] text-white ml-auto'
                            : 'bg-muted text-muted-foreground'
                    ]">
                        {{ message.content }}
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
                    @keyup.enter.exact.prevent="sendMessage"
                ></textarea>

                <Button
                    class="w-full mt-2 bg-[#2EC4B6] text-white hover:bg-[#2EC4B6]/90"
                    :disabled="isLoading || !userMessage.trim()"
                    @click="sendMessage"
                >
                    {{ isLoading ? 'Sending...' : 'Send Message' }}
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
