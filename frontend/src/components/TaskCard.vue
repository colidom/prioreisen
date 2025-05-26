<template>
  <div
    class="bg-white/[0.45] rounded-lg shadow p-4 mb-4 cursor-move"  
    draggable="true"
    @dragstart="$emit('drag-task', task)"
  >
    <div class="flex justify-between items-start">
      <div class="flex-1">
        <div class="font-bold text-gray-800">{{ task.title }}</div>
        <div 
          :class="{
            'line-clamp-2': !expanded,
            'whitespace-pre-wrap': true
          }"
          ref="descRef"
          class="text-sm text-gray-600"
        >
          {{ task.description }}
        </div>
        <div v-if="showToggle" class="text-blue-600 cursor-pointer text-sm mt-1" @click="toggleExpanded">
          {{ expanded ? 'Mostrar menos' : 'Mostrar más' }}
        </div>
      </div>
      <div class="flex items-center space-x-2 ml-2">
        <button @click="$emit('complete-task', task)" class="hover:scale-110 transition text-green-600">
          ✅
        </button>
        <button @click="$emit('edit-task', task)" class="hover:scale-110 transition text-blue-600">
          ✏️
        </button>
        <button @click="$emit('delete-task', task)" class="hover:scale-110 transition text-red-600">
          🗑️
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
  // Comprobamos si el scrollHeight es mayor que clientHeight => contenido recortado
  const el = descRef.value
  showToggle.value = el.scrollHeight > el.clientHeight + 5
}

onMounted(() => {
  nextTick(() => {
    checkDescriptionOverflow()
  })
})
</script>

<style>
.line-clamp-2 {
  display: -webkit-box;
  -webkit-line-clamp: 2;
  -webkit-box-orient: vertical;  
  line-clamp: 2;
  overflow: hidden;
  text-overflow: ellipsis;
}
</style>
