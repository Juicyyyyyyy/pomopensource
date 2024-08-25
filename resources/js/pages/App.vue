<template>
    <div class="app">
        <Header />
        <main class="flex flex-col items-center justify-center text-white">
            <Timer />
            <ProjectsAndTasks :projects="projects" />
        </main>
        <Footer />
    </div>
</template>

<script>
import Header from '../components/Header.vue';
import Timer from '../components/Timer.vue';
import Footer from '../components/Footer.vue';
import ProjectsAndTasks from '../components/ProjectsAndTasks.vue';

export default {
    components: {
        Header,
        Timer,
        Footer,
        ProjectsAndTasks,
    },
    props: {
        projects: {
            type: Array,
            default: () => []
        },
    },
};
</script>

<style>
body {
    font-family: 'Inter', sans-serif;
    background-image: url('https://t4.ftcdn.net/jpg/02/40/63/55/360_F_240635575_EJifwRAbKsVTDnA3QE0bCsWG5TLhUNEZ.jpg');
}
</style>
