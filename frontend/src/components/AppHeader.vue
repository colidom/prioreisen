<!-- src/components/AppHeader.vue -->
<template>
  <header class="bg-gray-800 text-white p-4 shadow-md">
    <div class="flex items-center justify-between max-w-7xl mx-auto">
      <img alt="Vue logo" class="logo w-16 h-16" src="@/assets/logo.svg" />
      <nav class="space-x-6">
        <RouterLink to="/" class="text-lg hover:text-blue-300">Home</RouterLink>
        <RouterLink to="/about" class="text-lg hover:text-blue-300">About</RouterLink>
      </nav>
    </div>
  </header>
</template>

<script setup>
import { RouterLink } from 'vue-router'
</script>

<style scoped>
.logo {
  width: 60px;
  height: 60px;
}

nav a {
  transition: color 0.3s ease;
}

nav a:hover {
  color: #4caf50;
}
</style>
