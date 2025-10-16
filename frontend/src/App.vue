<script setup>
import AppHeader from './components/AppHeader.vue'
import MainComponent from './components/MainComponent.vue'
</script>

<template>
  <div class="min-h-screen flex flex-col bg-gradient-to-br from-gray-50 via-blue-50 to-indigo-100">
    <AppHeader />

    <main class="flex-1 p-6">
      <MainComponent />
    </main>
  </div>
</template>
