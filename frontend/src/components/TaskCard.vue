<template>
  <div
    class="bg-white/90 backdrop-blur-sm rounded-xl shadow-lg hover:shadow-2xl p-5 mb-4 cursor-move border border-gray-200/50 transition-all duration-300 hover:-translate-y-1 group"  
    draggable="true"
    @dragstart="$emit('drag-task', task)"
    v-motion
    :initial="{ opacity: 0, scale: 0.8 }"
    :enter="{ opacity: 1, scale: 1, transition: { duration: 400, ease: 'easeOut' } }"
    :leave="{ opacity: 0, scale: 0.8, transition: { duration: 300 } }"
  >
    <div class="flex justify-between items-start gap-3">
      <div class="flex-1 min-w-0">
        <div class="font-bold text-gray-800 text-lg mb-2 flex items-center gap-2">
          <span class="w-2 h-2 rounded-full bg-gradient-to-r from-indigo-500 to-purple-500"></span>
          <span class="truncate">{{ task.title }}</span>
        </div>
        <div 
          :class="{
            'line-clamp-2': !expanded,
            'whitespace-pre-wrap': true
          }"
          ref="descRef"
          class="text-sm text-gray-600 leading-relaxed"
        >
          {{ task.description }}
        </div>
        <div 
          v-if="showToggle" 
          class="text-indigo-600 hover:text-indigo-700 cursor-pointer text-sm mt-2 font-medium inline-flex items-center gap-1 transition-colors" 
          @click="toggleExpanded"
        >
          <svg 
            class="w-4 h-4 transition-transform duration-300" 
            :class="{ 'rotate-180': expanded }"
            fill="none" 
            stroke="currentColor" 
            viewBox="0 0 24 24"
          >
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
          </svg>
          {{ expanded ? 'Mostrar menos' : 'Mostrar más' }}
        </div>
      </div>
      <div class="flex items-center gap-2">
        <button 
          @click="$emit('complete-task', task)" 
          class="p-2 hover:bg-green-100 rounded-lg transition-all duration-200 hover:scale-110 text-green-600 opacity-0 group-hover:opacity-100"
          title="Completar"
        >
          <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
          </svg>
        </button>
        <button 
          @click="$emit('edit-task', task)" 
          class="p-2 hover:bg-blue-100 rounded-lg transition-all duration-200 hover:scale-110 text-blue-600 opacity-0 group-hover:opacity-100"
          title="Editar"
        >
          <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
          </svg>
        </button>
        <button 
          @click="$emit('delete-task', task)" 
          class="p-2 hover:bg-red-100 rounded-lg transition-all duration-200 hover:scale-110 text-red-600 opacity-0 group-hover:opacity-100"
          title="Eliminar"
        >
          <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
          </svg>
        </button>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, nextTick } from 'vue'

const props = defineProps({
  task: Object,
  color: String
})

const expanded = ref(false)
const showToggle = ref(false)
const descRef = ref(null)

const toggleExpanded = () => {
  expanded.value = !expanded.value
}

const checkDescriptionOverflow = () => {
  if (!descRef.value) return
  const el = descRef.value
  showToggle.value = el.scrollHeight > el.clientHeight + 5
}

onMounted(() => {
  nextTick(() => {
    checkDescriptionOverflow()
  })
})
</script>

<style scoped>
.line-clamp-2 {
  display: -webkit-box;
  -webkit-line-clamp: 2;
  -webkit-box-orient: vertical;  
  line-clamp: 2;
  overflow: hidden;
  text-overflow: ellipsis;
}
</style>
