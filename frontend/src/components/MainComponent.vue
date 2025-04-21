<template>
  <div class="min-h-screen flex flex-col">
    <main class="flex-1 bg-gray-100 p-6">
      <div class="container mx-auto p-6">
        <div class="flex justify-center my-4">
          <button
            @click="openNewTaskModal"
            class="bg-indigo-600 hover:bg-indigo-700 text-white font-semibold py-2 px-4 rounded-lg shadow-md transition duration-200"
          >
            + Crear nueva tarea
          </button>
        </div>
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
            <div
              class="bg-blue-500 p-4 rounded-lg shadow-lg"
              @drop="moveTaskToQuadrant(1)"
              @dragover.prevent
            >
              <h2 class="text-xl font-semibold text-white text-center">HACER YA</h2>
              <hr class="my-2 border-white" />
              <div
                v-for="task in quadrant1Tasks"
                :key="task.id"
                class="task-item text-white hover:bg-blue-600 p-4 rounded-lg"
                draggable="true"
                @dragstart="dragTask(task)"
              >
                <div class="grid grid-cols-2 gap-4">
                  <div
                    class="flex justify-between items-center cursor-pointer"
                    @click="openTaskModal(task)"
                  >
                    <span>{{ task.title }}</span>
                  </div>
                </div>
              </div>
            </div>

            <!-- Cuadrante 2: PLANIFICAR -->
            <div
              class="bg-green-500 p-4 rounded-lg shadow-lg"
              @drop="moveTaskToQuadrant(2)"
              @dragover.prevent
            >
              <h2 class="text-xl font-semibold text-white text-center">PLANIFICAR</h2>
              <hr class="my-2 border-white" />
              <div
                v-for="task in quadrant2Tasks"
                :key="task.id"
                class="task-item text-white hover:bg-green-600 p-4 rounded-lg"
                draggable="true"
                @dragstart="dragTask(task)"
              >
                <div class="grid grid-cols-2 gap-4">
                  <div
                    class="flex justify-between items-center cursor-pointer"
                    @click="openTaskModal(task)"
                  >
                    <span>{{ task.title }}</span>
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
            <div
              class="bg-yellow-500 p-4 rounded-lg shadow-lg"
              @drop="moveTaskToQuadrant(3)"
              @dragover.prevent
            >
              <h2 class="text-xl font-semibold text-white text-center">DELEGAR</h2>
              <hr class="my-2 border-white" />
              <div
                v-for="task in quadrant3Tasks"
                :key="task.id"
                class="task-item text-white hover:bg-yellow-600 p-4 rounded-lg"
                draggable="true"
                @dragstart="dragTask(task)"
              >
                <div class="grid grid-cols-2 gap-4">
                  <div
                    class="flex justify-between items-center cursor-pointer"
                    @click="openTaskModal(task)"
                  >
                    <span>{{ task.title }}</span>
                  </div>
                </div>
              </div>
            </div>

            <!-- Cuadrante 4: ELIMINAR -->
            <div
              class="bg-gray-500 p-4 rounded-lg shadow-lg"
              @drop="moveTaskToQuadrant(4)"
              @dragover.prevent
            >
              <h2 class="text-xl font-semibold text-white text-center">ELIMINAR</h2>
              <hr class="my-2 border-white" />
              <div
                v-for="task in quadrant4Tasks"
                :key="task.id"
                class="task-item text-white hover:bg-gray-600 p-4 rounded-lg"
                draggable="true"
                @dragstart="dragTask(task)"
              >
                <div class="grid grid-cols-2 gap-4">
                  <div
                    class="flex justify-between items-center cursor-pointer"
                    @click="openTaskModal(task)"
                  >
                    <span>{{ task.title }}</span>
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
          <label class="block mb-2 text-sm text-gray-600">Tipo de tarea</label>
          <select v-model="selectedQuadrant" class="border p-2 w-full mb-4 text-gray-600">
            <option :value="1">🟦 Hacer ya (Importante + Urgente)</option>
            <option :value="2">🟩 Planificar (Importante + No urgente)</option>
            <option :value="3">🟨 Delegar (No importante + Urgente)</option>
            <option :value="4">⬜ Eliminar (No importante + No urgente)</option>
          </select>

          <input
            v-model="selectedTask.title"
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
    const selectedQuadrant = ref(1)
    const draggingTask = ref(null)

    const quadrant1Tasks = computed(() =>
      tasks.value.filter((task) => task.important && task.urgent),
    )
    const quadrant2Tasks = computed(() =>
      tasks.value.filter((task) => task.important && !task.urgent),
    )
    const quadrant3Tasks = computed(() =>
      tasks.value.filter((task) => !task.important && task.urgent),
    )
    const quadrant4Tasks = computed(() =>
      tasks.value.filter((task) => !task.important && !task.urgent),
    )

    onMounted(() => fetchTasks())

    const fetchTasks = async () => {
      try {
        const response = await fetch('http://localhost:8000/api/tasks')
        const data = await response.json()
        tasks.value = data
      } catch (error) {
        console.error('Error fetching tasks:', error)
      }
    }

    const dragTask = (task) => {
      draggingTask.value = task
    }

    const moveTaskToQuadrant = (quadrant) => {
      if (draggingTask.value) {
        draggingTask.value.important = quadrant === 1 || quadrant === 2
        draggingTask.value.urgent = quadrant === 1 || quadrant === 3
        saveTask(draggingTask.value).then(() => {
          // Actualizar el estado de los cuadrantes después de mover la tarea
          tasks.value = [...tasks.value] // Esto forzará que Vue reactive el cambio
        })
        draggingTask.value = null
      }
    }

    const saveTask = async (task) => {
      try {
        const response = await fetch(`http://localhost:8000/api/tasks/${task.id}`, {
          method: 'PATCH',
          headers: { 'Content-Type': 'application/json' },
          body: JSON.stringify({
            important: task.important,
            urgent: task.urgent,
          }),
        })
        if (!response.ok) throw new Error('Error updating task')
        const updatedTask = await response.json()

        // Actualizar solo la tarea modificada en el array tasks
        const index = tasks.value.findIndex((t) => t.id === updatedTask.id)
        if (index !== -1) tasks.value[index] = updatedTask
      } catch (error) {
        console.error('Error updating task:', error)
      }
    }


    const openTaskModal = (task) => {
      selectedTask.value = { ...task }
      selectedQuadrant.value = task.important ? (task.urgent ? 1 : 2) : task.urgent ? 3 : 4
      showModal.value = true
      isEditing.value = false
    }

    const closeModal = () => {
      showModal.value = false
    }

    const openNewTaskModal = () => {
      selectedTask.value = {
        id: null,
        title: '',
        description: '',
        important: false,
        urgent: false,
      }
      selectedQuadrant.value = 1 // Cuadrante por defecto
      isEditing.value = true
      showModal.value = true
    }

    const toggleEdit = async () => {
      if (!isEditing.value) {
        isEditing.value = true
        return
      }

      const task = selectedTask.value

      // Asignar importancia y urgencia según el cuadrante seleccionado
      task.important = selectedQuadrant.value === 1 || selectedQuadrant.value === 2
      task.urgent = selectedQuadrant.value === 1 || selectedQuadrant.value === 3

      try {
        let response

        if (task.id) {
          response = await fetch(`http://localhost:8000/api/tasks/${task.id}`, {
            method: 'PATCH',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({
              title: task.title,
              description: task.description,
              important: task.important,
              urgent: task.urgent,
            }),
          })
        } else {
          response = await fetch(`http://localhost:8000/api/tasks`, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(task),
          })
        }

        if (!response.ok) {
          throw new Error('Error al guardar la tarea')
        }

        const savedTask = await response.json()

        if (!task.id) {
          tasks.value.push(savedTask)
        } else {
          const index = tasks.value.findIndex((t) => t.id === savedTask.id)
          if (index !== -1) {
            tasks.value[index] = savedTask
          }
        }

        isEditing.value = false
        closeModal()
      } catch (error) {
        console.error('Error al guardar la tarea:', error)
      }
    }

    const deleteTask = async (task) => {
      if (!task.id) return closeModal()

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
      dragTask,
      deleteTask,
      openTaskModal,
      closeModal,
      showModal,
      openNewTaskModal,
      selectedTask,
      toggleEdit,
      isEditing,
      selectedQuadrant,
    }
  },
}
</script>

<style scoped>
.task-item {
  transition: background-color 0.3s ease;
  cursor: grab;
}
</style>
