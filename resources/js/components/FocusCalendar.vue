<template>
    <div class="focus-calendar">
        <h2 class="text-xl font-semibold mb-2">Focus Hours</h2>
        <div class="flex justify-between items-center mb-4">
            <button @click="changeMonth(-1)" class="btn">&lt; Prev</button>
            <h3 class="text-lg font-medium">{{ monthYear }}</h3>
            <button @click="changeMonth(1)" class="btn">Next &gt;</button>
        </div>
        <div v-if="isLoading" class="text-center py-4">
            Loading calendar data...
        </div>
        <div v-else-if="calendarDays.length > 0" class="grid grid-cols-7 gap-2">
            <div v-for="day in ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat']" :key="day" class="text-center font-medium">
                {{ day }}
            </div>
            <div v-for="(day, index) in calendarDays" :key="index" class="day-cell" :class="{ 'has-session': day.has_session }">
                <span class="day-number">{{ day.dayOfMonth }}</span>
                <span v-if="day.has_session" class="minutes-focused">{{ (day.minutes_focused / 60).toFixed(1) }}h</span>
            </div>
        </div>
        <div v-else class="text-center py-4">
            No calendar data available.
        </div>
    </div>
</template>

<script>
import { computed, ref, watch } from 'vue';

export default {
    props: {
        calendar: {
            type: Array,
            default: () => [],
        },
        currentYear: {
            type: Number,
            required: true,
        },
        currentMonth: {
            type: Number,
            required: true,
        },
    },
    emits: ['changeMonth'],
    setup(props, {emit}) {
        const isLoading = ref(true);

        const monthYear = computed(() => {
            const date = new Date(props.currentYear, props.currentMonth - 1);
            return date.toLocaleString('default', {month: 'long', year: 'numeric'});
        });

        const calendarDays = computed(() => {
            if (props.calendar.length === 0) return [];

            const firstDay = new Date(props.currentYear, props.currentMonth - 1, 1);
            const lastDay = new Date(props.currentYear, props.currentMonth, 0);
            const days = [];

            for (let i = 0; i < firstDay.getDay(); i++) {
                days.push({dayOfMonth: '', has_session: false});
            }

            for (let i = 0; i < lastDay.getDate(); i++) {
                const calendarDay = props.calendar[i] || {has_session: false, minutes_focused: 0};
                days.push({
                    dayOfMonth: i + 1,
                    has_session: calendarDay.has_session,
                    minutes_focused: calendarDay.minutes_focused,
                });
            }

            return days;
        });

        const changeMonth = (increment) => {
            let newMonth = props.currentMonth + increment;
            let newYear = props.currentYear;

            if (newMonth > 12) {
                newMonth = 1;
                newYear++;
            } else if (newMonth < 1) {
                newMonth = 12;
                newYear--;
            }

            emit('changeMonth', newYear, newMonth);
        };

        watch(() => props.calendar, (newCalendar) => {
            isLoading.value = newCalendar.length === 0;
        }, {immediate: true});

        return {
            monthYear,
            calendarDays,
            changeMonth,
            isLoading,
        };
    },
};
</script>

<style scoped>
/* ... (styles remain the same) ... */
</style>
