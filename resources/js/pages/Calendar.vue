<template>
    <div class="calendar-container p-6">
        <div class="mb-6 flex justify-between items-center">
            <h1 class="text-2xl font-bold text-gray-800">Pet Calendar</h1>
            <button
                @click="connectToGoogle"
                class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded"
            >
                Connect Google Calendar
            </button>
        </div>


  <div>
    <v-sheet
      class="d-flex"
      height="54"
      tile
    >
      <v-select
        v-model="type"
        :items="types"
        class="ma-2"
        density="compact"
        label="View Mode"
        variant="outlined"
        hide-details
      ></v-select>
      <v-select
        v-model="weekday"
        :items="weekdays"
        class="ma-2"
        density="compact"
        label="weekdays"
        variant="outlined"
        hide-details
      ></v-select>
    </v-sheet>
    <v-sheet>
      <v-calendar
        ref="calendar"
        v-model="value"
        :events="events"
        :view-mode="type"
        :weekdays="weekday"
      ></v-calendar>
    </v-sheet>
  </div>




        <div class="bg-white rounded-lg shadow p-6">
            <!-- Calendar Navigation -->
            <div class="flex justify-between items-center mb-4">
                <div class="flex space-x-4">
                    <button
                        @click="previousMonth"
                        class="p-2 hover:bg-gray-100 rounded"
                    >
                        &lt; Previous
                    </button>
                    <h2 class="text-xl font-semibold">
                        {{ currentMonthName }} {{ currentYear }}
                    </h2>
                    <button
                        @click="nextMonth"
                        class="p-2 hover:bg-gray-100 rounded"
                    >
                        Next &gt;
                    </button>
                </div>
            </div>

            <!-- Calendar Grid -->
            <div class="grid grid-cols-7 gap-2">
                <!-- Days of Week -->
                <div
                    v-for="day in daysOfWeek"
                    :key="day"
                    class="text-center font-semibold p-2 text-gray-600"
                >
                    {{ day }}
                </div>

                <!-- Calendar Days -->
                <div
                    v-for="date in calendarDays"
                    :key="date.day"
                    :class="[
                        'p-2 min-h-[80px] border border-gray-200',
                        date.isCurrentMonth ? 'bg-white' : 'bg-gray-50',
                        date.isToday ? 'border-blue-500 border-2' : ''
                    ]"
                >
                    <div class="flex justify-between">
                        <span :class="date.isCurrentMonth ? 'text-gray-800' : 'text-gray-400'">
                            {{ date.day }}
                        </span>
                    </div>
                    <!-- Events will go here -->
                    <div class="mt-1">
                        <div
                            v-for="event in getEventsForDate(date)"
                            :key="event.id"
                            class="text-xs p-1 mb-1 rounded bg-blue-100 text-blue-800"
                        >
                            {{ event.title }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup lang="ts">
import { ref, computed, onMounted } from 'vue';
import axios from 'axios';

const currentDate = ref(new Date());
const events = ref([]);

const daysOfWeek = ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'];

const currentMonthName = computed(() => {
    return new Intl.DateTimeFormat('en-US', { month: 'long' }).format(currentDate.value);
});

const currentYear = computed(() => {
    return currentDate.value.getFullYear();
});

const calendarDays = computed(() => {
    const year = currentDate.value.getFullYear();
    const month = currentDate.value.getMonth();

    const firstDayOfMonth = new Date(year, month, 1);
    const lastDayOfMonth = new Date(year, month + 1, 0);

    const days = [];

    // Add days from previous month
    const firstDayOfWeek = firstDayOfMonth.getDay();
    for (let i = firstDayOfWeek - 1; i >= 0; i--) {
        const date = new Date(year, month, -i);
        days.push({
            day: date.getDate(),
            isCurrentMonth: false,
            isToday: isSameDate(date, new Date()),
            date: date
        });
    }

    // Add days of current month
    for (let i = 1; i <= lastDayOfMonth.getDate(); i++) {
        const date = new Date(year, month, i);
        days.push({
            day: i,
            isCurrentMonth: true,
            isToday: isSameDate(date, new Date()),
            date: date
        });
    }

    // Add days from next month
    const remainingDays = 42 - days.length; // 6 rows * 7 days = 42
    for (let i = 1; i <= remainingDays; i++) {
        const date = new Date(year, month + 1, i);
        days.push({
            day: i,
            isCurrentMonth: false,
            isToday: isSameDate(date, new Date()),
            date: date
        });
    }

    return days;
});

function isSameDate(date1, date2) {
    return date1.getDate() === date2.getDate() &&
           date1.getMonth() === date2.getMonth() &&
           date1.getFullYear() === date2.getFullYear();
}

function previousMonth() {
    currentDate.value = new Date(
        currentDate.value.getFullYear(),
        currentDate.value.getMonth() - 1,
        1
    );
}

function nextMonth() {
    currentDate.value = new Date(
        currentDate.value.getFullYear(),
        currentDate.value.getMonth() + 1,
        1
    );
}

function getEventsForDate(date) {
    return events.value.filter(event => isSameDate(new Date(event.date), date.date));
}

async function connectToGoogle() {
    try {
        const response = await axios.get('/calendar/connect');
        window.location.href = response.data.url;
    } catch (error) {
        console.error('Failed to connect to Google Calendar:', error);
    }
}

async function fetchEvents() {
    try {
        const response = await axios.get('/calendar/events');
        events.value = response.data;
    } catch (error) {
        console.error('Failed to fetch events:', error);
    }
}

// Add these new refs for calendar view
const type = ref('month')
const types = ['month', 'week', 'day']
const weekday = ref([0, 1, 2, 3, 4, 5, 6])
const weekdays = [
  { value: [0, 1, 2, 3, 4, 5, 6], text: 'All' },
  { value: [1, 2, 3, 4, 5], text: 'Weekdays' },
  { value: [6, 0], text: 'Weekend' },
]
const value = ref('')

onMounted(() => {
    fetchEvents();
});
</script>

<style scoped>
.calendar-container {
    max-width: 1200px;
    margin: 0 auto;
}
</style>
