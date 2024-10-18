<template>
    <Timer :projects="localProjects" :settings="settings"/>
    <div class="w-full max-w-3xl py-8 px-6 bg-white/10 rounded-lg shadow-lg">
        <h2 class="text-3xl font-bold mb-6 font-oswald text-white">Projects</h2>

        <!-- Projects Section -->
        <div class="mb-8">
            <form @submit.prevent="addProject" class="mb-6">
                <div class="mb-4">
                    <input v-model="newProjectName" type="text" placeholder="New project name" required
                           class="w-full py-3 px-4 bg-white/10 border border-white/30 rounded-lg text-center text-sm font-inter font-semibold text-white placeholder-white/70 focus:outline-none focus:border-white focus:ring-1 focus:ring-white transition duration-200">
                </div>
                <button type="submit"
                        class="w-full py-3 border-2 border-dashed border-white rounded-lg text-center text-sm font-inter font-semibold text-white hover:bg-white/20 hover:border-white transition duration-200">
                    + Add Project
                </button>
            </form>
            <ul class="space-y-4">
                <li v-for="project in localProjects" :key="project.id" class="bg-white/5 rounded-lg p-4">
                    <div class="flex items-center justify-between mb-2">
                        <input v-model="project.name" @blur="updateProject(project)"
                               class="bg-transparent border-b border-white/30 py-1 px-2 text-white font-inter font-semibold focus:outline-none focus:border-white">
                        <div class="flex space-x-2">
                            <button @click="toggleTasksVisibility(project)" class="text-white hover:text-gray-300 transition">
                                {{ project.showTasks ? 'Hide Subprojects' : 'Show Subprojects' }}
                            </button>
                            <button @click="deleteProject(project.id)" class="text-red-400 hover:text-red-600 transition">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                    </div>

                    <!-- Tasks Section -->
                    <div v-if="project.showTasks" class="mt-4">
                        <form @submit.prevent="addTask(project)" class="mb-4">
                            <div class="flex">
                                <input v-model="newTaskNames[project.id]" type="text" placeholder="New Subproject name" required
                                       class="flex-grow py-2 px-3 bg-white/10 border border-white/30 rounded-l-lg text-sm font-inter font-semibold text-white placeholder-white/70 focus:outline-none focus:border-white focus:ring-1 focus:ring-white transition duration-200">
                                <button type="submit"
                                        class="py-2 px-4 bg-white/20 border border-white/30 rounded-r-lg text-sm font-inter font-semibold text-white hover:bg-white/30 transition duration-200">
                                    Add Subproject
                                </button>
                            </div>
                        </form>
                        <ul class="space-y-2">
                            <li v-for="task in project.tasks" :key="task.id" class="flex items-center justify-between bg-white/5 rounded p-2">
                                <input v-model="task.name" @blur="updateTask(project, task)"
                                       class="bg-transparent text-white font-inter focus:outline-none">
                                <button @click="deleteTask(project, task.id)" class="text-red-400 hover:text-red-600 transition">
                                    <i class="fas fa-times"></i>
                                </button>
                            </li>
                        </ul>
                    </div>
                </li>
            </ul>
        </div>

    </div>

</template>


<script>
import { ref, reactive } from 'vue';
import axios from 'axios';
import Timer from "./Timer.vue";

export default {
  components: {
    Timer
  },
  props: {
    projects: {
      type: Array,
      required: true
    },
    settings: {
      type: Object,
    }
  },
  setup(props) {
    const localProjects = ref([...props.projects]); // Use a deep copy of projects
    const newProjectName = ref('');
    const newTaskNames = reactive({});

    const addProject = async () => {
      if (newProjectName.value.trim()) {
        try {
          const response = await axios.post('/projects', { name: newProjectName.value });
          localProjects.value.push(response.data); // Push the new project to localProjects
          newProjectName.value = '';
          window.location.reload();
        } catch (error) {
          console.error(error);
        }
      }
    };

    const updateProject = async (project) => {
      try {
        await axios.patch(`/projects/${project.id}`, { name: project.name });
      } catch (error) {
        console.error(error);
      }
    };

    const deleteProject = async (projectId) => {
      if (confirm('Are you sure you want to delete this project?')) {
        try {
          await axios.delete(`/projects/${projectId}`);
          localProjects.value = localProjects.value.filter(project => project.id !== projectId);
        } catch (error) {
          console.error(error);
        }
      }
    };

    const toggleTasksVisibility = (project) => {
      project.showTasks = !project.showTasks;
    };

    const addTask = async (project) => {
      const taskName = newTaskNames[project.id]?.trim();
      if (taskName) {
        try {
          const response = await axios.post(`/projects/${project.id}/tasks`, { name: taskName });
          project.tasks.push(response.data); // Push the new task to the project's tasks
          newTaskNames[project.id] = '';
        } catch (error) {
          console.error(error);
        }
      }
    };

    const updateTask = async (project, task) => {
      try {
        await axios.patch(`/tasks/${task.id}`, { name: task.name });
      } catch (error) {
        console.error(error);
      }
    };

    const deleteTask = async (project, taskId) => {
      if (confirm('Are you sure you want to delete this subproject?')) {
        try {
          await axios.delete(`/tasks/${taskId}`);
          project.tasks = project.tasks.filter(task => task.id !== taskId);
        } catch (error) {
          console.error(error);
        }
      }
    };

    return {
      localProjects,
      newProjectName,
      newTaskNames,
      addProject,
      updateProject,
      deleteProject,
      toggleTasksVisibility,
      addTask,
      updateTask,
      deleteTask
    };
  }
};
</script>

