<template>
    <div class="flex flex-col items-center">
        <div class="flex space-x-4 mb-8">
            <button @click="setTimer('pomodoro', 25)" id="default-timer" class="timer-button">
                pomodoro
            </button>
            <button @click="setTimer('shortBreak', 5)" class="timer-button">
                short break
            </button>
            <button @click="setTimer('longBreak', 15)" class="timer-button">
                long break
            </button>
        </div>

        <div id="timerDisplay" class="text-9xl font-oswald font-bold mb-8">
            {{ formattedTime }}
        </div>

        <div class="flex space-x-4 mb-8 cent">
            <button @click="toggleTimer" id="stop-start-button" class="control-button">
                {{ isRunning ? 'pause' : 'start' }}
            </button>
            <button @click="resetTimer" class="text-3xl">
                <i class="fas fa-sync-alt"></i>
            </button>
            <button class="text-3xl">
                <i class="fas fa-cog"></i>
            </button>
        </div>

        <!-- Dropdown for project and task selection -->
        <div v-if="!isRunning && currentTimerType === 'pomodoro'" class="mb-4" id="task-dropdown">
            <select v-model="selectedId"
                    class="bg-gray-100 text-gray-800 rounded-lg p-3 border border-gray-300 shadow-sm focus:ring-2 focus:ring-indigo-500 focus:outline-none hover:bg-gray-200 transition duration-150 ease-in-out">
                <option value="" class="text-gray-600">No specific project (General focus)</option>
                <template v-for="project in projects" :key="project.id">
                    <option :value="'project_rbNiqBehszLPVzMmR_' + project.id" class="font-bold">
                        {{ project.name }} (Project)
                    </option>
                    <template v-for="task in project.tasks" :key="task.id">
                        <option :value="'task_rbNiqBehszLPVzMmR_' + task.id">
                            - {{ task.name }}
                        </option>
                    </template>
                </template>
            </select>
        </div>
    </div>
</template>

<script>
import { ref, computed } from 'vue';
import { Inertia } from '@inertiajs/inertia';

export default {
    props: {
        projects: {
            type: Array,
            required: true
        }
    },
    setup(props) {
        const time = ref(25 * 60);
        const initialTime = ref(25 * 60);
        const isRunning = ref(false);
        const timerInterval = ref(null);
        const selectedTaskId = ref('');
        const selectedId = ref('')
        const sessionStartTime = ref(null);
        const currentTimerType = ref('pomodoro');

        const formattedTime = computed(() => {
            const min = Math.floor(time.value / 60);
            const sec = time.value % 60;
            return `${min.toString().padStart(2, '0')}:${sec.toString().padStart(2, '0')}`;
        });

        const setTimer = (timerType, minutes) => {
            time.value = minutes * 60;
            initialTime.value = time.value;
            clearInterval(timerInterval.value);
            isRunning.value = false;
            currentTimerType.value = timerType;
        };

        const toggleTimer = () => {
            if (isRunning.value) {
                pauseTimer();
            } else {
                startTimer();
            }
        };

        const startTimer = () => {
            isRunning.value = true;
            sessionStartTime.value = new Date();
            timerInterval.value = setInterval(() => {
                time.value--;
                if (time.value <= 0) {
                    clearInterval(timerInterval.value);
                    isRunning.value = false;
                    if (currentTimerType.value === 'pomodoro') {
                        endSession();
                    }
                }
            }, 1000);

            // Start the focused session only for pomodoro timer
            if (currentTimerType.value === 'pomodoro') {
                if (selectedId.value) {
                    const selected = selectedId.value;
                    Inertia.post('/focused-sessions', {
                        project_id: selected.startsWith('project_rbNiqBehszLPVzMmR_') ? parseInt(selected.split('_')[1]) : null,
                        task_id: selected.startsWith('task_rbNiqBehszLPVzMmR_') ? parseInt(selected.split('_')[1]) : null,
                        started_at: sessionStartTime.value,
                    }, {
                        preserveState: true,
                    });
                } else {
                    // If no task or project is selected
                    Inertia.post('/focused-sessions', {
                        project_id: null,
                        task_id: null,
                        started_at: sessionStartTime.value,
                    }, {
                        preserveState: true,
                    });
                }
            }
        };

        const pauseTimer = () => {
            clearInterval(timerInterval.value);
            isRunning.value = false;
        };

        const resetTimer = () => {
            clearInterval(timerInterval.value);
            isRunning.value = false;
            time.value = initialTime.value;
            if (sessionStartTime.value && currentTimerType.value === 'pomodoro') {
                endSession();
            }
        };

        const endSession = () => {
            Inertia.patch('/focused-sessions/current', {
                ended_at: new Date(),
                time_focused: initialTime.value - time.value,
            }, {
                preserveState: true,
            });
            sessionStartTime.value = null;
            selectedTaskId.value = '';
        };

        return {
            time,
            isRunning,
            formattedTime,
            selectedTaskId,
            selectedId,
            currentTimerType,
            setTimer,
            toggleTimer,
            resetTimer,
            projects: computed(() => props.projects),
        };
    }
};
</script>


<style scoped>
.timer-button {
    padding: 0.5rem 1rem;
    border: 1px solid white;
    color: white;
    border-radius: 9999px;
    transition: all 0.2s;
}
.timer-button:hover {
    background-color: white;
    color: gray;
}
.control-button {
    padding: 0.5rem 2rem;
    background-color: white;
    color: gray;
    border-radius: 9999px;
    font-weight: 600;
}
</style>
