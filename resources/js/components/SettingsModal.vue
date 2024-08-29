<template>
    <div class="fixed inset-0 bg-black bg-opacity-70 flex items-center justify-center z-50">
        <div class="modal-content w-full max-w-2xl p-6 bg-white/10 backdrop-blur-lg rounded-lg shadow-xl transform transition-all duration-300 ease-in-out">
            <div class="flex justify-between items-center pb-4 border-b border-white/20">
                <h2 class="text-2xl font-bold font-oswald text-white">Settings</h2>
                <button @click="$emit('close')" class="text-white hover:text-gray-300 transition duration-150 ease-in-out">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <div class="flex mb-6 mt-6">
                <!-- Sidebar for category tabs -->
                <div class="w-1/4 pr-4 pt-6">
                    <div class="flex flex-col">
                        <button
                            v-for="category in settingsCategories"
                            :key="category.name"
                            @click="activeCategory = category.name"
                            :class="['mb-2 py-2 px-4 text-left rounded-lg transition ',
                                     activeCategory === category.name ? 'bg-white/20 text-white' : 'bg-white/10 text-white/70 hover:bg-white/20']"
                        >
                            <i :class="category.icon" class="mr-2"></i> {{ category.name }}
                        </button>
                    </div>
                </div>
                <!-- Settings content -->
                       <div class="modal-body w-3/4 pl-4">
                    <div v-if="activeCategory">
                        <div v-for="setting in getActiveCategorySettings()" :key="setting.id" class="mb-6">

                            <label v-if="setting.type !== 'checkbox'" :for="setting.key" class="block text-sm font-medium text-white/70">{{ setting.name }}</label>

                            <input v-if="setting.type === 'text'" type="text" v-model="setting.value" :id="setting.key"
                                   class="mt-1 block w-full bg-white/10 text-white border border-white/20 rounded-lg py-2 px-3 leading-5 focus:ring-white/40 focus:border-white/40 transition duration-150 ease-in-out">

                            <select v-else-if="setting.type === 'select'" v-model="setting.value" :id="setting.key"
                                    class="mt-1 block w-full bg-white/10 text-white border border-white/20 rounded-lg py-2 px-3 leading-5 focus:ring-white/40 focus:border-white/40 transition duration-150 ease-in-out">
                                <option v-for="option in setting.options" :key="option" :value="option" class="text-black">{{ option }}</option>
                            </select>

                            <div v-else-if="setting.type === 'checkbox'" class="mt-1 flex items-center">
                                <input type="checkbox" v-model="setting.value" :id="setting.key" class="toggle-input">
                                <label :for="setting.key" class="ml-2 text-white/70 cursor-pointer">{{ setting.name }}</label>
                            </div>

                            <div v-else-if="setting.type === 'number'" class="mt-1 flex flex-wrap gap-4 ">
                                <input type="number" v-model="setting.value" :id="setting.key"
                                       class="block w-1/4 bg-white/10 text-white border border-white/20 rounded-lg py-2 px-3 leading-5 focus:ring-white/40 focus:border-white/40 transition duration-150 ease-in-out">
                            </div>

                            <div v-else-if="setting.type === 'range'" class="mt-4">
                                <div class="flex items-center mt-2">
                                    <span class="text-white/70 text-xs">{{ setting.value }}</span>
                                    <input
                                        type="range"
                                        min="0"
                                        max="100"
                                        v-model="setting.value"
                                        :id="setting.key"
                                        class="ml-2 w-full h-3 bg-white/20 rounded-lg appearance-none cursor-pointer focus:outline-none focus:ring-2 focus:ring-blue-8      00/50 transition-all"
                                        :style="{ background: `linear-gradient(to right, rgba(37, 99, 235, 0.5) ${setting.value}%, rgba(255, 255, 255, 0.2) ${setting.value}%)` }"
                                    />
                                </div>
                                <div class="flex justify-between text-xs text-white/70 mt-1">
                                    <span></span>
                                    <span>100</span>
                                </div>
                            </div>


                        </div>
                    </div>
                </div>

            </div>
            <div class="modal-footer pt-4 border-t border-white/20 flex justify-end">
                <button @click="saveSettings"
                        class="px-4 py-2 bg-blue-800/50 text-white rounded-lg hover:bg-blue-900/50 transition">
                    Save
                </button>
                <button @click="$emit('close')" class="ml-2 px-4 py-2 bg-red-800/50 text-white rounded-lg hover:bg-red-900/50 transition">
                    Cancel
                </button>
            </div>
        </div>
    </div>
</template>

<script>
import { ref, onMounted } from 'vue';
import axios from 'axios';

export default {
    emits: ['close'],
    setup() {
        const settingsCategories = ref([]);
        const activeCategory = ref('');

        const fetchSettings = async () => {
            try {
                const response = await axios.get('/user-settings');
                settingsCategories.value = response.data;
                // Set the initial active category
                if (settingsCategories.value.length > 0) {
                    activeCategory.value = settingsCategories.value[0].name;
                }
            } catch (error) {
                console.error('Error fetching settings:', error);
            }
        };

        const getActiveCategorySettings = () => {
            const category = settingsCategories.value.find(cat => cat.name === activeCategory.value);
            return category ? category.settings : [];
        };

        const saveSettings = async () => {
            try {
                const payload = {
                    settings: settingsCategories.value.flatMap(category =>
                        category.settings.map(setting => ({
                            id: setting.id,
                            value: setting.value,
                        }))
                    ),
                };
                await axios.post('/user-settings', payload);
                alert('Settings updated successfully');
            } catch (error) {
                console.error('Error saving settings:', error);
            }
        };

        onMounted(fetchSettings);

        return {
            settingsCategories,
            activeCategory,
            getActiveCategorySettings,
            saveSettings,
        };
    },
};
</script>

<style scoped>
/* Toggle switch styles */
.toggle-input {
    appearance: none;
    width: 48px;
    height: 24px;
    background: rgba(255, 255, 255, 0.2);
    border-radius: 9999px;
    position: relative;
    outline: none;
    transition: background 0.3s, box-shadow 0.3s;
    cursor: pointer;
    box-shadow: inset 0 0 5px rgba(0, 0, 0, 0.2);
}

.toggle-input:checked {
    background: rgba(37, 99, 235, 0.5); /* Tailwind bg-blue-800/50 */
}

.toggle-input::after {
    content: "";
    position: absolute;
    top: 2px;
    left: 2px;
    width: 20px;
    height: 20px;
    background: white;
    border-radius: 50%;
    transition: transform 0.3s, box-shadow 0.3s;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.3);
}

.toggle-input:checked::after {
    transform: translateX(24px);
}

.toggle-input:hover::after {
    box-shadow: 0 1px 5px rgba(0, 0, 0, 0.5);
}

.toggle-input:focus {
    box-shadow: 0 0 0 2px rgba(37, 99, 235, 0.3);
}


</style>
