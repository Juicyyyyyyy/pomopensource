<template>
    <div class="fixed inset-0 bg-black bg-opacity-70 flex items-center justify-center z-50">
        <div class="w-full max-w-2xl p-6 bg-white/10 backdrop-blur-lg rounded-lg shadow-xl transform transition-all duration-300 ease-in-out">
            <div class="flex justify-between items-center pb-4 border-b border-white/20">
                <h2 class="text-2xl font-bold font-oswald text-white">Report</h2>
                <button @click="$emit('close')" class="text-white hover:text-gray-300 transition duration-150 ease-in-out">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <div class="pt-6">
                <div class="flex space-x-2 mb-6">
                    <button
                        v-for="tab in ['Summary', 'Detail', 'Ranking']"
                        :key="tab"
                        :class="['px-4 py-2 rounded-lg text-sm font-semibold transition',
                                 activeTab === tab ? 'bg-white/20 text-white' : 'bg-white/10 text-white/70 hover:bg-white/20']"
                        @click="activeTab = tab"
                    >
                        {{ tab }}
                    </button>
                </div>

                <h3 class="text-lg font-semibold font-oswald text-white mb-4">Activity Summary</h3>
                <ActivitySummary :stats="stats" />
            </div>
        </div>
    </div>
</template>


<script>
import { ref, onMounted } from 'vue';
import axios from 'axios';
import ActivitySummary from './ActivitySummary.vue';

export default {
    components: {
        ActivitySummary,
    },
    emits: ['close'],
    setup() {
        const activeTab = ref('Summary');
        const stats = ref({
            hours_focused: '--',
            days_accessed: '--',
            day_streak: '--'
        });

        const fetchStats = async () => {
            try {
                const response = await axios.get('/user-stats');
                stats.value = response.data.stats;
            } catch (error) {
                console.error('Error fetching stats:', error);
            }
        };

        onMounted(fetchStats);

        return {
            activeTab,
            stats,
        };
    },
};
</script>

<style scoped>
/* Add any additional styles here */
</style>
