<template>
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
        label="Weekdays"
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
        @click:event="showEvent"
      ></v-calendar>
    </v-sheet>
  </div>
</template>

<script setup>
import { ref } from 'vue'

const type = ref('month')
const types = ['month', 'week', 'day']

const weekday = ref([0, 1, 2, 3, 4, 5, 6])
const weekdays = [
  { title: 'All Week', value: [0, 1, 2, 3, 4, 5, 6] },
  { title: 'Weekdays', value: [1, 2, 3, 4, 5] },
  { title: 'Weekend', value: [0, 6] },
]

const value = ref('')

// Sample events - you can replace these with your own data
const events = ref([
  {
    name: 'Meeting',
    start: '2024-03-15 09:00',
    end: '2024-03-15 10:00',
    color: 'primary',
  },
  {
    name: 'Lunch',
    start: '2024-03-16 12:00',
    end: '2024-03-16 13:00',
    color: 'secondary',
  },
])

const showEvent = (event) => {
  console.log('Event clicked:', event)
  // Add your event handling logic here
}
</script>

<style scoped>
.v-sheet {
  margin-bottom: 16px;
}
</style> 