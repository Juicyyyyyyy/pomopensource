<template>
    <div class="w-full max-w-lg py-8 px-6 bg-white/10 rounded-lg shadow-lg">
        <div class="flex justify-between items-center mb-4">
            <h2 class="text-lg font-semibold font-oswald">Projects</h2>
        </div>
        <form @submit.prevent="addProject">
            <div class="mb-4">
                <input v-model="projectName" type="text" name="name" placeholder="Project Name" required
                       class="w-full py-3 px-4 bg-white/10 border border-white/30 rounded-lg text-center text-sm font-inter font-semibold text-white placeholder-white/70 focus:outline-none focus:border-white focus:ring-1 focus:ring-white transition duration-200">
            </div>
            <button type="submit"
                    class="w-full py-3 border-2 border-dashed border-white rounded-lg text-center text-sm font-inter font-semibold text-white hover:bg-white/20 hover:border-white transition duration-200">
                + Add Project
            </button>
        </form>
    </div>
</template>

<script>

</script>

<style scoped>
/* Add project specific styles */
</style>
