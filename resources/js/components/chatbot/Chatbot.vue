<script setup lang="ts">
import { ref } from 'vue';
import { Button } from "@/components/ui/button";

// Reactive state for user message and response message
const userMessage = ref('');
const responseMessage = ref('');

// Function to send a message to the OpenAI API endpoint
const sendMessage = async () => {
    try {
        const response = await fetch('/dashboard/test-openai', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({ message: userMessage.value }),
        });
        const data = await response.json();
        responseMessage.value = data.message;
    } catch (error) {
        console.error('Error fetching OpenAI response:', error);
    }
};

</script>

<template>
    <div class="p-6 max-w-lg mx-auto bg-white rounded-xl shadow-md space-y-4">
        <h2 class="text-2xl font-semibold text-gray-800">Chat with AI</h2>

        <!-- Textarea for user message input -->
        <textarea
            v-model="userMessage"
            class="border rounded w-full p-2"
            rows="4"
            placeholder="Type your message here..."
        ></textarea>

        <!-- Send button -->
        <Button class="bg-gray-400 text-white rounded w-full" @click="sendMessage">Send</Button>

        <!-- Response section -->
        <div v-if="responseMessage" class="mt-4">
            <h4 class="text-xl font-medium text-gray-700">Response from AI:</h4>
            <p class="text-gray-600 mt-2">{{ responseMessage }}</p>
        </div>
    </div>
</template>

<style scoped>
textarea {
    margin-bottom: 1rem;
}
</style>
