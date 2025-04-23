<template>
  <div
    class="bg-white rounded-lg shadow p-4 mb-4 cursor-move"
    draggable="true"
    @dragstart="$emit('drag-task', task)"
  >
    <div class="flex justify-between items-start">
      <div class="flex-1">
        <div class="font-bold text-gray-800">{{ task.title }}</div>
        <div class="text-sm text-gray-600">{{ task.description }}</div>
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
const props = defineProps({
  task: Object,
  color: String
})
</script>
