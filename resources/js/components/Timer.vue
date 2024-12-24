<template>
    <div class="flex flex-col items-center">
        <div class="flex space-x-4 mb-8">
            <button
                @click="setTimer('pomodoro', settings.timers.settings.pomodoro_duration)"
                id="default-timer"
                class="timer-button"
                :class="{ 'active-button': currentTimerType === 'pomodoro' }"
            >
                pomodoro
            </button>
            <button
                @click="setTimer('short_break', settings.timers.settings.short_break_duration)"
                class="timer-button"
                :class="{ 'active-button': currentTimerType === 'short_break' }"
            >
                short break
            </button>
            <button
                @click="setTimer('long_break', settings.timers.settings.long_break_duration)"
                class="timer-button"
                :class="{ 'active-button': currentTimerType === 'long_break' }"
            >
                long break
            </button>
        </div>

        <div id="timerDisplay" class="text-9xl font-oswald font-bold mb-8">
            {{ formattedTime }}
        </div>

        <div class="flex space-x-4 mb-8 cent">
            <button @click="toggleTimer" id="stop-start-button" class="control-button bg-white text-black border-2 border-transparent hover:bg-transparent hover:text-white hover:border-2 hover:border-white">
                {{ isRunning ? 'pause' : 'start' }}
            </button>
            <button @click="resetTimer" class="text-3xl">
                <i class="fas fa-sync-alt"></i>
            </button>
        </div>

        <!-- Dropdown for project and task selection -->
        <div v-if="!isRunning && currentTimerType === 'pomodoro'" class="mb-4" id="task-dropdown">
            <select v-model="selectedId"
                    class="bg-gray-100 text-gray-800 rounded-lg p-3 border border-gray-300 shadow-sm focus:ring-2 focus:ring-indigo-500 focus:outline-none hover:bg-gray-200 transition duration-150 ease-in-out">
                <option value="" class="text-gray-600">No specific project (General focus)</option>
                <template v-for="project in projects" :key="project.id">
                    <option :value="'project:rbNiqBehszLPVzMmR_' + project.id" class="font-bold">
                        {{ project.name }} (Project)
                    </option>
                    <template v-for="task in project.tasks" :key="task.id">
                        <option :value="'task:rbNiqBehszLPVzMmR_' + task.id">
                            - {{ task.name }}
                        </option>
                    </template>
                </template>
            </select>
        </div>
    </div>
</template>

<script>
import { ref, computed, watch, onMounted } from 'vue';
import axios from 'axios';

export default {
  props: {
    projects: {
      type: Array,
      required: true
    },
    settings: {
      type: Object,
      default: () => ({})
    }
  },
  setup(props) {
    const time = ref((props.settings?.timers?.settings?.pomodoro_duration ?? 25) * 60);
    const initialTime = ref(time.value);
    const alertVolume = ref(props.settings?.sound?.settings?.alert_volume ?? 50);
    const playSound = ref(props.settings?.sound?.settings?.play_sound ?? '0');
    const isRunning = ref(false);
    const timerInterval = ref(null);
    const selectedTaskId = ref('');
    const selectedId = ref('');
    const sessionStartTime = ref(null);
    const currentTimerType = ref('pomodoro');
    const audio = ref(null);

    // Track when we are restoring from localStorage to avoid overwriting timer
    let isRestoring = false;

    // -----------------------
    //   WATCHERS
    // -----------------------

    watch(
      () => props.settings?.sound?.settings?.alert_volume,
      (newVolume) => {
        alertVolume.value = newVolume;
      }
    );

    watch(
      () => props.settings?.sound?.settings?.play_sound,
      (newValue, oldValue) => {
        console.log(`playSound changed from ${oldValue} to ${newValue}`);
        playSound.value = newValue;
      }
    );

    // Watch for changes in timer settings
    // Only update timer from settings when we are NOT restoring
    // and only if the timer isn’t running, so we don’t overwrite a live countdown
    watch(
      () => props.settings.timers.settings,
      () => {
        if (!isRestoring && !isRunning.value) {
          updateTimerFromSettings();
        }
      },
      { deep: true }
    );

    // Whenever time changes and the timer is running, update localStorage
    watch(time, () => {
      if (isRunning.value) {
        saveTimerStateToLocalStorage();
      }
    });

    // -----------------------
    //   FUNCTIONS
    // -----------------------

    const formattedTime = computed(() => {
      const min = Math.floor(time.value / 60);
      const sec = time.value % 60;
      return `${min.toString().padStart(2, '0')}:${sec.toString().padStart(2, '0')}`;
    });

    const updateTimerFromSettings = () => {
      const duration = props.settings?.timers?.settings?.[`${currentTimerType.value}_duration`] ?? 25;
      time.value = duration * 60;
      initialTime.value = time.value;
    };

    const setTimer = (timerType) => {
      currentTimerType.value = timerType;
      updateTimerFromSettings();
      clearInterval(timerInterval.value);
      isRunning.value = false;
      localStorage.removeItem('pomodoroTimer');
    };

    const toggleTimer = () => {
      if (isRunning.value) pauseTimer();
      else startTimer();
    };

    const startTimer = () => {
      isRunning.value = true;
      sessionStartTime.value = new Date();

      timerInterval.value = setInterval(() => {
        time.value--;
        if (time.value <= 0) {
          clearInterval(timerInterval.value);
          isRunning.value = false;

          if (playSound.value === '1') playAlarmSound();
          if (currentTimerType.value === 'pomodoro') endSession();
        }
      }, 1000);

      saveTimerStateToLocalStorage();

      // Start a focused session only if it's a pomodoro
      if (currentTimerType.value === 'pomodoro') {
        const payload = {
          project_id: null,
          task_id: null,
          started_at: sessionStartTime.value
        };

        if (selectedId.value) {
          const selected = selectedId.value;
          if (selected.startsWith('project:rbNiqBehszLPVzMmR_')) {
            payload.project_id = extractAfterFirstUnderscore(selected);
          } else if (selected.startsWith('task:rbNiqBehszLPVzMmR_')) {
            payload.task_id = extractAfterFirstUnderscore(selected);
          }
        }

        axios
          .post('/focused-sessions', payload)
          .then((response) => {
            console.log('Session started successfully', response.data);
          })
          .catch((error) => {
            console.error('Error starting session', error);
          });
      }
    };

    const pauseTimer = () => {
      clearInterval(timerInterval.value);
      isRunning.value = false;
      saveTimerStateToLocalStorage();
    };

    const resetTimer = () => {
      clearInterval(timerInterval.value);
      isRunning.value = false;
      time.value = initialTime.value;

      if (sessionStartTime.value && currentTimerType.value === 'pomodoro') {
        endSession();
      }

      const duration = props.settings?.timers?.settings?.[`${currentTimerType.value}_duration`] ?? 25;
      time.value = duration * 60;
      initialTime.value = time.value;

      localStorage.removeItem('pomodoroTimer');
    };

    const endSession = () => {
      const payload = {
        ended_at: new Date(),
        time_focused: initialTime.value - time.value
      };

      axios
        .patch('/focused-sessions/current', payload)
        .then((response) => {
          console.log('Session ended successfully', response.data);
        })
        .catch((error) => {
          console.error('Error ending session', error);
        });

      // Reset the session-related state
      sessionStartTime.value = null;
      selectedTaskId.value = '';
    };

    const playAlarmSound = () => {
      const soundFile = props.settings?.sound?.settings?.alert_sound
        ? `${props.settings.sound.settings.alert_sound.toLowerCase()}.mp3`
        : 'alarm.mp3';

      audio.value = new Audio(`/sounds/${soundFile}`);
      const volume = Math.min(Math.max(parseInt(alertVolume.value) / 100, 0), 1);
      audio.value.volume = volume;
      audio.value.play().catch((error) => {
        console.error('Error playing sound:', error);
      });
    };

    function extractAfterFirstUnderscore(str) {
      const index = str.indexOf('_');
      return index !== -1 ? str.slice(index + 1) : '';
    }

    // -----------------------
    //   LOCALSTORAGE
    // -----------------------

    const saveTimerStateToLocalStorage = () => {
      if (isRunning.value) {
        // endTime is now + whatever is left in `time.value`
        const endTime = Date.now() + time.value * 1000;
        localStorage.setItem(
          'pomodoroTimer',
          JSON.stringify({
            isRunning: isRunning.value,
            endTime: endTime,
            currentTimerType: currentTimerType.value
          })
        );
      } else {
        localStorage.removeItem('pomodoroTimer');
      }
    };

    // -----------------------
    //   RESTORE ON MOUNT
    // -----------------------
    onMounted(() => {
      isRestoring = true; // we’re about to restore, so don’t overwrite time

      const storedData = localStorage.getItem('pomodoroTimer');
      if (!storedData) {
        // If there is no stored data, just set defaults from settings
        updateTimerFromSettings();
        isRestoring = false;
        return;
      }

      const { isRunning: storedIsRunning, endTime, currentTimerType: storedTimerType } =
        JSON.parse(storedData);

      // Restore the timer type
      if (storedTimerType) {
        currentTimerType.value = storedTimerType;
      }

      // compute the remaining time from the endTime
      const remainingSeconds = Math.floor((endTime - Date.now()) / 1000);

      if (remainingSeconds > 0) {
        time.value = remainingSeconds;
        initialTime.value = remainingSeconds;

        // If the timer was running before refresh, resume it
        if (storedIsRunning) {
          startTimer();
        }
      } else {
        // Timer expired already
        localStorage.removeItem('pomodoroTimer');
        updateTimerFromSettings();
      }

      isRestoring = false;
    });

    return {
      time,
      initialTime,
      isRunning,
      formattedTime,
      currentTimerType,
      selectedTaskId,
      selectedId,
      setTimer,
      toggleTimer,
      resetTimer,
      projects: computed(() => props.projects),
      settings: computed(() => props.settings)
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

.timer-button:focus {
    background-color: white;
    color: black;
}

.timer-button:hover {
    background-color: white;
    color: black;
}

.control-button {
    padding: 0.5rem 2rem;
    border-radius: 9999px;
    font-weight: 600;
}

.active-button {
    background-color: white;
    color: black;
}
</style>
