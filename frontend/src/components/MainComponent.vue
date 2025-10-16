<template>
  <div class="min-h-screen flex flex-col">
    <main class="flex-1 p-3 sm:p-4 md:p-6">
      <div class="container mx-auto max-w-7xl">
        <!-- Botón crear tarea con animación -->
        <div 
          class="flex justify-center my-4 sm:my-6"
          v-motion
          :initial="{ opacity: 0, y: 20 }"
          :enter="{ opacity: 1, y: 0, transition: { duration: 600, delay: 200 } }"
        >
          <button
            @click="openNewTaskModal"
            class="group relative bg-gradient-to-r from-indigo-600 to-purple-600 hover:from-indigo-700 hover:to-purple-700 text-white font-bold py-3 px-6 sm:py-3 sm:px-8 rounded-2xl shadow-xl hover:shadow-2xl transition-all duration-300 transform hover:scale-105 w-full sm:w-auto"
          >
            <span class="flex items-center justify-center gap-2">
              <svg class="w-5 h-5 sm:w-6 sm:h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
              </svg>
              <span class="text-sm sm:text-base">Crear Nueva Tarea</span>
            </span>
            <div class="absolute inset-0 rounded-2xl bg-white opacity-0 group-hover:opacity-20 transition-opacity duration-300"></div>
          </button>
        </div>

        <!-- Matriz Eisenhower - Layout Móvil (Vertical Stack) -->
        <div 
          class="mt-6 sm:mt-10 lg:hidden"
          v-motion
          :initial="{ opacity: 0 }"
          :enter="{ opacity: 1, transition: { duration: 800, delay: 400 } }"
        >
          <div class="space-y-4">
            <!-- Cuadrante 1: Hacer Ya -->
            <div
              class="relative bg-gradient-to-br from-blue-500 to-blue-600 p-4 rounded-2xl shadow-2xl min-h-[200px] transition-all duration-300"
              :class="{ 'ring-4 ring-white ring-opacity-50 scale-105': dragOverQuadrant === 1 }"
              @drop="handleDrop(1, $event)"
              @dragover.prevent
              @dragenter="onDragEnter($event, 1)"
              @dragleave="onDragLeave($event, 1)"
            >
              <div class="absolute inset-0 bg-white/10 rounded-2xl backdrop-blur-sm"></div>
              <div class="relative">
                <h2 class="text-xl font-bold text-white text-center mb-3 flex items-center justify-center gap-2">
                  <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                  </svg>
                  HACER YA
                </h2>
                <p class="text-white/80 text-xs text-center mb-3">Urgente e Importante</p>
                <div class="space-y-2">
                  <template v-for="(task, index) in quadrant1Tasks" :key="task.id">
                    <!-- Placeholder cuando se arrastra sobre esta posición -->
                    <div
                      v-if="dragOverQuadrant === 1 && draggingTask && dropIndex === index"
                      class="bg-white/30 backdrop-blur-sm rounded-xl p-4 mb-2 border-2 border-dashed border-white/50 animate-pulse"
                    >
                      <div class="font-bold text-white text-base mb-2">{{ draggingTask.title }}</div>
                      <div class="text-white/80 text-sm line-clamp-2">{{ draggingTask.description }}</div>
                    </div>
                    
                    <div
                      @dragover.prevent="onDragOverTask($event, 1, index)"
                      @drop.stop="handleDrop(1, $event)"
                    >
                      <TaskCard
                        :task="task"
                        color="blue"
                        :is-dragging="draggingTask?.id === task.id"
                        @drag-task="dragTask"
                        @drag-end="onDragEnd"
                        @edit-task="openTaskModal"
                        @complete-task="markAsCompleted"
                        @delete-task="deleteTask"
                      />
                    </div>
                  </template>
                  
                  <!-- Placeholder al final si se arrastra al final de la lista -->
                  <div
                    v-if="dragOverQuadrant === 1 && draggingTask && dropIndex === quadrant1Tasks.length"
                    class="bg-white/30 backdrop-blur-sm rounded-xl p-4 mb-2 border-2 border-dashed border-white/50 animate-pulse"
                  >
                    <div class="font-bold text-white text-base mb-2">{{ draggingTask.title }}</div>
                    <div class="text-white/80 text-sm line-clamp-2">{{ draggingTask.description }}</div>
                  </div>
                  
                  <p v-if="quadrant1Tasks.length === 0" class="text-center text-white/70 italic py-6 text-sm">
                    No hay tareas urgentes e importantes
                  </p>
                </div>
              </div>
            </div>

            <!-- Cuadrante 2: Planificar -->
            <div
              class="relative bg-gradient-to-br from-green-500 to-green-600 p-4 rounded-2xl shadow-2xl min-h-[200px] transition-all duration-300"
              :class="{ 'ring-4 ring-white ring-opacity-50 scale-105': dragOverQuadrant === 2 }"
              @drop="handleDrop(2, $event)"
              @dragover.prevent
              @dragenter="onDragEnter($event, 2)"
              @dragleave="onDragLeave($event, 2)"
            >
              <div class="absolute inset-0 bg-white/10 rounded-2xl backdrop-blur-sm"></div>
              <div class="relative">
                <h2 class="text-xl font-bold text-white text-center mb-3 flex items-center justify-center gap-2">
                  <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                  </svg>
                  PLANIFICAR
                </h2>
                <p class="text-white/80 text-xs text-center mb-3">Importante, No urgente</p>
                <div class="space-y-2">
                  <template v-for="(task, index) in quadrant2Tasks" :key="task.id">
                    <div
                      v-if="dragOverQuadrant === 2 && draggingTask && dropIndex === index"
                      class="bg-white/30 backdrop-blur-sm rounded-xl p-4 mb-2 border-2 border-dashed border-white/50 animate-pulse"
                    >
                      <div class="font-bold text-white text-base mb-2">{{ draggingTask.title }}</div>
                      <div class="text-white/80 text-sm line-clamp-2">{{ draggingTask.description }}</div>
                    </div>
                    
                    <div
                      @dragover.prevent="onDragOverTask($event, 2, index)"
                      @drop.stop="handleDrop(2, $event)"
                    >
                      <TaskCard
                        :task="task"
                        color="green"
                        :is-dragging="draggingTask?.id === task.id"
                        @drag-task="dragTask"
                        @drag-end="onDragEnd"
                        @edit-task="openTaskModal"
                        @complete-task="markAsCompleted"
                        @delete-task="deleteTask"
                      />
                    </div>
                  </template>
                  
                  <div
                    v-if="dragOverQuadrant === 2 && draggingTask && dropIndex === quadrant2Tasks.length"
                    class="bg-white/30 backdrop-blur-sm rounded-xl p-4 mb-2 border-2 border-dashed border-white/50 animate-pulse"
                  >
                    <div class="font-bold text-white text-base mb-2">{{ draggingTask.title }}</div>
                    <div class="text-white/80 text-sm line-clamp-2">{{ draggingTask.description }}</div>
                  </div>
                  
                  <p v-if="quadrant2Tasks.length === 0" class="text-center text-white/70 italic py-6 text-sm">
                    No hay tareas importantes para planificar
                  </p>
                </div>
              </div>
            </div>

            <!-- Cuadrante 3: Delegar -->
            <div
              class="relative bg-gradient-to-br from-yellow-500 to-yellow-600 p-4 rounded-2xl shadow-2xl min-h-[200px] transition-all duration-300"
              :class="{ 'ring-4 ring-white ring-opacity-50 scale-105': dragOverQuadrant === 3 }"
              @drop="handleDrop(3, $event)"
              @dragover.prevent
              @dragenter="onDragEnter($event, 3)"
              @dragleave="onDragLeave($event, 3)"
            >
              <div class="absolute inset-0 bg-white/10 rounded-2xl backdrop-blur-sm"></div>
              <div class="relative">
                <h2 class="text-xl font-bold text-white text-center mb-3 flex items-center justify-center gap-2">
                  <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                  </svg>
                  DELEGAR
                </h2>
                <p class="text-white/80 text-xs text-center mb-3">Urgente, No importante</p>
                <div class="space-y-2">
                  <template v-for="(task, index) in quadrant3Tasks" :key="task.id">
                    <div
                      v-if="dragOverQuadrant === 3 && draggingTask && dropIndex === index"
                      class="bg-white/30 backdrop-blur-sm rounded-xl p-4 mb-2 border-2 border-dashed border-white/50 animate-pulse"
                    >
                      <div class="font-bold text-white text-base mb-2">{{ draggingTask.title }}</div>
                      <div class="text-white/80 text-sm line-clamp-2">{{ draggingTask.description }}</div>
                    </div>
                    
                    <div
                      @dragover.prevent="onDragOverTask($event, 3, index)"
                      @drop.stop="handleDrop(3, $event)"
                    >
                      <TaskCard
                        :task="task"
                        color="yellow"
                        :is-dragging="draggingTask?.id === task.id"
                        @drag-task="dragTask"
                        @drag-end="onDragEnd"
                        @edit-task="openTaskModal"
                        @complete-task="markAsCompleted"
                        @delete-task="deleteTask"
                      />
                    </div>
                  </template>
                  
                  <div
                    v-if="dragOverQuadrant === 3 && draggingTask && dropIndex === quadrant3Tasks.length"
                    class="bg-white/30 backdrop-blur-sm rounded-xl p-4 mb-2 border-2 border-dashed border-white/50 animate-pulse"
                  >
                    <div class="font-bold text-white text-base mb-2">{{ draggingTask.title }}</div>
                    <div class="text-white/80 text-sm line-clamp-2">{{ draggingTask.description }}</div>
                  </div>
                  
                  <p v-if="quadrant3Tasks.length === 0" class="text-center text-white/70 italic py-6 text-sm">
                    No hay tareas para delegar
                  </p>
                </div>
              </div>
            </div>

            <!-- Cuadrante 4: Eliminar -->
            <div
              class="relative bg-gradient-to-br from-gray-500 to-gray-600 p-4 rounded-2xl shadow-2xl min-h-[200px] transition-all duration-300"
              :class="{ 'ring-4 ring-white ring-opacity-50 scale-105': dragOverQuadrant === 4 }"
              @drop="handleDrop(4, $event)"
              @dragover.prevent
              @dragenter="onDragEnter($event, 4)"
              @dragleave="onDragLeave($event, 4)"
            >
              <div class="absolute inset-0 bg-white/10 rounded-2xl backdrop-blur-sm"></div>
              <div class="relative">
                <h2 class="text-xl font-bold text-white text-center mb-3 flex items-center justify-center gap-2">
                  <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                  </svg>
                  ELIMINAR
                </h2>
                <p class="text-white/80 text-xs text-center mb-3">No urgente, No importante</p>
                <div class="space-y-2">
                  <template v-for="(task, index) in quadrant4Tasks" :key="task.id">
                    <div
                      v-if="dragOverQuadrant === 4 && draggingTask && dropIndex === index"
                      class="bg-white/30 backdrop-blur-sm rounded-xl p-4 mb-2 border-2 border-dashed border-white/50 animate-pulse"
                    >
                      <div class="font-bold text-white text-base mb-2">{{ draggingTask.title }}</div>
                      <div class="text-white/80 text-sm line-clamp-2">{{ draggingTask.description }}</div>
                    </div>
                    
                    <div
                      @dragover.prevent="onDragOverTask($event, 4, index)"
                      @drop.stop="handleDrop(4, $event)"
                    >
                      <TaskCard
                        :task="task"
                        color="gray"
                        :is-dragging="draggingTask?.id === task.id"
                        @drag-task="dragTask"
                        @drag-end="onDragEnd"
                        @edit-task="openTaskModal"
                        @complete-task="markAsCompleted"
                        @delete-task="deleteTask"
                      />
                    </div>
                  </template>
                  
                  <div
                    v-if="dragOverQuadrant === 4 && draggingTask && dropIndex === quadrant4Tasks.length"
                    class="bg-white/30 backdrop-blur-sm rounded-xl p-4 mb-2 border-2 border-dashed border-white/50 animate-pulse"
                  >
                    <div class="font-bold text-white text-base mb-2">{{ draggingTask.title }}</div>
                    <div class="text-white/80 text-sm line-clamp-2">{{ draggingTask.description }}</div>
                  </div>
                  
                  <p v-if="quadrant4Tasks.length === 0" class="text-center text-white/70 italic py-6 text-sm">
                    No hay tareas para eliminar
                  </p>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Matriz Eisenhower - Layout Desktop (Grid Original) -->
        <div 
          class="mt-10 hidden lg:block"
          v-motion
          :initial="{ opacity: 0 }"
          :enter="{ opacity: 1, transition: { duration: 800, delay: 400 } }"
        >
          <div class="grid grid-cols-[auto,1fr,1fr] gap-6">
            <div></div>
            <div class="text-center">
              <div class="inline-flex items-center gap-2 bg-white/80 backdrop-blur-sm px-6 py-3 rounded-xl shadow-md">
                <svg class="w-5 h-5 text-red-500" fill="currentColor" viewBox="0 0 20 20">
                  <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd" />
                </svg>
                <span class="text-lg font-bold text-gray-800">Urgente</span>
              </div>
            </div>
            <div class="text-center">
              <div class="inline-flex items-center gap-2 bg-white/80 backdrop-blur-sm px-6 py-3 rounded-xl shadow-md">
                <svg class="w-5 h-5 text-blue-500" fill="currentColor" viewBox="0 0 20 20">
                  <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd" />
                </svg>
                <span class="text-lg font-bold text-gray-800">No Urgente</span>
              </div>
            </div>

            <div
              class="flex items-center justify-center mb-2"
              style="writing-mode: vertical-lr; transform: rotate(180deg)"
            >
              <div class="inline-flex items-center gap-2 bg-white/80 backdrop-blur-sm px-6 py-3 rounded-xl shadow-md">
                <svg class="w-5 h-5 text-amber-500" fill="currentColor" viewBox="0 0 20 20">
                  <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                </svg>
                <span class="text-lg font-bold text-gray-800">Importante</span>
              </div>
            </div>

            <!-- Cuadrante 1 Desktop -->
            <div
              class="relative bg-gradient-to-br from-blue-500 to-blue-600 p-6 rounded-2xl shadow-2xl min-h-[300px] transition-all duration-300 hover:shadow-blue-500/50"
              :class="{ 'ring-4 ring-white ring-opacity-50 scale-105': dragOverQuadrant === 1 }"
              @drop="handleDrop(1, $event)"
              @dragover.prevent
              @dragenter="onDragEnter($event, 1)"
              @dragleave="onDragLeave($event, 1)"
            >
              <div class="absolute inset-0 bg-white/10 rounded-2xl backdrop-blur-sm"></div>
              <div class="relative">
                <h2 class="text-2xl font-bold text-white text-center mb-4 flex items-center justify-center gap-2">
                  <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                  </svg>
                  HACER YA
                </h2>
                <div class="space-y-3">
                  <template v-for="(task, index) in quadrant1Tasks" :key="task.id">
                    <div
                      v-if="dragOverQuadrant === 1 && draggingTask && dropIndex === index"
                      class="bg-white/30 backdrop-blur-sm rounded-xl p-5 mb-3 border-2 border-dashed border-white/50 animate-pulse"
                    >
                      <div class="font-bold text-white text-lg mb-2">{{ draggingTask.title }}</div>
                      <div class="text-white/80 text-sm line-clamp-2">{{ draggingTask.description }}</div>
                    </div>
                    
                    <div
                      @dragover.prevent="onDragOverTask($event, 1, index)"
                      @drop.stop="handleDrop(1, $event)"
                    >
                      <TaskCard
                        :task="task"
                        color="blue"
                        :is-dragging="draggingTask?.id === task.id"
                        @drag-task="dragTask"
                        @drag-end="onDragEnd"
                        @edit-task="openTaskModal"
                        @complete-task="markAsCompleted"
                        @delete-task="deleteTask"
                      />
                    </div>
                  </template>
                  
                  <div
                    v-if="dragOverQuadrant === 1 && draggingTask && dropIndex === quadrant1Tasks.length"
                    class="bg-white/30 backdrop-blur-sm rounded-xl p-5 mb-3 border-2 border-dashed border-white/50 animate-pulse"
                  >
                    <div class="font-bold text-white text-lg mb-2">{{ draggingTask.title }}</div>
                    <div class="text-white/80 text-sm line-clamp-2">{{ draggingTask.description }}</div>
                  </div>
                  
                  <p v-if="quadrant1Tasks.length === 0" class="text-center text-white/70 italic py-8">
                    No hay tareas urgentes e importantes
                  </p>
                </div>
              </div>
            </div>

            <!-- Cuadrante 2 Desktop -->
            <div
              class="relative bg-gradient-to-br from-green-500 to-green-600 p-6 rounded-2xl shadow-2xl min-h-[300px] transition-all duration-300 hover:shadow-green-500/50"
              :class="{ 'ring-4 ring-white ring-opacity-50 scale-105': dragOverQuadrant === 2 }"
              @drop="handleDrop(2, $event)"
              @dragover.prevent
              @dragenter="onDragEnter($event, 2)"
              @dragleave="onDragLeave($event, 2)"
            >
              <div class="absolute inset-0 bg-white/10 rounded-2xl backdrop-blur-sm"></div>
              <div class="relative">
                <h2 class="text-2xl font-bold text-white text-center mb-4 flex items-center justify-center gap-2">
                  <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                  </svg>
                  PLANIFICAR
                </h2>
                <div class="space-y-3">
                  <template v-for="(task, index) in quadrant2Tasks" :key="task.id">
                    <div
                      v-if="dragOverQuadrant === 2 && draggingTask && dropIndex === index"
                      class="bg-white/30 backdrop-blur-sm rounded-xl p-5 mb-3 border-2 border-dashed border-white/50 animate-pulse"
                    >
                      <div class="font-bold text-white text-lg mb-2">{{ draggingTask.title }}</div>
                      <div class="text-white/80 text-sm line-clamp-2">{{ draggingTask.description }}</div>
                    </div>
                    
                    <div
                      @dragover.prevent="onDragOverTask($event, 2, index)"
                      @drop.stop="handleDrop(2, $event)"
                    >
                      <TaskCard
                        :task="task"
                        color="green"
                        :is-dragging="draggingTask?.id === task.id"
                        @drag-task="dragTask"
                        @drag-end="onDragEnd"
                        @edit-task="openTaskModal"
                        @complete-task="markAsCompleted"
                        @delete-task="deleteTask"
                      />
                    </div>
                  </template>
                  
                  <div
                    v-if="dragOverQuadrant === 2 && draggingTask && dropIndex === quadrant2Tasks.length"
                    class="bg-white/30 backdrop-blur-sm rounded-xl p-5 mb-3 border-2 border-dashed border-white/50 animate-pulse"
                  >
                    <div class="font-bold text-white text-lg mb-2">{{ draggingTask.title }}</div>
                    <div class="text-white/80 text-sm line-clamp-2">{{ draggingTask.description }}</div>
                  </div>
                  
                  <p v-if="quadrant2Tasks.length === 0" class="text-center text-white/70 italic py-8">
                    No hay tareas importantes para planificar
                  </p>
                </div>
              </div>
            </div>

            <div
              class="flex items-center justify-center mb-2"
              style="writing-mode: vertical-lr; transform: rotate(180deg)"
            >
              <div class="inline-flex items-center gap-2 bg-white/80 backdrop-blur-sm px-6 py-3 rounded-xl shadow-md">
                <svg class="w-5 h-5 text-gray-500" fill="currentColor" viewBox="0 0 20 20">
                  <path fill-rule="evenodd" d="M3 3a1 1 0 000 2v8a2 2 0 002 2h2.586l-1.293 1.293a1 1 0 101.414 1.414L10 15.414l2.293 2.293a1 1 0 001.414-1.414L12.414 15H15a2 2 0 002-2V5a1 1 0 100-2H3zm11.707 4.707a1 1 0 00-1.414-1.414L10 9.586 8.707 8.293a1 1 0 00-1.414 0l-2 2a1 1 0 101.414 1.414L8 10.414l1.293 1.293a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                </svg>
                <span class="text-lg font-bold text-gray-800">No Importante</span>
              </div>
            </div>

            <!-- Cuadrante 3 Desktop -->
            <div
              class="relative bg-gradient-to-br from-yellow-500 to-yellow-600 p-6 rounded-2xl shadow-2xl min-h-[300px] transition-all duration-300 hover:shadow-yellow-500/50"
              :class="{ 'ring-4 ring-white ring-opacity-50 scale-105': dragOverQuadrant === 3 }"
              @drop="handleDrop(3, $event)"
              @dragover.prevent
              @dragenter="onDragEnter($event, 3)"
              @dragleave="onDragLeave($event, 3)"
            >
              <div class="absolute inset-0 bg-white/10 rounded-2xl backdrop-blur-sm"></div>
              <div class="relative">
                <h2 class="text-2xl font-bold text-white text-center mb-4 flex items-center justify-center gap-2">
                  <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                  </svg>
                  DELEGAR
                </h2>
                <div class="space-y-3">
                  <template v-for="(task, index) in quadrant3Tasks" :key="task.id">
                    <div
                      v-if="dragOverQuadrant === 3 && draggingTask && dropIndex === index"
                      class="bg-white/30 backdrop-blur-sm rounded-xl p-5 mb-3 border-2 border-dashed border-white/50 animate-pulse"
                    >
                      <div class="font-bold text-white text-lg mb-2">{{ draggingTask.title }}</div>
                      <div class="text-white/80 text-sm line-clamp-2">{{ draggingTask.description }}</div>
                    </div>
                    
                    <div
                      @dragover.prevent="onDragOverTask($event, 3, index)"
                      @drop.stop="handleDrop(3, $event)"
                    >
                      <TaskCard
                        :task="task"
                        color="yellow"
                        :is-dragging="draggingTask?.id === task.id"
                        @drag-task="dragTask"
                        @drag-end="onDragEnd"
                        @edit-task="openTaskModal"
                        @complete-task="markAsCompleted"
                        @delete-task="deleteTask"
                      />
                    </div>
                  </template>
                  
                  <div
                    v-if="dragOverQuadrant === 3 && draggingTask && dropIndex === quadrant3Tasks.length"
                    class="bg-white/30 backdrop-blur-sm rounded-xl p-5 mb-3 border-2 border-dashed border-white/50 animate-pulse"
                  >
                    <div class="font-bold text-white text-lg mb-2">{{ draggingTask.title }}</div>
                    <div class="text-white/80 text-sm line-clamp-2">{{ draggingTask.description }}</div>
                  </div>
                  
                  <p v-if="quadrant3Tasks.length === 0" class="text-center text-white/70 italic py-8">
                    No hay tareas para delegar
                  </p>
                </div>
              </div>
            </div>

            <!-- Cuadrante 4 Desktop -->
            <div
              class="relative bg-gradient-to-br from-gray-500 to-gray-600 p-6 rounded-2xl shadow-2xl min-h-[300px] transition-all duration-300 hover:shadow-gray-500/50"
              :class="{ 'ring-4 ring-white ring-opacity-50 scale-105': dragOverQuadrant === 4 }"
              @drop="handleDrop(4, $event)"
              @dragover.prevent
              @dragenter="onDragEnter($event, 4)"
              @dragleave="onDragLeave($event, 4)"
            >
              <div class="absolute inset-0 bg-white/10 rounded-2xl backdrop-blur-sm"></div>
              <div class="relative">
                <h2 class="text-2xl font-bold text-white text-center mb-4 flex items-center justify-center gap-2">
                  <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                  </svg>
                  ELIMINAR
                </h2>
                <div class="space-y-3">
                  <template v-for="(task, index) in quadrant4Tasks" :key="task.id">
                    <div
                      v-if="dragOverQuadrant === 4 && draggingTask && dropIndex === index"
                      class="bg-white/30 backdrop-blur-sm rounded-xl p-5 mb-3 border-2 border-dashed border-white/50 animate-pulse"
                    >
                      <div class="font-bold text-white text-lg mb-2">{{ draggingTask.title }}</div>
                      <div class="text-white/80 text-sm line-clamp-2">{{ draggingTask.description }}</div>
                    </div>
                    
                    <div
                      @dragover.prevent="onDragOverTask($event, 4, index)"
                      @drop.stop="handleDrop(4, $event)"
                    >
                      <TaskCard
                        :task="task"
                        color="gray"
                        :is-dragging="draggingTask?.id === task.id"
                        @drag-task="dragTask"
                        @drag-end="onDragEnd"
                        @edit-task="openTaskModal"
                        @complete-task="markAsCompleted"
                        @delete-task="deleteTask"
                      />
                    </div>
                  </template>
                  
                  <div
                    v-if="dragOverQuadrant === 4 && draggingTask && dropIndex === quadrant4Tasks.length"
                    class="bg-white/30 backdrop-blur-sm rounded-xl p-5 mb-3 border-2 border-dashed border-white/50 animate-pulse"
                  >
                    <div class="font-bold text-white text-lg mb-2">{{ draggingTask.title }}</div>
                    <div class="text-white/80 text-sm line-clamp-2">{{ draggingTask.description }}</div>
                  </div>
                  
                  <p v-if="quadrant4Tasks.length === 0" class="text-center text-white/70 italic py-8">
                    No hay tareas para eliminar
                  </p>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Lista de tareas finalizadas -->
        <div 
          class="mt-12 sm:mt-16"
          v-motion
          :initial="{ opacity: 0, y: 30 }"
          :enter="{ opacity: 1, y: 0, transition: { duration: 600, delay: 600 } }"
        >
          <div class="relative py-6 sm:py-8">
            <div class="absolute inset-0 flex items-center">
              <div class="w-full border-t-2 border-gray-300"></div>
            </div>
            <div class="relative flex justify-center">
              <span class="bg-gradient-to-br from-gray-50 via-blue-50 to-indigo-100 px-4 sm:px-6 py-2 sm:py-3 rounded-2xl shadow-lg">
                <h2 class="text-lg sm:text-2xl font-bold text-gray-800 flex items-center gap-2 sm:gap-3">
                  <svg class="w-5 h-5 sm:w-7 sm:h-7 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                  </svg>
                  <span class="text-sm sm:text-2xl">TERMINADAS</span>
                </h2>
              </span>
            </div>
          </div>
          
          <div v-if="loadingCompleted" class="text-center text-gray-600 py-8 sm:py-12">
            <svg class="animate-spin h-8 w-8 sm:h-10 sm:w-10 mx-auto text-indigo-600" fill="none" viewBox="0 0 24 24">
              <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
              <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
            <p class="mt-3 sm:mt-4 text-sm sm:text-lg">Cargando tareas terminadas...</p>
          </div>
          <div v-else class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 sm:gap-6">
            <div
              v-for="task in completedTasks"
              :key="task.id"
              class="group relative bg-white/70 backdrop-blur-sm p-4 sm:p-6 rounded-2xl shadow-lg border-2 border-gray-200 hover:border-indigo-400 text-gray-500 hover:bg-white cursor-pointer transition-all duration-300 hover:-translate-y-1 hover:shadow-xl"
              @click="recoverCompletedTask(task)"
              v-motion
              :initial="{ opacity: 0, scale: 0.9 }"
              :enter="{ opacity: 1, scale: 1, transition: { duration: 400 } }"
            >
              <div class="absolute top-3 right-3 sm:top-4 sm:right-4 bg-green-100 text-green-700 px-2 sm:px-3 py-1 rounded-full text-xs font-semibold">
                ✓ Completada
              </div>
              <div class="text-gray-800 font-bold text-base sm:text-lg mb-2 pr-20 sm:pr-24">{{ task.title }}</div>
              <div class="text-gray-600 text-xs sm:text-sm mb-2 sm:mb-3 line-clamp-3">{{ task.description }}</div>
              <div class="flex items-center gap-2 text-xs text-gray-400">
                <svg class="w-3 h-3 sm:w-4 sm:h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <span class="text-xs">{{ formatDate(task.due_date) }}</span>
              </div>
              <div class="absolute inset-0 bg-gradient-to-br from-indigo-500/0 to-purple-500/0 group-hover:from-indigo-500/5 group-hover:to-purple-500/5 rounded-2xl transition-all duration-300"></div>
            </div>
            <p v-if="completedTasks.length === 0" class="col-span-full text-center text-gray-500 py-8 sm:py-12 text-sm sm:text-lg">
              <svg class="w-12 h-12 sm:w-16 sm:h-16 mx-auto mb-3 sm:mb-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" />
              </svg>
              No hay tareas terminadas aún.
            </p>
          </div>
        </div>
      </div>
    </main>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import { useToast } from 'vue-toastification'
import Swal from 'sweetalert2'
import TaskCard from '../components/TaskCard.vue'
import { API_URL } from '@/config'

const toast = useToast()
const tasks = ref([])
const draggingTask = ref(null)
const dragOverQuadrant = ref(null)
const dropIndex = ref(null)
const initialQuadrant = ref(null)
const initialIndex = ref(null)
const loading = ref(false)
const loadingCompleted = ref(false)

const filteredTasks = computed(() =>
  tasks.value.filter((task) => task.status !== 'completed')
)

const quadrant1Tasks = computed(() =>
  filteredTasks.value.filter((task) => task.important && task.urgent)
)
const quadrant2Tasks = computed(() =>
  filteredTasks.value.filter((task) => task.important && !task.urgent)
)
const quadrant3Tasks = computed(() =>
  filteredTasks.value.filter((task) => !task.important && task.urgent)
)
const quadrant4Tasks = computed(() =>
  filteredTasks.value.filter((task) => !task.important && !task.urgent)
)
const completedTasks = computed(() => tasks.value.filter((task) => task.status === 'completed'))

const getQuadrantTasks = (quadrant) => {
  switch (quadrant) {
    case 1: return quadrant1Tasks.value
    case 2: return quadrant2Tasks.value
    case 3: return quadrant3Tasks.value
    case 4: return quadrant4Tasks.value
    default: return []
  }
}

const formatDate = (dateString) => {
  if (!dateString) return 'Fecha no disponible'
  const date = new Date(dateString)
  return date.toLocaleDateString('es-ES', { 
    year: 'numeric', 
    month: 'long', 
    day: 'numeric',
    hour: '2-digit',
    minute: '2-digit'
  })
}

const fetchTasks = async () => {
  loading.value = true
  loadingCompleted.value = true

  try {
    const res = await fetch(`${API_URL}/tasks`)
    if (!res.ok) {
      throw new Error(`HTTP error! status: ${res.status}`)
    }
    tasks.value = await res.json()
  } catch (e) {
    toast.error('No se pudieron cargar las tareas. Inténtalo de nuevo más tarde.', {
      timeout: 4000
    })
  } finally {
    loading.value = false
    loadingCompleted.value = false
  }
}

onMounted(fetchTasks)

const dragTask = (task) => {
  draggingTask.value = task
  
  // Determinar cuadrante inicial y posición
  if (task.important && task.urgent) {
    initialQuadrant.value = 1
    initialIndex.value = quadrant1Tasks.value.findIndex(t => t.id === task.id)
  } else if (task.important && !task.urgent) {
    initialQuadrant.value = 2
    initialIndex.value = quadrant2Tasks.value.findIndex(t => t.id === task.id)
  } else if (!task.important && task.urgent) {
    initialQuadrant.value = 3
    initialIndex.value = quadrant3Tasks.value.findIndex(t => t.id === task.id)
  } else {
    initialQuadrant.value = 4
    initialIndex.value = quadrant4Tasks.value.findIndex(t => t.id === task.id)
  }
}

const onDragEnd = () => {
  // Limpiar el estado al terminar el drag (cuando se cancela o termina mal)
  setTimeout(() => {
    if (draggingTask.value) {
      draggingTask.value = null
      dragOverQuadrant.value = null
      dropIndex.value = null
      initialQuadrant.value = null
      initialIndex.value = null
    }
  }, 50)
}

const onDragEnter = (event, quadrant) => {
  dragOverQuadrant.value = quadrant
  
  // Establecer la posición inicial del placeholder al entrar en el cuadrante
  if (dropIndex.value === null) {
    const quadrantTasks = getQuadrantTasks(quadrant)
    // Si la tarea viene del mismo cuadrante, mantener su posición inicial
    if (initialQuadrant.value === quadrant) {
      dropIndex.value = initialIndex.value
    } else {
      // Si viene de otro cuadrante, colocar al final
      dropIndex.value = quadrantTasks.length
    }
  }
}

const onDragOverTask = (event, quadrant, index) => {
  event.preventDefault()
  dragOverQuadrant.value = quadrant
  
  // Calcular si debemos mostrar el placeholder antes o después de este elemento
  const rect = event.currentTarget.getBoundingClientRect()
  const midpoint = rect.top + rect.height / 2
  
  if (event.clientY < midpoint) {
    dropIndex.value = index
  } else {
    dropIndex.value = index + 1
  }
}

const onDragLeave = (event, quadrant) => {
  const rect = event.currentTarget.getBoundingClientRect()
  const x = event.clientX
  const y = event.clientY
  
  if (x < rect.left || x >= rect.right || y < rect.top || y >= rect.bottom) {
    dragOverQuadrant.value = null
    dropIndex.value = null
  }
}

const updateTaskLocally = (updatedTask) => {
  const index = tasks.value.findIndex((t) => t.id === updatedTask.id)
  if (index !== -1) {
    tasks.value[index] = { ...tasks.value[index], ...updatedTask }
  }
}

const reorderTasksInQuadrant = (quadrant, taskId, newIndex) => {
  const quadrantTasks = getQuadrantTasks(quadrant)
  const currentIndex = quadrantTasks.findIndex(t => t.id === taskId)
  
  if (currentIndex === -1) return
  
  // Crear una nueva copia del array de tareas
  const allTasks = [...tasks.value]
  const task = allTasks.find(t => t.id === taskId)
  
  // Obtener todas las tareas del cuadrante
  const tasksInQuadrant = allTasks.filter(t => {
    if (quadrant === 1) return t.important && t.urgent && t.status !== 'completed'
    if (quadrant === 2) return t.important && !t.urgent && t.status !== 'completed'
    if (quadrant === 3) return !t.important && t.urgent && t.status !== 'completed'
    if (quadrant === 4) return !t.important && !t.urgent && t.status !== 'completed'
    return false
  })
  
  // Reordenar
  const tasksCopy = tasksInQuadrant.filter(t => t.id !== taskId)
  const adjustedIndex = newIndex > currentIndex ? newIndex - 1 : newIndex
  tasksCopy.splice(adjustedIndex, 0, task)
  
  // Asignar nuevos order_index
  tasksCopy.forEach((t, idx) => {
    const taskInAll = allTasks.find(at => at.id === t.id)
    if (taskInAll) {
      taskInAll.order_index = idx
    }
  })
  
  tasks.value = allTasks
}

const handleDrop = async (quadrant, event) => {
  event.preventDefault()
  
  if (!draggingTask.value) return

  const targetQuadrant = quadrant
  const targetIndex = dropIndex.value !== null ? dropIndex.value : getQuadrantTasks(quadrant).length
  
  // Determinar si cambió de cuadrante o de posición
  const changedQuadrant = initialQuadrant.value !== targetQuadrant
  
  let changedPosition = false
  if (!changedQuadrant) {
    // Ajustar el índice si estamos en el mismo cuadrante
    const adjustedTargetIndex = targetIndex > initialIndex.value ? targetIndex - 1 : targetIndex
    changedPosition = adjustedTargetIndex !== initialIndex.value
  }
  
  // Limpiar estados visuales inmediatamente
  dragOverQuadrant.value = null
  dropIndex.value = null

  const originalTask = { ...draggingTask.value }

  // Actualizar las propiedades de cuadrante
  draggingTask.value.important = quadrant === 1 || quadrant === 2
  draggingTask.value.urgent = quadrant === 1 || quadrant === 3

  if (changedQuadrant) {
    // Cambió de cuadrante
    draggingTask.value.order_index = targetIndex
    updateTaskLocally(draggingTask.value)

    try {
      await saveTask(draggingTask.value)
      toast.success('Tarea movida correctamente', {
        timeout: 2000
      })
    } catch (error) {
      updateTaskLocally(originalTask)
      toast.error('No se pudo mover la tarea', {
        timeout: 3000
      })
    }
  } else if (changedPosition) {
    // Mismo cuadrante, diferente posición
    reorderTasksInQuadrant(quadrant, draggingTask.value.id, targetIndex)
    
    try {
      await saveTask(draggingTask.value)
      toast.success('Tarea reordenada correctamente', {
        timeout: 2000
      })
    } catch (error) {
      updateTaskLocally(originalTask)
      toast.error('No se pudo reordenar la tarea', {
        timeout: 3000
      })
    }
  }
  // Si no cambió nada, no mostramos mensaje

  draggingTask.value = null
  initialQuadrant.value = null
  initialIndex.value = null
}

const saveTask = async (task) => {
  const url = `${API_URL}/tasks/${task.id}`
  const res = await fetch(url, {
    method: 'PATCH',
    headers: { 'Content-Type': 'application/json' },
    body: JSON.stringify(task)
  })
  if (!res.ok) {
    throw new Error('Error al guardar la tarea')
  }
  return res.json()
}

const openNewTaskModal = async () => {
  const { value: formValues } = await Swal.fire({
    title: '<span class="text-xl sm:text-2xl font-bold text-gray-800">Nueva Tarea</span>',
    width: '90%',
    maxWidth: '700px',
    html: `
      <div class="space-y-4 sm:space-y-6 text-left">
        <div>
          <label for="swal-input1" class="block text-sm font-semibold text-gray-700 mb-2">
            Título
          </label>
          <input 
            id="swal-input1" 
            class="w-full px-3 sm:px-4 py-2 sm:py-3 text-sm sm:text-base border-2 border-gray-200 rounded-xl focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 outline-none transition-all" 
            placeholder="Ej: Preparar presentación para el cliente"
          >
        </div>
        <div>
          <label for="swal-input2" class="block text-sm font-semibold text-gray-700 mb-2">
            Descripción
          </label>
          <textarea 
            id="swal-input2" 
            class="w-full px-3 sm:px-4 py-2 sm:py-3 text-sm sm:text-base border-2 border-gray-200 rounded-xl focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 outline-none transition-all resize-none" 
            placeholder="Describe los detalles de la tarea..."
            rows="4"
          ></textarea>
        </div>
      </div>
    `,
    focusConfirm: false,
    showCancelButton: true,
    confirmButtonText: 'Crear',
    cancelButtonText: 'Cancelar',
    confirmButtonColor: '#4F46E5',
    cancelButtonColor: '#6B7280',
    customClass: {
      popup: 'rounded-2xl',
      confirmButton: 'rounded-xl px-4 sm:px-6 py-2 sm:py-3 text-sm sm:text-base font-semibold',
      cancelButton: 'rounded-xl px-4 sm:px-6 py-2 sm:py-3 text-sm sm:text-base font-semibold'
    },
    preConfirm: () => {
      const title = document.getElementById('swal-input1').value.trim()
      const description = document.getElementById('swal-input2').value.trim()
      if (!title || !description) {
        Swal.showValidationMessage('Por favor, completa todos los campos')
        return false
      }
      return { title, description }
    }
  })

  if (formValues) {
    const newTask = {
      title: formValues.title,
      description: formValues.description,
      important: true,
      urgent: true,
      status: 'pending'
    }

    try {
      const response = await fetch(`${API_URL}/tasks`, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify(newTask)
      })
      if (!response.ok) {
        throw new Error(`HTTP error! status: ${response.status}`)
      }
      const createdTask = await response.json()
      tasks.value.push(createdTask)

      toast.success('Tarea creada exitosamente', {
        timeout: 3000
      })
    } catch (error) {
      toast.error('Error al crear la tarea', {
        timeout: 3000
      })
    }
  }
}

const openTaskModal = async (task) => {
  const { value: formValues } = await Swal.fire({
    title: '<span class="text-xl sm:text-2xl font-bold text-gray-800">Editar Tarea</span>',
    width: '90%',
    maxWidth: '700px',
    html: `
      <div class="space-y-4 sm:space-y-6 text-left">
        <div>
          <label for="swal-input1" class="block text-sm font-semibold text-gray-700 mb-2">
            📌 Título
          </label>
          <input 
            id="swal-input1" 
            class="w-full px-3 sm:px-4 py-2 sm:py-3 text-sm sm:text-base border-2 border-gray-200 rounded-xl focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 outline-none transition-all" 
            value="${task.title || ''}"
          >
        </div>
        <div>
          <label for="swal-input2" class="block text-sm font-semibold text-gray-700 mb-2">
            📝 Descripción
          </label>
          <textarea 
            id="swal-input2" 
            class="w-full px-3 sm:px-4 py-2 sm:py-3 text-sm sm:text-base border-2 border-gray-200 rounded-xl focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 outline-none transition-all resize-none" 
            rows="4"
          >${task.description || ''}</textarea>
        </div>
      </div>
    `,
    focusConfirm: false,
    showCancelButton: true,
    confirmButtonText: 'Guardar',
    cancelButtonText: 'Cancelar',
    confirmButtonColor: '#4F46E5',
    cancelButtonColor: '#6B7280',
    customClass: {
      popup: 'rounded-2xl',
      confirmButton: 'rounded-xl px-4 sm:px-6 py-2 sm:py-3 text-sm sm:text-base font-semibold',
      cancelButton: 'rounded-xl px-4 sm:px-6 py-2 sm:py-3 text-sm sm:text-base font-semibold'
    },
    preConfirm: () => {
      const title = document.getElementById('swal-input1').value.trim()
      const description = document.getElementById('swal-input2').value.trim()
      if (!title || !description) {
        Swal.showValidationMessage('⚠️ Por favor, completa todos los campos')
        return false
      }
      return { title, description }
    }
  })

  if (formValues) {
    const originalTask = { ...task }
    const updatedTaskData = {
      ...task,
      title: formValues.title,
      description: formValues.description
    }

    updateTaskLocally(updatedTaskData)

    try {
      await saveTask(updatedTaskData)
      toast.success('Tarea actualizada correctamente', {
        timeout: 2500
      })
    } catch (error) {
      updateTaskLocally(originalTask)
      toast.error('Error al actualizar la tarea', {
        timeout: 3000
      })
    }
  }
}

const markAsCompleted = async (task) => {
  const originalStatus = task.status
  const originalDueDate = task.due_date

  task.status = 'completed'
  task.due_date = new Date().toISOString()
  updateTaskLocally(task)

  try {
    await saveTask(task)
    toast.success('Tarea completada', {
      timeout: 2500
    })
  } catch (error) {
    task.status = originalStatus
    task.due_date = originalDueDate
    updateTaskLocally(task)
    toast.error('Error al completar la tarea', {
      timeout: 3000
    })
  }
}

const deleteTask = async (task) => {
  const result = await Swal.fire({
    title: '¿Eliminar tarea?',
    text: 'Esta acción no se puede deshacer.',
    icon: 'warning',
    showCancelButton: true,
    confirmButtonText: 'Sí, eliminar',
    cancelButtonText: 'Cancelar',
    confirmButtonColor: '#EF4444',
    cancelButtonColor: '#6B7280',
    customClass: {
      popup: 'rounded-2xl',
      confirmButton: 'rounded-xl px-4 sm:px-6 py-2 sm:py-3 text-sm sm:text-base font-semibold',
      cancelButton: 'rounded-xl px-4 sm:px-6 py-2 sm:py-3 text-sm sm:text-base font-semibold'
    }
  })

  if (result.isConfirmed) {
    try {
      const response = await fetch(`${API_URL}/tasks/${task.id}`, { method: 'DELETE' })

      if (!response.ok) {
        throw new Error(`HTTP error! status: ${response.status}`)
      }
      tasks.value = tasks.value.filter((t) => t.id !== task.id)
      toast.success('Tarea eliminada correctamente', {
        timeout: 2500
      })
    } catch (error) {
      toast.error('Error al eliminar la tarea', {
        timeout: 3000
      })
    }
  }
}

const recoverCompletedTask = async (task) => {
  const confirm = await Swal.fire({
    title: '¿Recuperar tarea?',
    text: 'Selecciona el cuadrante al que deseas moverla.',
    input: 'select',
    inputOptions: {
      1: 'Hacer ya (Urgente e Importante)',
      2: 'Planificar (Importante, No urgente)',
      3: 'Delegar (Urgente, No importante)',
      4: 'Eliminar (Ni urgente ni importante)'
    },
    showCancelButton: true,
    confirmButtonText: 'Mover',
    cancelButtonText: 'Cancelar',
    confirmButtonColor: '#4F46E5',
    cancelButtonColor: '#6B7280',
    customClass: {
      popup: 'rounded-2xl',
      confirmButton: 'rounded-xl px-4 sm:px-6 py-2 sm:py-3 text-sm sm:text-base font-semibold',
      cancelButton: 'rounded-xl px-4 sm:px-6 py-2 sm:py-3 text-sm sm:text-base font-semibold'
    }
  })

  if (confirm.isConfirmed) {
    const originalTask = { ...task }
    const quadrant = parseInt(confirm.value)
    
    task.important = quadrant === 1 || quadrant === 2
    task.urgent = quadrant === 1 || quadrant === 3
    task.status = 'pending'
    task.due_date = null
    updateTaskLocally(task)

    try {
      await saveTask(task)
      toast.success('Tarea recuperada correctamente', {
        timeout: 2500
      })
    } catch (error) {
      updateTaskLocally(originalTask)
      toast.error('Error al recuperar la tarea', {
        timeout: 3000
      })
    }
  }
}
</script>

<style scoped>
.line-clamp-2 {
  display: -webkit-box;
  -webkit-line-clamp: 2;
  -webkit-box-orient: vertical;
  overflow: hidden;
}

.line-clamp-3 {
  display: -webkit-box;
  -webkit-line-clamp: 3;
  -webkit-box-orient: vertical;
  overflow: hidden;
}
</style>
