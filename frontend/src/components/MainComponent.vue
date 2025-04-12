<template>
  <div class="container mx-auto p-6">
    <h1 class="text-4xl font-bold text-center mb-6">Eisenhower Matrix</h1>
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-2 gap-6">
      <!-- Quadrant 1: Important and Urgent -->
      <div class="bg-red-500 p-4 rounded-lg shadow-lg">
        <h2 class="text-2xl font-semibold text-white">Quadrant 1: Important & Urgent</h2>
        <ul>
          <li v-for="task in quadrantTasks(1)" :key="task.id" class="text-white mt-2">
          {{ task.title }} - {{ task.status }}
        </li>
      </ul>
    </div>

    <!-- Quadrant 2: Important but Not Urgent -->
    <div class="bg-blue-500 p-4 rounded-lg shadow-lg">
      <h2 class="text-2xl font-semibold text-white">Quadrant 2: Important but Not Urgent</h2>
      <ul>
        <li v-for="task in quadrantTasks(2)" :key="task.id" class="text-white mt-2">
        {{ task.title }} - {{ task.status }}
      </li>
    </ul>
  </div>

  <!-- Quadrant 3: Urgent but Not Important -->
  <div class="bg-yellow-500 p-4 rounded-lg shadow-lg">
    <h2 class="text-2xl font-semibold text-white">Quadrant 3: Urgent but Not Important</h2>
    <ul>
      <li v-for="task in quadrantTasks(3)" :key="task.id" class="text-white mt-2">
      {{ task.title }} - {{ task.status }}
    </li>
  </ul>
</div>

<!-- Quadrant 4: Neither Important nor Urgent -->
<div class="bg-green-500 p-4 rounded-lg shadow-lg">
  <h2 class="text-2xl font-semibold text-white">Quadrant 4: Neither Important nor Urgent</h2>
  <ul>
    <li v-for="task in quadrantTasks(4)" :key="task.id" class="text-white mt-2">
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
  tasks: []
};
},
  methods: {
  // Fetch tasks from API (make sure to replace with actual API URL)
  async fetchTasks() {
  try {
  const response = await fetch("http://localhost/api/tasks");
  const data = await response.json();
  this.tasks = data;
} catch (error) {
  console.error("Error fetching tasks:", error);
}
},
  // Get tasks by quadrant number
  quadrantTasks(quadrant) {
  return this.tasks.filter(task => task.quadrant === quadrant);
}
},
  mounted() {
  this.fetchTasks();
}
};
</script>

<style scoped>
  /* Scoped styles can go here if needed */
</style>
