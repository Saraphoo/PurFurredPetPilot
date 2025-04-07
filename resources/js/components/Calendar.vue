<script setup lang="ts">
import { ref, onMounted } from 'vue';
import { useDate } from 'vuetify';

const type = ref<'month' | 'week' | 'day'>('month');
const types = ['month', 'week', 'day'];
const weekday = ref([0, 1, 2, 3, 4, 5, 6]);
const weekdays = [
    { title: 'Sun - Sat', value: [0, 1, 2, 3, 4, 5, 6] },
    { title: 'Mon - Sun', value: [1, 2, 3, 4, 5, 6, 0] },
    { title: 'Mon - Fri', value: [1, 2, 3, 4, 5] },
    { title: 'Mon, Wed, Fri', value: [1, 3, 5] },
];
const value = ref([new Date()]);
const events = ref<{ title: string; start: Date; end: Date; color: string; allDay: boolean; }[]>([]);
const colors = ['blue', 'indigo', 'deep-purple', 'cyan', 'green', 'orange', 'grey darken-1'];
const titles = ['Meeting', 'Holiday', 'PTO', 'Travel', 'Event', 'Birthday', 'Conference', 'Party'];

const getEvents = ({ start, end }: { start: Date; end: Date }) => {
    const eventList = [];
    const min = start;
    const max = end;
    const days = (max.getTime() - min.getTime()) / 86400000;
    const eventCount = rnd(days, days + 20);

    for (let i = 0; i < eventCount; i++) {
        const allDay = rnd(0, 3) === 0;
        const firstTimestamp = rnd(min.getTime(), max.getTime());
        const first = new Date(firstTimestamp - (firstTimestamp % 900000));
        const secondTimestamp = rnd(2, allDay ? 288 : 8) * 900000;
        const second = new Date(first.getTime() + secondTimestamp);

        eventList.push({
            title: titles[rnd(0, titles.length - 1)],
            start: first,
            end: second,
            color: colors[rnd(0, colors.length - 1)],
            allDay: !allDay,
        });
    }

    events.value = eventList;
};

const rnd = (a: number, b: number) => {
    return Math.floor((b - a + 1) * Math.random()) + a;
};

onMounted(() => {
    const adapter = useDate() as { startOfDay: (date: Date) => Date; startOfMonth: (date: Date) => Date; endOfDay: (date: Date) => Date; endOfMonth: (date: Date) => Date; };
    getEvents({ start: adapter.startOfDay(adapter.startOfMonth(new Date())), end: adapter.endOfDay(adapter.endOfMonth(new Date())) });
});
</script>
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
                :weekday="weekday"
            ></v-calendar>
        </v-sheet>
    </div>
</template>

