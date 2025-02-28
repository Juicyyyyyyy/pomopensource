<template>
    <div class="calendar bg-white/10 backdrop-blur-lg rounded-lg p-6 shadow-lg">
        <div class="flex justify-between items-center mb-4">
            <button @click="previousPeriod" class="text-white hover:text-gray-300 transition">
                <i class="fas fa-chevron-left"></i>
            </button>
            <h3 class="text-lg font-semibold text-white">{{ currentPeriodLabel }}</h3>
            <button @click="nextPeriod" class="text-white hover:text-gray-300 transition">
                <i class="fas fa-chevron-right"></i>
            </button>
        </div>

        <div class="flex space-x-2 mb-4">
            <button
                v-for="view in ['Week', 'Month', 'Year']"
                :key="view"
                :class="['px-4 py-2 rounded-lg text-sm font-semibold transition',
                    currentView === view.toLowerCase() ? 'bg-white/20 text-white' : 'bg-white/10 text-white/70 hover:bg-white/20']"
                @click="changeView(view.toLowerCase())"
            >
                {{ view }}
            </button>
        </div>

        <div v-if="currentView === 'week'" class="grid grid-cols-7 gap-1" id="weekly-view">
            <div v-for="day in daysOfWeek" :key="day" class="text-center text-sm font-medium text-white/70">
                {{ day }}
            </div>
            <div
                v-for="(day, index) in weekCalendarDays"
                :key="index"
                class="day-cell aspect-square flex flex-col items-center justify-center text-sm rounded-lg p-2 transition hover:bg-white/10"
                :class="getDayClasses(day)"
            >
                <span class="text-white">{{ day.date.getDate() }}</span>
                <span v-if="day.minutesFocused > 0" class="text-xs mt-1 text-white font-bold">
                    {{ formatTime(day.minutesFocused) }}
                </span>
            </div>
        </div>

        <div v-else-if="currentView === 'month'" class="grid grid-cols-7 gap-1" id="monthly-view">
            <div v-for="day in daysOfWeek" :key="day" class="text-center text-sm font-medium text-white/70">
                {{ day }}
            </div>
            <div
                v-for="(day, index) in monthCalendarDays"
                :key="index"
                class="day-cell aspect-square flex flex-col items-center justify-center text-sm rounded-lg p-2 transition hover:bg-white/10"
                :class="getDayClasses(day)"
            >
                <span v-if="day.date" class="text-white">{{ day.date.getDate() }}</span>
                <span v-if="day.minutesFocused > 0" class="text-xs mt-1 text-white font-bold">
                    {{ formatTime(day.minutesFocused) }}
                </span>
            </div>
        </div>

        <div v-else-if="currentView === 'year'" class="grid grid-cols-4 gap-4" id="yearly-view">
            <div
                v-for="(month, index) in yearCalendarMonths"
                :key="index"
                :class="getMonthClasses(month)"
            >
                <span class="font-medium">{{ month.name }}</span>
                <span class="text-sm mt-1 text-white">{{ formatTime(month.minutesFocused) }}</span>
            </div>
        </div>



        <p class="mt-4 text-center text-white">
            Current streak: <span class="font-bold">{{ currentStreak }}</span> days
        </p>
    </div>
</template>


<script>
import { ref, computed, onMounted, watch } from 'vue';
import axios from 'axios';

export default {
    setup() {
        const currentDate = ref(new Date());
        const currentView = ref('month');
        const calendarData = ref([]);
        const currentStreak = ref(0);

        const daysOfWeek = ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'];

        const currentPeriodLabel = computed(() => {
            if (currentView.value === 'week') {
                const weekStart = getWeekStart(currentDate.value);
                const weekEnd = new Date(weekStart);
                weekEnd.setDate(weekEnd.getDate() + 6);
                return `${weekStart.toLocaleDateString('default', { month: 'short', day: 'numeric' })} - ${weekEnd.toLocaleDateString('default', { month: 'short', day: 'numeric', year: 'numeric' })}`;
            } else if (currentView.value === 'month') {
                return currentDate.value.toLocaleString('default', { month: 'long', year: 'numeric' });
            } else {
                return currentDate.value.getFullYear().toString();
            }
        });

        const weekCalendarDays = computed(() => {
            const weekStart = getWeekStart(currentDate.value);
            return [...Array(7)].map((_, i) => {
                const date = new Date(weekStart);
                date.setDate(date.getDate() + i);
                const dayData = calendarData.value.find(d => new Date(d.date).toDateString() === date.toDateString()) || {};
                return {
                    date,
                    hasSession: dayData.has_session || false,
                    minutesFocused: dayData.minutes_focused || 0
                };
            });
        });

        const monthCalendarDays = computed(() => {
            const year = currentDate.value.getFullYear();
            const month = currentDate.value.getMonth();
            const firstDay = new Date(year, month, 1);
            const lastDay = new Date(year, month + 1, 0);
            const daysInMonth = lastDay.getDate();

            let days = [];

            // Add empty cells for days before the first of the month
            for (let i = 0; i < firstDay.getDay(); i++) {
                days.push({ date: null });
            }

            // Add days of the month
            for (let i = 1; i <= daysInMonth; i++) {
                const date = new Date(year, month, i);
                const dayData = calendarData.value.find(d => new Date(d.date).toDateString() === date.toDateString()) || {};
                days.push({
                    date,
                    hasSession: dayData.has_session || false,
                    minutesFocused: dayData.minutes_focused || 0
                });
            }

            return days;
        });

        const yearCalendarMonths = computed(() => {
            return [...Array(12)].map((_, i) => {
                const monthData = calendarData.value.filter(d => new Date(d.date).getMonth() === i);
                return {
                    name: new Date(currentDate.value.getFullYear(), i, 1).toLocaleString('default', { month: 'short' }),
                    minutesFocused: monthData.reduce((sum, day) => sum + day.minutes_focused, 0)
                };
            });
        });

        const fetchCalendarData = async () => {
            try {
                let url;
                if (currentView.value === 'week') {
                    const weekStart = getWeekStart(currentDate.value);
                    url = `/user-stats/calendar/${weekStart.getFullYear()}/${weekStart.getMonth() + 1}/${weekStart.getDate()}?view=week`;
                } else if (currentView.value === 'month') {
                    url = `/user-stats/calendar/${currentDate.value.getFullYear()}/${currentDate.value.getMonth() + 1}?view=month`;
                } else {
                    url = `/user-stats/calendar/${currentDate.value.getFullYear()}?view=year`;
                }
                const response = await axios.get(url);
                calendarData.value = response.data.calendar;
                currentStreak.value = response.data.currentStreak;
            } catch (error) {
                console.error('Error fetching calendar data:', error);
            }
        };


        const previousPeriod = () => {
            if (currentView.value === 'week') {
                currentDate.value = new Date(currentDate.value.setDate(currentDate.value.getDate() - 7));
            } else if (currentView.value === 'month') {
                currentDate.value = new Date(currentDate.value.setMonth(currentDate.value.getMonth() - 1));
            } else {
                currentDate.value = new Date(currentDate.value.setFullYear(currentDate.value.getFullYear() - 1));
            }
        };

        const nextPeriod = () => {
            if (currentView.value === 'week') {
                currentDate.value = new Date(currentDate.value.setDate(currentDate.value.getDate() + 7));
            } else if (currentView.value === 'month') {
                currentDate.value = new Date(currentDate.value.setMonth(currentDate.value.getMonth() + 1));
            } else {
                currentDate.value = new Date(currentDate.value.setFullYear(currentDate.value.getFullYear() + 1));
            }
        };


        const changeView = (view) => {
            currentView.value = view;
            fetchCalendarData();
        };

        const getDayClasses = (day) => {
            if (!day.date) return 'invisible';
            let classes = 'cursor-pointer hover:bg-white/10';

            // Add background color based on session intensity
            if (day.hasSession) {
                const intensityLevels = ['200', '400', '600', '800'];
                const intensityIndex = Math.min(Math.floor(day.minutesFocused / 60), intensityLevels.length - 1);
                classes += ` bg-blue-${intensityLevels[intensityIndex]}/50`;
            }
            return classes;
        };

        const getMonthClasses = (month) => {
            let classes = 'flex flex-col items-center justify-center rounded-lg p-4 text-white transition duration-200 ease-in-out';
            if (month.minutesFocused > 0) {
                const intensityLevels = ['200', '400', '600', '800'];
                const maxMinutesPerMonth = 30 * 90; // max intensity: 1h30 per day
                const intensityIndex = Math.min(
                    Math.floor(month.minutesFocused / maxMinutesPerMonth * intensityLevels.length),
                    intensityLevels.length - 1
                );
                classes += ` bg-blue-${intensityLevels[intensityIndex]}/50 font-bold`;
            } else {
                classes += ' bg-white/10';
            }
            return classes;
        };


        const formatTime = (minutes) => {
            const hours = Math.floor(minutes / 60);
            const mins = minutes % 60;
            return `${hours}h ${mins}m`;
        };

        const getWeekStart = (date) => {
            const d = new Date(date);
            const day = d.getDay();
            const diff = d.getDate() - day + (day === 0 ? -6 : 1);
            return new Date(d.setDate(diff));
        };

        onMounted(fetchCalendarData);

       watch([currentDate, currentView], fetchCalendarData);

        return {
            currentDate,
            currentView,
            currentPeriodLabel,
            daysOfWeek,
            weekCalendarDays,
            monthCalendarDays,
            yearCalendarMonths,
            currentStreak,
            previousPeriod,
            nextPeriod,
            changeView,
            getDayClasses,
            getMonthClasses,
            formatTime
        };
    }
};
</script>

<style scoped>
.calendar {
    background-color: rgba(255, 255, 255, 0.1);
    padding: 1.5rem;
    border-radius: 0.75rem;
    backdrop-filter: blur(10px);
}

.day-cell {
    position: relative;
    cursor: pointer;
}

.day-cell span:first-child {
    font-size: 1.25rem;
    font-weight: bold;
    color: #fff;
}

.day-cell span:last-child {
    font-size: 0.875rem;
    font-weight: bold;
    color: rgba(255, 255, 255, 0.9);
}

/* Adjusted class for days with sessions to enhance visibility */
.bg-green-200-50 {
    background-color: rgba(110, 231, 183, 0.5);
}

.bg-green-400-50 {
    background-color: rgba(52, 211, 153, 0.5);
}

.bg-green-600-50 {
    background-color: rgba(16, 185, 129, 0.5);
}

.bg-green-800-50 {
    background-color: rgba(5, 150, 105, 0.5);
}

button {
    outline: none;
}
</style>

