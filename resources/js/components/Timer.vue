<template>
    <div class="flex flex-col items-center">
        <div class="flex space-x-4 mb-8">
            <button @click="setTimer(25)" id="default-timer" class="timer-button">
                pomodoro
            </button>
            <button @click="setTimer(5)" class="timer-button">
                short break
            </button>
            <button @click="setTimer(15)" class="timer-button">
                long break
            </button>
        </div>

        <div id="timerDisplay" class="text-9xl font-oswald font-bold mb-8">
            {{ formattedTime }}
        </div>

        <div class="flex space-x-4 mb-8 cent">
            <button @click="startTimer" id="stop-start-button" class="control-button">
                start
            </button>
            <button @click="resetTimer" class="text-3xl">
                <i class="fas fa-sync-alt"></i>
            </button>
            <button class="text-3xl">
                <i class="fas fa-cog"></i>
            </button>
        </div>
    </div>
</template>

<script>
export default {
    data() {
        return {
            time: 25 * 60,
            initialTime: 25 * 60,
            isRunning: false,
            isPaused: false,
            timerInterval: null,
        };
    },
    computed: {
        formattedTime() {
            const min = Math.floor(this.time / 60);
            const sec = this.time % 60;
            return `${min.toString().padStart(2, '0')}:${sec.toString().padStart(2, '0')}`;
        },
    },
    methods: {
        setTimer(minutes) {
            this.time = minutes * 60;
            this.initialTime = this.time;
            clearInterval(this.timerInterval);
            this.isRunning = false;
        },
        startTimer() {
            if (this.isRunning && !this.isPaused) {
                this.pauseTimer();
                return;
            }

            this.isRunning = true;
            this.isPaused = false;
            this.timerInterval = setInterval(() => {
                this.time--;
                if (this.time <= 0) {
                    clearInterval(this.timerInterval);
                    this.isRunning = false;
                }
            }, 1000);
        },
        pauseTimer() {
            clearInterval(this.timerInterval);
            this.isRunning = false;
            this.isPaused = true;
        },
        resetTimer() {
            clearInterval(this.timerInterval);
            this.isRunning = false;
            this.time = this.initialTime;
        },
    },
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
