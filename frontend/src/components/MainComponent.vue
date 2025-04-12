<template>
  <div class="min-h-screen flex flex-col">
    <main class="flex-1 bg-gray-100 p-6">
      <div class="container mx-auto p-6">
        <h1 class="text-4xl text-gray-700 font-bold text-center mb-1">Organizador de Tareas</h1>
        <span class="text-sm text-gray-800 italic text-center block">Matriz de Eisenhower</span>

        <!-- Matriz Eisenhower -->
        <div class="mt-10">
          <div class="grid grid-cols-[auto,1fr,1fr] gap-4">
            <!-- Esquina vacía -->
            <div></div>

            <!-- Cabeceras horizontales -->
            <div class="text-center text-lg font-semibold text-gray-700">Urgente</div>
            <div class="text-center text-lg font-semibold text-gray-700">No urgente</div>

            <div
              class="flex items-center justify-center text-lg font-semibold text-gray-700 mb-2"
              style="writing-mode: vertical-lr; transform: rotate(180deg)"
            >
              Importante
            </div>

            <!-- Cuadrante 1: HACER YA -->
            <div class="bg-red-500 p-4 rounded-lg shadow-lg">
              <h2 class="text-xl font-semibold text-white text-center">HACER YA</h2>
              <hr class="my-2 border-white" />
              <div
                v-for="task in quadrant1Tasks"
                :key="task.id"
                class="task-item text-white hover:bg-red-600 p-4 rounded-lg"
              >
                <div class="grid grid-cols-2 gap-4">
                  <!-- Título de la tarea (abre modal) -->
                  <div
                    class="flex justify-between items-center cursor-pointer"
                    @click="openTaskModal(task)"
                  >
                    <span>{{ task.title }}</span>
                  </div>

                  <!-- Botones para mover a otros cuadrantes -->
                  <div class="flex space-x-2 justify-end items-center">
                    <button @click="moveTaskToQuadrant(task, 2)">🟦</button>
                    <button @click="moveTaskToQuadrant(task, 3)">🟩</button>
                    <button @click="moveTaskToQuadrant(task, 4)">🟨</button>
                  </div>
                </div>
              </div>
            </div>

            <!-- Cuadrante 2: PLANIFICAR -->
            <div class="bg-blue-500 p-4 rounded-lg shadow-lg">
              <h2 class="text-xl font-semibold text-white text-center">PLANIFICAR</h2>
              <hr class="my-2 border-white" />
              <div
                v-for="task in quadrant2Tasks"
                :key="task.id"
                class="task-item text-white hover:bg-blue-600 p-4 rounded-lg"
              >
                <div class="grid grid-cols-2 gap-4">
                  <!-- Título de la tarea (abre modal) -->
                  <div
                    class="flex justify-between items-center cursor-pointer"
                    @click="openTaskModal(task)"
                  >
                    <span>{{ task.title }}</span>
                  </div>

                  <!-- Botones para mover a otros cuadrantes -->
                  <div class="flex space-x-2 justify-end items-center">
                    <button @click="moveTaskToQuadrant(task, 1)">🟥</button>
                    <button @click="moveTaskToQuadrant(task, 3)">🟩</button>
                    <button @click="moveTaskToQuadrant(task, 4)">🟨</button>
                  </div>
                </div>
              </div>
            </div>

            <div
              class="flex items-center justify-center text-lg font-semibold text-gray-700 mb-2"
              style="writing-mode: vertical-lr; transform: rotate(180deg)"
            >
              No importante
            </div>

            <!-- Cuadrante 3: DELEGAR -->
            <div class="bg-green-500 p-4 rounded-lg shadow-lg">
              <h2 class="text-xl font-semibold text-white text-center">DELEGAR</h2>
              <hr class="my-2 border-white" />
              <div
                v-for="task in quadrant3Tasks"
                :key="task.id"
                class="task-item text-white hover:bg-green-600 p-4 rounded-lg"
              >
                <div class="grid grid-cols-2 gap-4">
                  <!-- Título de la tarea (abre modal) -->
                  <div
                    class="flex justify-between items-center cursor-pointer"
                    @click="openTaskModal(task)"
                  >
                    <span>{{ task.title }}</span>
                  </div>

                  <!-- Botones para mover a otros cuadrantes -->
                  <div class="flex space-x-2 justify-end items-center">
                    <button @click="moveTaskToQuadrant(task, 1)">🟥</button>
                    <button @click="moveTaskToQuadrant(task, 2)">🟦</button>
                    <button @click="moveTaskToQuadrant(task, 4)">🟨</button>
                  </div>
                </div>
              </div>
            </div>

            <!-- Cuadrante 4: DESECHAR -->
            <div class="bg-yellow-500 p-4 rounded-lg shadow-lg">
              <h2 class="text-xl font-semibold text-white text-center">ELIMINAR</h2>
              <hr class="my-2 border-white" />
              <div
                v-for="task in quadrant4Tasks"
                :key="task.id"
                class="task-item text-white hover:bg-yellow-600 p-4 rounded-lg"
              >
                <div class="grid grid-cols-2 gap-4">
                  <!-- Título de la tarea (abre modal) -->
                  <div
                    class="flex justify-between items-center cursor-pointer"
                    @click="openTaskModal(task)"
                  >
                    <span>{{ task.title }}</span>
                  </div>

                  <!-- Botones para mover a otros cuadrantes -->
                  <div class="flex space-x-2 justify-end items-center">
                    <button @click="moveTaskToQuadrant(task, 1)">🟥</button>
                    <button @click="moveTaskToQuadrant(task, 2)">🟦</button>
                    <button @click="moveTaskToQuadrant(task, 3)">🟩</button>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </main>

    <!-- Modal -->
    <div
      v-if="showModal"
      class="fixed inset-0 bg-black bg-opacity-50 flex justify-center items-center"
      @click.self="closeModal"
    >
      <div class="bg-white p-6 rounded-lg w-1/3">
        <h2 class="text-2xl text-gray-800 font-semibold mb-4">{{ selectedTask.title }}</h2>
        <p class="mb-4 text-gray-500" v-if="!isEditing">{{ selectedTask.description }}</p>

        <!-- Formulario de edición -->
        <div v-if="isEditing">
          <input
            v-model="selectedTask.title"
            type="text"
            class="text-gray-500 border p-2 w-full mb-4"
            placeholder="Título de la tarea"
          />
          <textarea
            v-model="selectedTask.description"
            class="text-gray-500 border p-2 w-full mb-4"
            placeholder="Descripción de la tarea"
          ></textarea>
        </div>

        <div class="flex justify-end space-x-4">
          <button @click="toggleEdit" class="text-blue-500">
            {{ isEditing ? 'Guardar' : 'Editar' }}
          </button>
          <button @click="deleteTask(selectedTask)" class="text-red-500">Eliminar</button>
          <button @click="closeModal" class="text-gray-500">Cerrar</button>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import { ref, computed, onMounted } from 'vue'

export default {
  name: 'MainComponent',
  setup() {
    const tasks = ref([])
    const showModal = ref(false)
    const selectedTask = ref(null)
    const isEditing = ref(false)

    const quadrant1Tasks = computed(() => {
      return tasks.value.filter((task) => task.important && task.urgent)
    })
    const quadrant2Tasks = computed(() => {
      return tasks.value.filter((task) => task.important && !task.urgent)
    })
    const quadrant3Tasks = computed(() => {
      return tasks.value.filter((task) => !task.important && task.urgent)
    })
    const quadrant4Tasks = computed(() => {
      return tasks.value.filter((task) => !task.important && !task.urgent)
    })

    onMounted(() => {
      fetchTasks()
    })

    const fetchTasks = async () => {
      try {
        const response = await fetch('http://localhost:8000/api/tasks')
        const data = await response.json()
        tasks.value = data
      } catch (error) {
        console.error('Error fetching tasks:', error)
      }
    }

    const moveTaskToQuadrant = async (task, quadrant) => {
      task.important = quadrant === 1 || quadrant === 2
      task.urgent = quadrant === 1 || quadrant === 3

      try {
        const response = await fetch(`http://localhost:8000/api/tasks/${task.id}`, {
          method: 'PATCH',
          headers: {
            'Content-Type': 'application/json',
          },
          body: JSON.stringify({
            important: task.important,
            urgent: task.urgent,
          }),
        })

        if (!response.ok) {
          throw new Error('Error updating task')
        }

        const updatedTask = await response.json()
        const index = tasks.value.findIndex((t) => t.id === task.id)
        if (index !== -1) {
          tasks.value[index] = updatedTask
        }
      } catch (error) {
        console.error('Error updating task:', error)
      }
    }

    const openTaskModal = (task) => {
      selectedTask.value = task
      showModal.value = true
      isEditing.value = false
    }

    const closeModal = () => {
      showModal.value = false
    }

    const toggleEdit = () => {
      isEditing.value = !isEditing.value
    }

    const deleteTask = async (task) => {
      try {
        const response = await fetch(`http://localhost:8000/api/tasks/${task.id}`, {
          method: 'DELETE',
        })

        if (!response.ok) {
          throw new Error('Error deleting task')
        }

        tasks.value = tasks.value.filter((t) => t.id !== task.id)
        closeModal()
      } catch (error) {
        console.error('Error deleting task:', error)
      }
    }

    return {
      tasks,
      quadrant1Tasks,
      quadrant2Tasks,
      quadrant3Tasks,
      quadrant4Tasks,
      moveTaskToQuadrant,
      openTaskModal,
      closeModal,
      showModal,
      selectedTask,
      toggleEdit,
      deleteTask,
      isEditing,
    }
  },
}
</script>

<style scoped>
/* Estilo para hover en las tareas */
.task-item {
  transition: background-color 0.3s ease;
}

.task-item:hover {
  cursor: pointer;
}
</style>
