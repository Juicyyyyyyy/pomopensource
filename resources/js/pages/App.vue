<template>
    <div class="app min-h-screen bg-cover bg-center bg-no-repeat flex flex-col"
         style="background-image: url('https://res.cloudinary.com/dbg3uxasg/image/upload/v1723829502/yvknivddyzsgb8miz9hl.jpg');">
        <Header @toggleStats="toggleStatsModal" />
        <main class="flex flex-col items-center justify-center text-white">
            <ProjectsAndTasks :projects="projects" />
        </main>
        <Footer />
        <StatsModal v-if="showStatsModal" @close="toggleStatsModal" />
    </div>
</template>

<script>
import { ref } from 'vue';
import Header from '../components/Header.vue';
import Timer from '../components/Timer.vue';
import Footer from '../components/Footer.vue';
import ProjectsAndTasks from '../components/ProjectsAndTasks.vue';
import Calendar from "../components/FocusCalendar.vue";
import StatsModal from '../components/StatsModal.vue';

export default {
    components: {
        Calendar,
        Header,
        Timer,
        Footer,
        ProjectsAndTasks,
        StatsModal,
    },
    props: {
        projects: {
            type: Array,
            default: () => []
        },
    },
    setup() {
        const showStatsModal = ref(false);

        const toggleStatsModal = () => {
            showStatsModal.value = !showStatsModal.value;
        };

        return {
            showStatsModal,
            toggleStatsModal,
        };
    },
};
</script>

<style>
</style>
