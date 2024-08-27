<script>
import { ref, watch } from 'vue';
import { Inertia } from '@inertiajs/inertia';
import { usePage } from '@inertiajs/inertia-vue3';

export default {
    props: {
        initialYear: Number,
        initialMonth: Number,
    },
    setup(props) {
        const page = usePage();
        const calendar = ref(page.props.value.calendar || []);
        const currentStreak = ref(page.props.value.currentStreak || 0);
        const currentMonth = ref(props.initialMonth);
        const currentYear = ref(props.initialYear);

        const fetchCalendarData = () => {
            Inertia.get(`/user-stats/calendar/${currentYear.value}/${currentMonth.value}`, {}, {
                preserveState: true,
                preserveScroll: true,
                only: ['calendar', 'currentStreak'],
            });
        };

        watch(() => page.props.value.calendar, (newCalendar) => {
            calendar.value = newCalendar;
        });

        watch(() => page.props.value.currentStreak, (newStreak) => {
            currentStreak.value = newStreak;
        });

        const changeMonth = (increment) => {
            let newMonth = currentMonth.value + increment;
            let newYear = currentYear.value;

            if (newMonth > 12) {
                newMonth = 1;
                newYear++;
            } else if (newMonth < 1) {
                newMonth = 12;
                newYear--;
            }

            currentMonth.value = newMonth;
            currentYear.value = newYear;
            fetchCalendarData();
        };

        return {
            calendar,
            currentStreak,
            currentMonth,
            currentYear,
            changeMonth,
        };
    },
};
</script>

<style scoped>
.focus-calendar {
    /* Add your styles here */
}
.calendar-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 1rem;
}
.calendar-grid {
    display: grid;
    grid-template-columns: repeat(7, 1fr);
    gap: 0.5rem;
}
.calendar-day {
    padding: 0.5rem;
    border: 1px solid #ccc;
    border-radius: 4px;
    display: flex;
    flex-direction: column;
    align-items: center;
}
.calendar-day.active {
    font-weight: bold;
}
</style>
