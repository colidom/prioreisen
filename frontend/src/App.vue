<script setup>
import AppHeader from './components/AppHeader.vue'
import MainComponent from './components/MainComponent.vue'
</script>

<template>
  <div class="min-h-screen flex flex-col bg-gradient-to-br from-gray-50 via-blue-50 to-indigo-100">
    <AppHeader />

    <main class="flex-1 p-0 pt-16 sm:pt-20">
      <MainComponent />
    </main>
  </div>
</template>

<style scoped>
/* Asegurar que el viewport se ajuste correctamente en móviles */
@media (max-width: 640px) {
  html {
    -webkit-text-size-adjust: 100%;
    -ms-text-size-adjust: 100%;
  }
}
</style>
