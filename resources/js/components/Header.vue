<template>
    <header class="flex justify-between items-center py-6 px-24">
        <div class="flex items-center space-x-2">
            <i class="fas fa-check text-white w-6 h-6"></i>
            <h1 class="text-xl font-oswald font-bold">Pomopensource</h1>
        </div>
        <div class="flex space-x-3">
            <button @click="$emit('toggleStats')" class="flex items-center space-x-1 py-2 px-3 bg-white/10 text-white rounded-full hover:bg-white/20">
                <i class="fa-solid fa-chart-simple"></i>
                <span class="text-sm font-inter">Stats</span>
            </button>
            <button class="flex items-center space-x-1 py-2 px-3 bg-white/10 text-white rounded-full hover:bg-white/20">
                <i class="fas fa-cog w-4 h-4"></i>
                <span class="text-sm font-inter">Settings</span>
            </button>
            <a class="py-2 px-3 bg-white/10 text-white rounded-full hover:bg-white/20" href="register">
                <i class="fas fa-user w-6 h-6"></i>
                <span class="text-sm font-inter">Login</span>
            </a>
        </div>
    </header>
</template>

<script>
export default {
    name: 'Header',
    emits: ['toggleStats'],
};
</script>

<style scoped>
/* Header specific styles */
</style>
