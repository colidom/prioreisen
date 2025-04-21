<template>
    <div
      class="task-item text-white p-4 rounded-lg transition-colors duration-300 cursor-pointer"
      :class="hoverClass"
      @click="open"
      @dragstart="startDrag"
      draggable="true"
    >
      <div class="grid grid-cols-2 gap-4">
        <div class="flex justify-between items-center">
          <span>{{ task.title }}</span>
        </div>
      </div>
    </div>
  </template>
  
  <script>
  export default {
    name: 'TaskCard',
    props: {
      task: {
        type: Object,
        required: true,
      },
      color: {
        type: String,
        required: true, // ej: "red", "blue", "green", "yellow"
      },
    },
    emits: ['drag-task', 'open-task'],
    computed: {
      hoverClass() {
        return `hover:bg-${this.color}-600`
      },
    },
    methods: {
      startDrag() {
        this.$emit('drag-task', this.task)
      },
      open() {
        this.$emit('open-task', this.task)
      },
    },
  }
  </script>
  
  <style scoped>
  .task-item {
    transition: background-color 0.3s ease;
  }
  </style>
  