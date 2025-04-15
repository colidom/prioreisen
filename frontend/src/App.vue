<script setup>
import AppHeader from './components/AppHeader.vue'
import MainComponent from './components/MainComponent.vue'
</script>

<template>
  <div class="min-h-screen flex flex-col">
    <AppHeader />

    <main class="flex-1 bg-gray-100 p-6">
      <MainComponent />
    </main>
  </div>
</template>
