<template>
  <header 
    class="bg-white/80 backdrop-blur-lg text-gray-800 border-b border-gray-200 shadow-lg sticky top-0 z-50"
    v-motion
    :initial="{ opacity: 0, y: -50 }"
    :enter="{ opacity: 1, y: 0, transition: { duration: 800, ease: 'easeOut' } }"
  >
    <div class="max-w-7xl mx-auto px-4 sm:px-6 py-2 sm:py-4">
      <!-- Mobile Layout -->
      <div class="flex items-center justify-between sm:hidden">
        <div class="flex items-center gap-2">
          <img 
            alt="prioreisen logo" 
            class="w-16 h-16 object-contain drop-shadow-lg" 
            src="@/assets/logo.svg" 
          />
          <div class="flex flex-col">
            <span class="text-sm font-bold text-indigo-600">Prioreisen</span>
            <span class="text-xs text-gray-500">Matriz Eisenhower</span>
          </div>
        </div>
      </div>

      <!-- Desktop Layout -->
      <div class="hidden sm:flex sm:items-center sm:justify-between gap-4">
        <div class="flex items-center gap-3">
          <img 
            alt="prioreisen logo" 
            class="w-24 h-24 lg:w-32 lg:h-32 object-contain drop-shadow-lg hover:scale-105 transition-transform duration-300" 
            src="@/assets/logo.svg" 
          />
        </div>

        <div class="flex flex-col items-end gap-2">
          <div class="flex items-center gap-2 text-indigo-600 font-semibold text-lg">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" />
            </svg>
            <span>Matriz de Eisenhower</span>
          </div>
          <p class="text-xs sm:text-sm text-gray-600 italic text-right leading-tight max-w-md">
            "Lo urgente rara vez es importante, y lo importante nunca es urgente."
          </p>
        </div>
      </div>

      <!-- Mobile Quote (below logo) -->
      <p class="sm:hidden text-xs text-gray-500 italic text-center mt-2 px-2">
        "Lo urgente rara vez es importante"
      </p>
    </div>
  </header>
</template>

<script setup>
</script>

<style scoped>
header {
  background: linear-gradient(135deg, rgba(255, 255, 255, 0.9) 0%, rgba(249, 250, 251, 0.9) 100%);
}
</style>
