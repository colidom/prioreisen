<template>
  <div class="container mx-auto p-6">
    <h1 class="text-4xl text-gray-500 font-bold text-center mb-6">Eisenhower Matrix</h1>
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-2 gap-6">
      <!-- Quadrant 1: Important and Urgent -->
      <div class="bg-red-500 p-4 rounded-lg shadow-lg">
        <h2 class="text-2xl font-semibold text-white text-center">Quadrant 1: Important & Urgent</h2>
        <ul>
          <li v-for="task in quadrant1Tasks" :key="task.id" class="text-white mt-2">
            {{ task.title }} - {{ task.status }}
          </li>
        </ul>
      </div>

      <!-- Quadrant 2: Important but Not Urgent -->
      <div class="bg-blue-500 p-4 rounded-lg shadow-lg">
        <h2 class="text-2xl font-semibold text-white text-center">Quadrant 2: Important but Not Urgent</h2>
        <ul>
          <li v-for="task in quadrant2Tasks" :key="task.id" class="text-white mt-2">
            {{ task.title }} - {{ task.status }}
          </li>
        </ul>
      </div>

      <!-- Quadrant 3: Urgent but Not Important -->
      <div class="bg-yellow-500 p-4 rounded-lg shadow-lg">
        <h2 class="text-2xl font-semibold text-white text-center">Quadrant 3: Urgent but Not Important</h2>
        <ul>
          <li v-for="task in quadrant3Tasks" :key="task.id" class="text-white mt-2">
            {{ task.title }} - {{ task.status }}
          </li>
        </ul>
      </div>

      <!-- Quadrant 4: Neither Important nor Urgent -->
      <div class="bg-green-500 p-4 rounded-lg shadow-lg">
        <h2 class="text-2xl font-semibold text-white text-center">Quadrant 4: Neither Important nor Urgent</h2>
        <ul>
          <li v-for="task in quadrant4Tasks" :key="task.id" class="text-white mt-2">
            {{ task.title }} - {{ task.status }}
          </li>
        </ul>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  name: "MainComponent",
  data() {
    return {
      tasks: [] // Array to hold all tasks
    };
  },
  computed: {
    // Quadrant 1: Important & Urgent
    quadrant1Tasks() {
      return this.tasks.filter(task => task.quadrant === 1);
    },
    // Quadrant 2: Important but Not Urgent
    quadrant2Tasks() {
      return this.tasks.filter(task => task.quadrant === 2);
    },
    // Quadrant 3: Urgent but Not Important
    quadrant3Tasks() {
      return this.tasks.filter(task => task.quadrant === 3);
    },
    // Quadrant 4: Neither Important nor Urgent
    quadrant4Tasks() {
      return this.tasks.filter(task => task.quadrant === 4);
    }
  },
  methods: {
    // Fetch tasks from API
    async fetchTasks() {
      try {
        const response = await fetch("http://127.0.0.1:8000/api/tasks");
        const data = await response.json();
        this.tasks = data; // Set tasks array with the fetched data
        console.log("Tasks fetched:", data); // Verify the fetched data
      } catch (error) {
        console.error("Error fetching tasks:", error);
      }
    }
  },
  mounted() {
    this.fetchTasks(); // Fetch tasks when the component is mounted
  }
};
</script>

<style scoped>
/* Scoped styles can go here if needed */
</style>
