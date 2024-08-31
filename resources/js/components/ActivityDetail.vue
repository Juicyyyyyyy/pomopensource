<template>
    <div class="activity-detail bg-white/10 backdrop-blur-lg rounded-lg p-6 shadow-lg">
        <div v-if="projects.length">
            <div v-for="project in projects" :key="project.id" class="mb-6">
                <h3 class="text-xl font-semibold text-white mb-3">{{ project.name }}</h3>
                <div class="ml-4">
                    <div v-for="task in project.tasks" :key="task.id" class="text-white/70">
                        <i class="fas fa-tasks mr-2"></i>
                        {{ task.name }} - {{ task.time_focused }} mins
                    </div>
                </div>
            </div>
        </div>
        <div v-else class="text-white/70">
            No data available.
        </div>
    </div>
</template>

<script>
import { ref, onMounted } from 'vue';
import axios from 'axios';

export default {
    setup() {
        const projects = ref([]);

        const fetchProjectStats = async () => {
            try {
                const response = await axios.get('/projects-stats');
                projects.value = response.data.projects;
            } catch (error) {
                console.error('Error fetching project stats:', error);
            }
        };

        onMounted(fetchProjectStats);

        return {
            projects,
        };
    },
};
</script>

<style scoped>
.activity-detail {
    background-color: rgba(255, 255, 255, 0.1);
    padding: 1.5rem;
    border-radius: 0.75rem;
    backdrop-filter: blur(10px);
}

h3 {
    color: white;
}

.task {
    color: rgba(255, 255, 255, 0.7);
}
</style>
