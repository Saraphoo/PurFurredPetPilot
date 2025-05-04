<template>
  <div class="calendar">
    <div class="calendar-header">
      <div class="calendar-header-left">
        <button class="btn" @click="previousPeriod">&lt;</button>
        <h2>{{ headerTitle }}</h2>
        <button class="btn" @click="nextPeriod">&gt;</button>
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
      <!-- Month View -->
      <template v-if="currentView === 'month'">
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
      </template>

      <!-- Week View -->
      <template v-if="currentView === 'week'">
        <div class="week-view">
          <div class="week-header">
            <div class="time-column"></div>
            <div 
              v-for="day in weekDays" 
              :key="day"
              class="week-day-header"
              :class="{ 'today': isToday(weekDates[weekDays.indexOf(day)]) }"
            >
              {{ day }}
              <div class="date-number">{{ weekDates[weekDays.indexOf(day)].getDate() }}</div>
            </div>
          </div>
          <div class="week-body">
            <div class="time-column">
              <div v-for="hour in hours" :key="hour" class="time-slot">
                {{ formatHour(hour) }}
              </div>
            </div>
            <div class="week-grid">
              <div 
                v-for="day in weekDates" 
                :key="day.toISOString()"
                class="week-day-column"
                :class="{ 'today': isToday(day) }"
              >
                <div 
                  v-for="hour in hours" 
                  :key="hour"
                  class="time-slot"
                  @click="selectTimeSlot(day, hour)"
                >
                  <div 
                    v-for="event in getEventsForTimeSlot(day, hour)"
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
        </div>
      </template>

      <!-- Day View -->
      <template v-if="currentView === 'day'">
        <div class="day-view">
          <div class="day-header">
            <div class="time-column"></div>
            <div class="day-header-content">
              {{ formatFullDate(currentDate) }}
            </div>
          </div>
          <div class="day-body">
            <div class="time-column">
              <div v-for="hour in hours" :key="hour" class="time-slot">
                {{ formatHour(hour) }}
              </div>
            </div>
            <div class="day-grid">
              <div 
                v-for="hour in hours" 
                :key="hour"
                class="time-slot"
                @click="selectTimeSlot(currentDate, hour)"
              >
                <div 
                  v-for="event in getEventsForTimeSlot(currentDate, hour)"
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
      </template>
    </div>

    <!-- Event Creation Modal -->
    <div v-if="showEventModal" class="modal">
      <div class="modal-content">
        <h3>Create New Event</h3>
        <form @submit.prevent="createEvent" class="event-form">
          <div class="form-group">
            <label for="event-title">Title</label>
            <input
              id="event-title"
              v-model="newEvent.title"
              type="text"
              required
              placeholder="Event title"
            >
          </div>

          <div class="form-group">
            <label for="event-pet">Pet</label>
            <select
              id="event-pet"
              v-model="newEvent.petId"
              required
            >
              <option value="">Select a pet</option>
              <option
                v-for="pet in pets"
                :key="pet.id"
                :value="pet.id"
              >
                {{ pet.name }}
              </option>
            </select>
          </div>

          <div class="form-group">
            <label for="event-type">Event Type</label>
            <select
              id="event-type"
              v-model="newEvent.type"
              required
            >
              <option
                v-for="type in eventTypes"
                :key="type.value"
                :value="type.value"
              >
                {{ type.label }}
              </option>
            </select>
          </div>

          <div class="form-group">
            <label for="event-time">Time</label>
            <input
              id="event-time"
              v-model="newEvent.time"
              type="time"
              required
            >
          </div>

          <div class="form-group">
            <label for="event-notes">Notes (optional)</label>
            <textarea
              id="event-notes"
              v-model="newEvent.notes"
              placeholder="Add any additional notes..."
              rows="3"
            ></textarea>
          </div>

          <div class="form-actions">
            <button type="button" class="btn btn-secondary" @click="showEventModal = false">
              Cancel
            </button>
            <button type="submit" class="btn btn-primary">
              Create Event
            </button>
          </div>
        </form>
      </div>
    </div>

    <!-- Event Modal -->
    <div v-if="showEventModal" class="modal">
      <div class="modal-content">
        <h3>Event Details</h3>
        <div v-if="selectedEvent" class="event-details">
          <p><strong>Title:</strong> {{ selectedEvent.title }}</p>
          <p><strong>Date:</strong> {{ formatEventDate(selectedEvent.start) }}</p>
          <p><strong>Time:</strong> {{ formatEventTime(selectedEvent.start) }} - {{ formatEventTime(selectedEvent.end) }}</p>
          <p><strong>Pet:</strong> {{ selectedEvent.petName }}</p>
          <p v-if="selectedEvent.description"><strong>Notes:</strong> {{ selectedEvent.description }}</p>
          <div class="modal-actions">
            <button class="btn btn-danger" @click="deleteEvent(selectedEvent.id)">Delete</button>
            <button class="btn btn-secondary" @click="showEventModal = false">Close</button>
          </div>
        </div>
      </div>
    </div>

    <!-- Loading State -->
    <div v-if="loading" class="loading-overlay">
      <div class="loading-spinner"></div>
    </div>

    <!-- Error Message -->
    <div v-if="error" class="error-message">
      {{ error }}
      <button class="btn btn-secondary" @click="fetchEvents">Retry</button>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, computed, onMounted } from 'vue';
import axios from 'axios';

// Configure axios defaults
axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';
axios.defaults.withCredentials = true;

interface Pet {
  id: number;
  name: string;
  type: string;
  breed?: string;
  age?: number;
  weight?: number;
  image?: string;
}

interface CalendarEvent {
  id: number;
  title: string;
  start: Date;
  end: Date;
  description?: string;
  color: string;
  petName: string;
  time?: string;
}

interface CalendarDay {
  date: Date;
  dayNumber: number;
  currentMonth: boolean;
  isToday: boolean;
  events: CalendarEvent[];
}

interface NewEvent {
  title: string;
  date: Date;
  time: string;
  petId: number;
  type: 'feeding' | 'medication' | 'grooming' | 'vet' | 'other';
  notes: string;
}

const props = defineProps<{
  pets: Pet[] | null;
}>();

const currentDate = ref(new Date());
const currentView = ref('month');
const views = ['month', 'week', 'day'];
const selectedEvent = ref<CalendarEvent | null>(null);
const hours = Array.from({ length: 24 }, (_, i) => i);

const weekDays = ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'];

const headerTitle = computed(() => {
  switch (currentView.value) {
    case 'month':
      return `${currentMonthName.value} ${currentYear.value}`;
    case 'week':
      const start = weekDates.value[0];
      const end = weekDates.value[6];
      return `${formatDateRange(start, end)}`;
    case 'day':
      return formatFullDate(currentDate.value);
    default:
      return '';
  }
});

const currentMonthName = computed(() => {
  return currentDate.value.toLocaleString('default', { month: 'long' });
});

const currentYear = computed(() => {
  return currentDate.value.getFullYear();
});

const weekDates = computed(() => {
  const dates: Date[] = [];
  const current = new Date(currentDate.value);
  const day = current.getDay();
  
  // Start from Sunday
  current.setDate(current.getDate() - day);
  
  for (let i = 0; i < 7; i++) {
    dates.push(new Date(current));
    current.setDate(current.getDate() + 1);
  }
  
  return dates;
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
      id: parseInt(`${pet.id}${date.getTime()}`),
      title: `Feed ${pet.name} (Morning)`,
      start: new Date(date.setHours(8, 0)),
      end: new Date(date.setHours(9, 0)),
      petName: pet.name,
      color: '#4CAF50',
      time: '08:00'
    });
    
    // Evening feeding
    events.push({
      id: parseInt(`${pet.id}${date.getTime() + 1}`),
      title: `Feed ${pet.name} (Evening)`,
      start: new Date(date.setHours(18, 0)),
      end: new Date(date.setHours(19, 0)),
      petName: pet.name,
      color: '#2196F3',
      time: '18:00'
    });
  });
  
  return events;
};

const getEventsForTimeSlot = (date: Date, hour: number): CalendarEvent[] => {
  const events = getEventsForDate(date);
  return events.filter(event => {
    const eventTime = event.time || event.start.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });
    const eventHour = parseInt(eventTime.split(':')[0]);
    return eventHour === hour;
  });
};

const isToday = (date: Date) => {
  const today = new Date();
  return date.getDate() === today.getDate() &&
         date.getMonth() === today.getMonth() &&
         date.getFullYear() === today.getFullYear();
};

const previousPeriod = () => {
  const date = new Date(currentDate.value);
  switch (currentView.value) {
    case 'month':
      date.setMonth(date.getMonth() - 1);
      break;
    case 'week':
      date.setDate(date.getDate() - 7);
      break;
    case 'day':
      date.setDate(date.getDate() - 1);
      break;
  }
  currentDate.value = date;
};

const nextPeriod = () => {
  const date = new Date(currentDate.value);
  switch (currentView.value) {
    case 'month':
      date.setMonth(date.getMonth() + 1);
      break;
    case 'week':
      date.setDate(date.getDate() + 7);
      break;
    case 'day':
      date.setDate(date.getDate() + 1);
      break;
  }
  currentDate.value = date;
};

const selectDate = (day: CalendarDay) => {
  console.log('Selected date:', day.date);
};

const showEventModal = ref(false);
const newEvent = ref<NewEvent>({
  title: '',
  date: new Date(),
  time: '09:00',
  petId: 0,
  type: 'feeding',
  notes: ''
});

const eventTypes = [
  { value: 'feeding', label: 'Feeding' },
  { value: 'vet', label: 'Vet Appointment' },
  { value: 'grooming', label: 'Grooming' },
  { value: 'medication', label: 'Medication' },
  { value: 'other', label: 'Other' }
];

const getEventColor = (type: string) => {
  switch (type) {
    case 'feeding':
      return '#4CAF50';
    case 'vet':
      return '#F44336';
    case 'grooming':
      return '#9C27B0';
    case 'medication':
      return '#FF9800';
    default:
      return '#2196F3';
  }
};

const selectTimeSlot = (date: Date, hour: number) => {
  newEvent.value = {
    title: '',
    date: new Date(date),
    time: `${hour.toString().padStart(2, '0')}:00`,
    petId: props.pets?.[0]?.id || 0,
    type: 'feeding',
    notes: ''
  };
  showEventModal.value = true;
};

const events = ref<CalendarEvent[]>([]);
const loading = ref(false);
const error = ref<string | null>(null);

const fetchEvents = async () => {
  loading.value = true;
  error.value = null;
  try {
    const response = await axios.get('/events');
    events.value = response.data.map((event: any) => ({
      ...event,
      start: new Date(event.start),
      end: new Date(event.end)
    }));
  } catch (err) {
    error.value = 'Failed to load events';
    console.error('Error loading events:', err);
  } finally {
    loading.value = false;
  }
};

const createEvent = async () => {
  if (!newEvent.value.title || !newEvent.value.petId) return;

  const pet = props.pets?.find(p => p.id === newEvent.value.petId);
  if (!pet) return;

  try {
    const startTime = new Date(newEvent.value.date);
    const [hours, minutes] = newEvent.value.time.split(':').map(Number);
    startTime.setHours(hours, minutes);

    const endTime = new Date(startTime);
    endTime.setHours(hours + 1, minutes);

    const response = await axios.post('/events', {
      title: newEvent.value.title,
      start_time: startTime,
      end_time: endTime,
      description: newEvent.value.notes,
      color: getEventColor(newEvent.value.type),
      pet_id: newEvent.value.petId
    });

    // Add the new event to the calendar
    events.value.push({
      id: response.data.id,
      title: response.data.title,
      start: new Date(response.data.start_time),
      end: new Date(response.data.end_time),
      description: response.data.description,
      color: response.data.color,
      petName: pet.name
    });

    showEventModal.value = false;
    
    // Reset the form
    newEvent.value = {
      title: '',
      date: new Date(),
      time: '09:00',
      petId: 0,
      type: 'feeding',
      notes: ''
    };
  } catch (err) {
    error.value = 'Failed to create event';
    console.error('Error creating event:', err);
  }
};

const deleteEvent = async (eventId: number) => {
  try {
    await axios.delete(`/events/${eventId}`);
    events.value = events.value.filter(e => e.id !== eventId);
    showEventModal.value = false;
    selectedEvent.value = null;
  } catch (err) {
    error.value = 'Failed to delete event';
    console.error('Error deleting event:', err);
  }
};

onMounted(() => {
  fetchEvents();
});

const selectEvent = (event: CalendarEvent) => {
  selectedEvent.value = event;
  showEventModal.value = true;
};

const formatDate = (date: Date) => {
  return date.toLocaleDateString('default', {
    weekday: 'long',
    year: 'numeric',
    month: 'long',
    day: 'numeric'
  });
};

const formatFullDate = (date: Date) => {
  return date.toLocaleDateString('default', {
    weekday: 'long',
    year: 'numeric',
    month: 'long',
    day: 'numeric'
  });
};

const formatDateRange = (start: Date, end: Date) => {
  const startMonth = start.toLocaleString('default', { month: 'short' });
  const endMonth = end.toLocaleString('default', { month: 'short' });
  const startDay = start.getDate();
  const endDay = end.getDate();
  const year = start.getFullYear();
  
  if (startMonth === endMonth) {
    return `${startMonth} ${startDay}-${endDay}, ${year}`;
  }
  return `${startMonth} ${startDay} - ${endMonth} ${endDay}, ${year}`;
};

const formatHour = (hour: number) => {
  return hour === 0 ? '12 AM' :
         hour < 12 ? `${hour} AM` :
         hour === 12 ? '12 PM' :
         `${hour - 12} PM`;
};

const formatEventTime = (date: Date) => {
  return date.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });
};

const formatEventDate = (date: Date) => {
  return date.toLocaleDateString([], { 
    weekday: 'short', 
    month: 'short', 
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

/* Month View Styles */
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

/* Week View Styles */
.week-view {
  border: 1px solid #e0e0e0;
  border-radius: 4px;
}

.week-header {
  display: grid;
  grid-template-columns: 60px repeat(7, 1fr);
  background: #f5f5f5;
  border-bottom: 1px solid #e0e0e0;
}

.week-day-header {
  padding: 10px;
  text-align: center;
  font-weight: bold;
  background: white;
  border-left: 1px solid #e0e0e0;
}

.week-day-header.today {
  background: #e3f2fd;
}

.date-number {
  font-size: 12px;
  color: #666;
  margin-top: 4px;
}

.week-body {
  display: grid;
  grid-template-columns: 60px repeat(7, 1fr);
  height: 600px;
  overflow-y: auto;
}

.time-column {
  background: #f5f5f5;
  border-right: 1px solid #e0e0e0;
}

.time-slot {
  height: 60px;
  padding: 4px;
  border-bottom: 1px solid #e0e0e0;
  font-size: 12px;
  color: #666;
}

.week-grid {
  display: grid;
  grid-template-columns: repeat(7, 1fr);
}

.week-day-column {
  border-right: 1px solid #e0e0e0;
}

.week-day-column.today {
  background: #e3f2fd;
}

/* Day View Styles */
.day-view {
  border: 1px solid #e0e0e0;
  border-radius: 4px;
}

.day-header {
  display: grid;
  grid-template-columns: 60px 1fr;
  background: #f5f5f5;
  border-bottom: 1px solid #e0e0e0;
}

.day-header-content {
  padding: 10px;
  text-align: center;
  font-weight: bold;
  background: white;
}

.day-body {
  display: grid;
  grid-template-columns: 60px 1fr;
  height: 600px;
  overflow-y: auto;
}

.day-grid {
  border-left: 1px solid #e0e0e0;
}

/* Event Styles */
.event {
  font-size: 12px;
  padding: 2px 4px;
  border-radius: 2px;
  color: white;
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
  margin-bottom: 2px;
  cursor: pointer;
}

/* Modal Styles */
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
  padding: 24px;
  border-radius: 8px;
  min-width: 400px;
  max-width: 500px;
}

.modal-content h3 {
  margin: 0 0 20px 0;
  color: #333;
}

/* Event Form Styles */
.event-form {
  display: flex;
  flex-direction: column;
  gap: 16px;
}

.form-group {
  display: flex;
  flex-direction: column;
  gap: 4px;
}

.form-group label {
  font-weight: 500;
  color: #333;
}

.form-group input,
.form-group select,
.form-group textarea {
  padding: 8px;
  border: 1px solid #e0e0e0;
  border-radius: 4px;
  font-size: 14px;
}

.form-group textarea {
  resize: vertical;
  min-height: 80px;
}

.form-actions {
  display: flex;
  justify-content: flex-end;
  gap: 8px;
  margin-top: 16px;
}

.btn-primary {
  background: #2196F3;
  color: white;
  border-color: #2196F3;
}

.btn-primary:hover {
  background: #1976D2;
}

.btn-secondary {
  background: #f5f5f5;
  color: #333;
  border-color: #e0e0e0;
}

.btn-secondary:hover {
  background: #e0e0e0;
}

/* Loading and Error States */
.loading-overlay {
  position: absolute;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background: rgba(255, 255, 255, 0.8);
  display: flex;
  align-items: center;
  justify-content: center;
  z-index: 1000;
}

.loading-spinner {
  width: 40px;
  height: 40px;
  border: 4px solid #f3f3f3;
  border-top: 4px solid #2196F3;
  border-radius: 50%;
  animation: spin 1s linear infinite;
}

.error-message {
  position: fixed;
  top: 20px;
  right: 20px;
  background: #f44336;
  color: white;
  padding: 16px;
  border-radius: 4px;
  display: flex;
  align-items: center;
  gap: 8px;
  z-index: 1000;
}

@keyframes spin {
  0% { transform: rotate(0deg); }
  100% { transform: rotate(360deg); }
}
</style>

