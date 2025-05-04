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

    <!-- Add Event Modal -->
    <div v-if="showAddEventModal" class="modal">
      <div class="modal-content">
        <div class="modal-header">
          <h2 class="text-xl font-semibold">Add New Event</h2>
          <button @click="closeAddEventModal" class="close-button">
            <span class="sr-only">Close</span>
            <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
            </svg>
          </button>
        </div>

        <form @submit.prevent="createEvent" class="event-form">
          <div class="form-grid">
            <div class="form-group">
              <label for="title">Event Title</label>
              <input
                type="text"
                id="title"
                v-model="newEvent.title"
                required
                placeholder="Enter event title"
                class="form-input"
              />
            </div>

            <div class="form-group">
              <label for="pet">Pet</label>
              <select
                id="pet"
                v-model="newEvent.pet_id"
                required
                class="form-input"
              >
                <option value="">Select a pet</option>
                <option v-for="pet in localPets" :key="pet.id" :value="pet.id">
                  {{ pet.name }}
                </option>
              </select>
            </div>

            <div class="form-group">
              <label for="date">Date</label>
              <input
                type="date"
                id="date"
                v-model="newEvent.date"
                required
                class="form-input"
              />
            </div>

            <div class="form-group">
              <label for="time">Time</label>
              <input
                type="time"
                id="time"
                v-model="newEvent.time"
                required
                class="form-input"
              />
            </div>

            <div class="form-group full-width">
              <label for="description">Description</label>
              <textarea
                id="description"
                v-model="newEvent.description"
                rows="3"
                placeholder="Enter event description"
                class="form-input"
              ></textarea>
            </div>
          </div>

          <div class="form-actions">
            <button type="button" @click="closeAddEventModal" class="btn btn-secondary">
              Cancel
            </button>
            <button type="submit" class="btn btn-primary">
              Create Event
            </button>
          </div>
        </form>
      </div>
    </div>

    <!-- Event Details Modal -->
    <div v-if="selectedEvent && showEventDetailsModal" class="modal">
      <div class="modal-content">
        <div class="modal-header">
          <h2 class="text-xl font-semibold">{{ selectedEvent.title }}</h2>
          <button @click="closeEventDetailsModal" class="close-button">
            <span class="sr-only">Close</span>
            <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
            </svg>
          </button>
        </div>

        <div class="event-details">
          <div class="detail-row">
            <span class="detail-label">Date:</span>
            <span class="detail-value">{{ formatEventDate(selectedEvent) }}</span>
          </div>
          <div class="detail-row">
            <span class="detail-label">Time:</span>
            <span class="detail-value">{{ formatEventTime(selectedEvent) }}</span>
          </div>
          <div class="detail-row">
            <span class="detail-label">Pet:</span>
            <span class="detail-value">{{ selectedEvent.petName }}</span>
          </div>
          <div class="detail-row" v-if="selectedEvent.description">
            <span class="detail-label">Description:</span>
            <span class="detail-value">{{ selectedEvent.description }}</span>
          </div>
        </div>

        <div class="form-actions">
          <button @click="deleteEvent(selectedEvent.id)" class="btn btn-danger">
            Delete Event
          </button>
          <button @click="closeEventDetailsModal" class="btn btn-secondary">
            Close
          </button>
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
import { router } from '@inertiajs/vue3';

interface Pet {
  id: number;
  name: string;
  petImage: string | null;
  type: string;
  DOB: string;
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
  pet_id: number;
  description: string;
  type: 'feeding' | 'medication' | 'grooming' | 'vet' | 'other';
}

interface PageProps {
  events: CalendarEvent[];
  pets: Pet[];
}

const props = defineProps<{
  events: CalendarEvent[];
  pets: Pet[];
}>();

const localPets = ref<Pet[]>(props.pets);

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

const showAddEventModal = ref(false);
const newEvent = ref<NewEvent>({
  title: '',
  date: new Date(),
  time: '09:00',
  pet_id: 0,
  description: '',
  type: 'feeding'
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
    pet_id: props.pets?.[0]?.id || 0,
    description: '',
    type: 'feeding'
  };
  showAddEventModal.value = true;
};

const events = ref<CalendarEvent[]>([]);
const loading = ref(false);
const error = ref<string | null>(null);

const fetchEvents = async () => {
  try {
    loading.value = true;
    await router.reload({ 
      only: ['events'],
      onSuccess: (page) => {
        if (page.props.events) {
          events.value = page.props.events;
        }
      }
    });
  } catch (error: unknown) {
    console.error('Error fetching events:', error);
    error.value = 'Failed to load events. Please try again.';
  } finally {
    loading.value = false;
  }
};

const fetchPets = async () => {
  try {
    await router.reload({ 
      only: ['pets'],
      onSuccess: (page) => {
        console.log('Pets response:', page.props.pets); // Debug log
        if (page.props.pets) {
          localPets.value = page.props.pets;
        }
      }
    });
  } catch (error: unknown) {
    console.error('Error fetching pets:', error);
  }
};

const createEvent = async () => {
  if (!newEvent.value.title || !newEvent.value.pet_id) return;

  const pet = localPets.value.find(p => p.id === newEvent.value.pet_id);
  if (!pet) return;

  try {
    const startTime = new Date(newEvent.value.date);
    const [hours, minutes] = newEvent.value.time.split(':');
    startTime.setHours(parseInt(hours), parseInt(minutes));

    const endTime = new Date(startTime);
    endTime.setHours(endTime.getHours() + 1);

    await router.post('/events', {
      title: newEvent.value.title,
      start_time: startTime,
      end_time: endTime,
      description: newEvent.value.description,
      color: getEventColor(newEvent.value.type),
      pet_id: newEvent.value.pet_id
    }, {
      preserveState: true,
      preserveScroll: true,
      onSuccess: (page) => {
        if (page.props.event) {
          events.value = [...events.value, page.props.event];
        }
        showAddEventModal.value = false;
        newEvent.value = {
          title: '',
          date: new Date(),
          time: '09:00',
          pet_id: 0,
          description: '',
          type: 'feeding'
        };
      }
    });
  } catch (error: unknown) {
    console.error('Error creating event:', error);
    error.value = 'Failed to create event. Please try again.';
  }
};

const deleteEvent = async (eventId: number) => {
  try {
    await router.delete(`/events/${eventId}`, {
      preserveState: true,
      preserveScroll: true,
      onSuccess: () => {
        events.value = events.value.filter(e => e.id !== eventId);
        showEventDetailsModal.value = false;
        selectedEvent.value = null;
      }
    });
  } catch (error: unknown) {
    console.error('Error deleting event:', error);
    error.value = 'Failed to delete event. Please try again.';
  }
};

onMounted(() => {
  fetchEvents();
  fetchPets();
});

const showEventDetailsModal = ref(false);

const selectEvent = (event: CalendarEvent) => {
  selectedEvent.value = event;
  showEventDetailsModal.value = true;
};

const closeEventDetailsModal = () => {
  showEventDetailsModal.value = false;
  selectedEvent.value = null;
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

const formatEventDate = (event: CalendarEvent) => {
  return event.start.toLocaleDateString('en-US', {
    weekday: 'long',
    year: 'numeric',
    month: 'long',
    day: 'numeric'
  });
};

const formatEventTime = (event: CalendarEvent) => {
  return event.start.toLocaleTimeString('en-US', {
    hour: 'numeric',
    minute: '2-digit',
    hour12: true
  });
};

const closeAddEventModal = () => {
  showAddEventModal.value = false;
  newEvent.value = {
    title: '',
    date: new Date(),
    time: '09:00',
    pet_id: 0,
    description: '',
    type: 'feeding'
  };
};
</script>

<style scoped>
.calendar {
  position: relative;
  background: white;
  border-radius: 8px;
  box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
  padding: 20px;
  height: calc(100vh - 200px); /* Adjust based on your layout */
  display: flex;
  flex-direction: column;
  overflow: hidden;
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
  border: none;
  border-radius: 4px;
  cursor: pointer;
  font-size: 14px;
  font-weight: 500;
  transition: background-color 0.2s;
}

.btn:hover {
  opacity: 0.9;
}

.btn.active {
  background: #2196F3;
  color: white;
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
  display: flex;
  flex-direction: column;
  flex: 1;
  min-height: 0;
  overflow: hidden;
}

.week-header {
  display: grid;
  grid-template-columns: 60px repeat(7, 1fr);
  border-bottom: 1px solid #e0e0e0;
  background: #f5f5f5;
}

.week-day-header {
  padding: 10px;
  text-align: center;
  font-weight: 500;
  border-right: 1px solid #e0e0e0;
}

.week-day-header:last-child {
  border-right: none;
}

.week-body {
  display: flex;
  flex: 1;
  min-height: 0;
  overflow: auto;
  position: relative;
}

.time-column {
  width: 60px;
  background: #f5f5f5;
  border-right: 1px solid #e0e0e0;
  position: sticky;
  left: 0;
  z-index: 1;
}

.time-slot {
  height: 60px;
  padding: 5px;
  border-bottom: 1px solid #e0e0e0;
  font-size: 12px;
  color: #666;
}

.week-grid {
  display: grid;
  grid-template-columns: repeat(7, 1fr);
  flex: 1;
  min-width: 0;
}

.week-day-column {
  border-right: 1px solid #e0e0e0;
}

.week-day-column:last-child {
  border-right: none;
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
  padding: 4px 8px;
  border-radius: 4px;
  color: white;
  font-size: 12px;
  margin: 2px 0;
  cursor: pointer;
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
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
  z-index: 9999;
}

.modal-content {
  background: white;
  border-radius: 8px;
  width: 90%;
  max-width: 600px;
  max-height: 90vh;
  overflow-y: auto;
  position: relative;
  z-index: 10000;
  box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
}

.modal-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 20px;
  border-bottom: 1px solid #e0e0e0;
}

.close-button {
  background: none;
  border: none;
  padding: 4px;
  cursor: pointer;
  color: #666;
  border-radius: 4px;
  transition: all 0.2s;
}

.close-button:hover {
  background: #f5f5f5;
  color: #333;
}

/* Form styles */
.event-form {
  padding: 20px;
}

.form-grid {
  display: grid;
  grid-template-columns: repeat(2, 1fr);
  gap: 20px;
  margin-bottom: 20px;
}

.form-group {
  display: flex;
  flex-direction: column;
  gap: 8px;
}

.form-group.full-width {
  grid-column: 1 / -1;
}

.form-group label {
  font-weight: 500;
  color: #333;
  font-size: 14px;
}

.form-input {
  padding: 10px;
  border: 1px solid #ddd;
  border-radius: 6px;
  font-size: 14px;
  transition: border-color 0.2s;
  background: white;
}

.form-input:focus {
  outline: none;
  border-color: #2196F3;
  box-shadow: 0 0 0 2px rgba(33, 150, 243, 0.1);
}

.form-input::placeholder {
  color: #999;
}

textarea.form-input {
  resize: vertical;
  min-height: 80px;
}

.form-actions {
  display: flex;
  justify-content: flex-end;
  gap: 12px;
  padding-top: 20px;
  border-top: 1px solid #e0e0e0;
}

.btn {
  padding: 10px 20px;
  border: none;
  border-radius: 6px;
  font-size: 14px;
  font-weight: 500;
  cursor: pointer;
  transition: all 0.2s;
}

.btn-primary {
  background: #2196F3;
  color: white;
}

.btn-primary:hover {
  background: #1976D2;
}

.btn-secondary {
  background: #f5f5f5;
  color: #333;
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
  z-index: 1002;
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
  z-index: 1003;
}

@keyframes spin {
  0% { transform: rotate(0deg); }
  100% { transform: rotate(360deg); }
}

.event-details {
  padding: 20px;
}

.detail-row {
  display: flex;
  margin-bottom: 12px;
}

.detail-label {
  font-weight: 500;
  color: #666;
  width: 100px;
}

.detail-value {
  color: #333;
  flex: 1;
}
</style>

