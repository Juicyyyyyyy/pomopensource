<template>
    <div class="app">
        <Header />
        <main class="flex flex-col items-center justify-center text-white">
            <Timer />
            <AddProject />
        </main>
        <Footer />
    </div>
</template>

<script>
import Header from './components/Header.vue';
import Timer from './components/Timer.vue';
import AddProject from './components/AddProject.vue';
import Footer from './components/Footer.vue';

export default {
    components: {
        Header,
        Timer,
        AddProject,
        Footer,
    },
};
</script>

<style>
body {
    font-family: 'Inter', sans-serif;
    background-image: url('https://res.cloudinary.com/dbg3uxasg/image/upload/v1723829502/yvknivddyzsgb8miz9hl.jpg');
}
</style>
