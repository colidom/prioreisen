<template>
  <header
    class="bg-white/80 backdrop-blur-lg text-gray-800 border-b border-gray-200/50 shadow-sm fixed top-0 left-0 right-0 z-50 transition-all duration-300 ease-in-out"
    :style="{ transform: showHeader ? 'translateY(0)' : 'translateY(-100%)' }"
  >
    <div class="max-w-7xl mx-auto px-4 sm:px-6 py-2 sm:py-2.5">
      <!-- Mobile Layout -->
      <div class="flex items-center justify-between sm:hidden">
        <div class="flex items-center gap-2">
          <img
            alt="prioreisen logo"
            class="w-12 h-12 object-contain drop-shadow-md"
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
            class="w-16 h-16 lg:w-20 lg:h-20 object-contain drop-shadow-md hover:scale-105 transition-transform duration-300"
            src="@/assets/logo.svg"
          />
          <div class="flex flex-col">
            <span class="text-xl font-bold text-indigo-600 tracking-tight">Prioreisen</span>
            <span class="text-xs text-gray-500">Gestión de Prioridades</span>
          </div>
        </div>

        <div class="flex flex-col items-end gap-1.5">
          <div class="flex items-center gap-2 text-gray-700 font-medium text-base">
            <svg
              class="w-5 h-5 text-indigo-500"
              fill="none"
              stroke="currentColor"
              viewBox="0 0 24 24"
            >
              <path
                stroke-linecap="round"
                stroke-linejoin="round"
                stroke-width="2"
                d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"
              />
            </svg>
            <span>Matriz de Eisenhower</span>
          </div>
          <p class="text-xs text-gray-500 italic text-right leading-tight max-w-md">
            "Lo urgente rara vez es importante, y lo importante nunca es urgente."
          </p>
        </div>
      </div>

      <!-- Mobile Quote (below logo) -->
      <p class="sm:hidden text-xs text-gray-500 italic text-center mt-1.5 px-2">
        "Lo urgente rara vez es importante"
      </p>
    </div>
  </header>
</template>

<script setup>
import { ref, onMounted, onUnmounted } from 'vue'

const showHeader = ref(true)
let lastScrollY = 0
let ticking = false

const handleScroll = () => {
  if (!ticking) {
    window.requestAnimationFrame(() => {
      const currentScrollY = window.scrollY
      
      // Si estamos en la parte superior (primeros 50px), siempre mostrar el header
      if (currentScrollY < 50) {
        showHeader.value = true
      } else {
        // Detectar dirección del scroll
        if (currentScrollY > lastScrollY && currentScrollY > 100) {
          // Scrolling down y después de 100px - ocultar header
          showHeader.value = false
        } else if (currentScrollY < lastScrollY) {
          // Scrolling up - mostrar header
          showHeader.value = true
        }
      }
      
      lastScrollY = currentScrollY
      ticking = false
    })
    
    ticking = true
  }
}

onMounted(() => {
  lastScrollY = window.scrollY
  window.addEventListener('scroll', handleScroll, { passive: true })
})

onUnmounted(() => {
  window.removeEventListener('scroll', handleScroll)
})
</script>

<style scoped>
header {
  background: linear-gradient(135deg, rgba(255, 255, 255, 0.95) 0%, rgba(249, 250, 251, 0.95) 100%);
}
</style>
