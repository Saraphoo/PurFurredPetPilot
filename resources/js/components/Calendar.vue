<template>
  <div class="calendar">
    <div class="calendar-header">
      <div class="calendar-header-left">
        <button class="btn" @click="previousMonth">&lt;</button>
        <h2>{{ currentMonthName }} {{ currentYear }}</h2>
        <button class="btn" @click="nextMonth">&gt;</button>
      </div>
      <div class="calendar-header-right">
        <button 
          v-for="view in views" 
          :key="view"
          class="btn"
          :class="{ active: currentView === view }"
          @click="currentView = view"
        >
          {{ view }}
        </button>
      </div>
    </div>

    <div class="calendar-body">
      <div class="calendar-weekdays">
        <div v-for="day in weekDays" :key="day" class="weekday">{{ day }}</div>
      </div>
      
      <div class="calendar-days">
        <div 
          v-for="day in calendarDays" 
          :key="day.date.toISOString()"
          class="day"
          :class="{
            'other-month': !day.currentMonth,
            'today': day.isToday,
            'has-events': day.events.length > 0
          }"
          @click="selectDate(day)"
        >
          <span class="day-number">{{ day.dayNumber }}</span>
          <div class="day-events">
            <div 
              v-for="event in day.events" 
              :key="event.id"
              class="event"
              :style="{ backgroundColor: event.color }"
              @click.stop="selectEvent(event)"
            >
              {{ event.title }}
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Event Modal -->
    <div v-if="selectedEvent" class="modal">
      <div class="modal-content">
        <h3>{{ selectedEvent.title }}</h3>
        <p>Date: {{ formatDate(selectedEvent.date) }}</p>
        <p>Time: {{ selectedEvent.time }}</p>
        <p>Pet: {{ selectedEvent.petName }}</p>
        <button class="btn" @click="selectedEvent = null">Close</button>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, computed, onMounted } from 'vue';

interface Pet {
  id: number;
  name: string;
  petImage: string | null;
  type: string;
  DOB: string;
}

interface CalendarEvent {
  id: string;
  title: string;
  date: Date;
  time: string;
  petName: string;
  color: string;
}

interface CalendarDay {
  date: Date;
  dayNumber: number;
  currentMonth: boolean;
  isToday: boolean;
  events: CalendarEvent[];
}

const props = defineProps<{
  pets: Pet[] | null;
}>();

const currentDate = ref(new Date());
const currentView = ref('month');
const views = ['month', 'week', 'day'];
const selectedEvent = ref<CalendarEvent | null>(null);

const weekDays = ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'];

const currentMonthName = computed(() => {
  return currentDate.value.toLocaleString('default', { month: 'long' });
});

const currentYear = computed(() => {
  return currentDate.value.getFullYear();
});

const calendarDays = computed(() => {
  const year = currentDate.value.getFullYear();
  const month = currentDate.value.getMonth();
  
  const firstDay = new Date(year, month, 1);
  const lastDay = new Date(year, month + 1, 0);
  
  const days: CalendarDay[] = [];
  
  // Add days from previous month
  const firstDayOfWeek = firstDay.getDay();
  for (let i = firstDayOfWeek - 1; i >= 0; i--) {
    const date = new Date(year, month, -i);
    days.push({
      date,
      dayNumber: date.getDate(),
      currentMonth: false,
      isToday: isToday(date),
      events: getEventsForDate(date)
    });
  }
  
  // Add days from current month
  for (let i = 1; i <= lastDay.getDate(); i++) {
    const date = new Date(year, month, i);
    days.push({
      date,
      dayNumber: i,
      currentMonth: true,
      isToday: isToday(date),
      events: getEventsForDate(date)
    });
  }
  
  // Add days from next month
  const remainingDays = 42 - days.length; // 6 rows * 7 days
  for (let i = 1; i <= remainingDays; i++) {
    const date = new Date(year, month + 1, i);
    days.push({
      date,
      dayNumber: date.getDate(),
      currentMonth: false,
      isToday: isToday(date),
      events: getEventsForDate(date)
    });
  }
  
  return days;
});

const getEventsForDate = (date: Date): CalendarEvent[] => {
  if (!props.pets) return [];
  
  const events: CalendarEvent[] = [];
  props.pets.forEach(pet => {
    // Morning feeding
    events.push({
      id: `${pet.id}-morning-${date.toISOString()}`,
      title: `Feed ${pet.name} (Morning)`,
      date: date,
      time: '08:00',
      petName: pet.name,
      color: '#4CAF50'
    });
    
    // Evening feeding
    events.push({
      id: `${pet.id}-evening-${date.toISOString()}`,
      title: `Feed ${pet.name} (Evening)`,
      date: date,
      time: '18:00',
      petName: pet.name,
      color: '#2196F3'
    });
  });
  
  return events;
};

const isToday = (date: Date) => {
  const today = new Date();
  return date.getDate() === today.getDate() &&
         date.getMonth() === today.getMonth() &&
         date.getFullYear() === today.getFullYear();
};

const previousMonth = () => {
  currentDate.value = new Date(
    currentDate.value.getFullYear(),
    currentDate.value.getMonth() - 1,
    1
  );
};

const nextMonth = () => {
  currentDate.value = new Date(
    currentDate.value.getFullYear(),
    currentDate.value.getMonth() + 1,
    1
  );
};

const selectDate = (day: CalendarDay) => {
  console.log('Selected date:', day.date);
};

const selectEvent = (event: CalendarEvent) => {
  selectedEvent.value = event;
};

const formatDate = (date: Date) => {
  return date.toLocaleDateString('default', {
    weekday: 'long',
    year: 'numeric',
    month: 'long',
    day: 'numeric'
  });
};
</script>

<style scoped>
.calendar {
  background: white;
  border-radius: 8px;
  box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
  padding: 20px;
}

.calendar-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 20px;
}

.calendar-header-left {
  display: flex;
  align-items: center;
  gap: 10px;
}

.calendar-header-right {
  display: flex;
  gap: 10px;
}

.btn {
  padding: 8px 16px;
  border: 1px solid #e0e0e0;
  border-radius: 4px;
  background: white;
  cursor: pointer;
  transition: all 0.2s;
}

.btn:hover {
  background: #f5f5f5;
}

.btn.active {
  background: #2196F3;
  color: white;
  border-color: #2196F3;
}

.calendar-weekdays {
  display: grid;
  grid-template-columns: repeat(7, 1fr);
  gap: 1px;
  background: #f5f5f5;
  border: 1px solid #e0e0e0;
  border-radius: 4px 4px 0 0;
}

.weekday {
  padding: 10px;
  text-align: center;
  font-weight: bold;
  background: white;
}

.calendar-days {
  display: grid;
  grid-template-columns: repeat(7, 1fr);
  gap: 1px;
  background: #f5f5f5;
  border: 1px solid #e0e0e0;
  border-top: none;
  border-radius: 0 0 4px 4px;
}

.day {
  min-height: 100px;
  padding: 5px;
  background: white;
  cursor: pointer;
  transition: all 0.2s;
}

.day:hover {
  background: #f5f5f5;
}

.day.other-month {
  color: #bdbdbd;
}

.day.today {
  background: #e3f2fd;
}

.day-number {
  font-size: 14px;
  font-weight: bold;
  margin-bottom: 5px;
}

.day-events {
  display: flex;
  flex-direction: column;
  gap: 2px;
}

.event {
  font-size: 12px;
  padding: 2px 4px;
  border-radius: 2px;
  color: white;
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
}

.modal {
  position: fixed;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background: rgba(0, 0, 0, 0.5);
  display: flex;
  align-items: center;
  justify-content: center;
}

.modal-content {
  background: white;
  padding: 20px;
  border-radius: 8px;
  min-width: 300px;
}
</style>

