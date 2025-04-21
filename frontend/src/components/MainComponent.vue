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
            <div class="text-center text-lg font-semibold text-gray-700">Urgente</div>
            <div class="text-center text-lg font-semibold text-gray-700">No urgente</div>

            <div class="flex items-center justify-center text-lg font-semibold text-gray-700 mb-2"
                 style="writing-mode: vertical-lr; transform: rotate(180deg)">
              Importante
            </div>

            <!-- Cuadrante 1: HACER YA -->
            <div class="bg-blue-500 p-4 rounded-lg shadow-lg">
              <h2 class="text-xl font-semibold text-white text-center">HACER YA</h2>
              <hr class="my-2 border-white" />
              <draggable
                v-model="quadrant1Tasks"
                group="tasks"
                item-key="id"
                @end="onReorder(1)"
              >
                <template #item="{ element }">
                  <div
                    class="task-item text-white hover:bg-blue-600 p-4 rounded-lg"
                    @click="openTaskModal(element)"
                  >
                    <span>{{ element.title }}</span>
                  </div>
                </template>
              </draggable>
            </div>

            <!-- Cuadrante 2: PLANIFICAR -->
            <div class="bg-green-500 p-4 rounded-lg shadow-lg">
              <h2 class="text-xl font-semibold text-white text-center">PLANIFICAR</h2>
              <hr class="my-2 border-white" />
              <draggable
                v-model="quadrant2Tasks"
                group="tasks"
                item-key="id"
                @end="onReorder(2)"
              >
                <template #item="{ element }">
                  <div
                    class="task-item text-white hover:bg-green-600 p-4 rounded-lg"
                    @click="openTaskModal(element)"
                  >
                    <span>{{ element.title }}</span>
                  </div>
                </template>
              </draggable>
            </div>

            <div class="flex items-center justify-center text-lg font-semibold text-gray-700 mb-2"
                 style="writing-mode: vertical-lr; transform: rotate(180deg)">
              No importante
            </div>

            <!-- Cuadrante 3: DELEGAR -->
            <div class="bg-yellow-500 p-4 rounded-lg shadow-lg">
              <h2 class="text-xl font-semibold text-white text-center">DELEGAR</h2>
              <hr class="my-2 border-white" />
              <draggable
                v-model="quadrant3Tasks"
                group="tasks"
                item-key="id"
                @end="onReorder(3)"
              >
                <template #item="{ element }">
                  <div
                    class="task-item text-white hover:bg-yellow-600 p-4 rounded-lg"
                    @click="openTaskModal(element)"
                  >
                    <span>{{ element.title }}</span>
                  </div>
                </template>
              </draggable>
            </div>

            <!-- Cuadrante 4: ELIMINAR -->
            <div class="bg-gray-500 p-4 rounded-lg shadow-lg">
              <h2 class="text-xl font-semibold text-white text-center">ELIMINAR</h2>
              <hr class="my-2 border-white" />
              <draggable
                v-model="quadrant4Tasks"
                group="tasks"
                item-key="id"
                @end="onReorder(4)"
              >
                <template #item="{ element }">
                  <div
                    class="task-item text-white hover:bg-gray-600 p-4 rounded-lg"
                    @click="openTaskModal(element)"
                  >
                    <span>{{ element.title }}</span>
                  </div>
                </template>
              </draggable>
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

        <div v-if="isEditing">
          <label class="block mb-2 text-sm text-gray-600">Tipo de tarea</label>
          <select v-model="selectedQuadrant" class="border p-2 w-full mb-4 text-gray-600">
            <option :value="1">🟥 Hacer ya (Importante + Urgente)</option>
            <option :value="2">🟦 Planificar (Importante + No urgente)</option>
            <option :value="3">🟩 Delegar (No importante + Urgente)</option>
            <option :value="4">🟨 Eliminar (No importante + No urgente)</option>
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
import draggable from 'vuedraggable'

export default {
  name: 'MainComponent',
  components: { draggable },
  setup() {
    const tasks = ref([])
    const showModal = ref(false)
    const selectedTask = ref(null)
    const isEditing = ref(false)
    const selectedQuadrant = ref(1)

    const fetchTasks = async () => {
      try {
        const response = await fetch('http://localhost:8000/api/tasks')
        const data = await response.json()
        tasks.value = data.sort((a, b) => (a.position ?? 0) - (b.position ?? 0))
      } catch (error) {
        console.error('Error fetching tasks:', error)
      }
    }

    const quadrant1Tasks = computed({
      get: () => tasks.value.filter(t => t.important && t.urgent),
      set: newList => updateTasksFromQuadrant(newList, 1)
    })
    const quadrant2Tasks = computed({
      get: () => tasks.value.filter(t => t.important && !t.urgent),
      set: newList => updateTasksFromQuadrant(newList, 2)
    })
    const quadrant3Tasks = computed({
      get: () => tasks.value.filter(t => !t.important && t.urgent),
      set: newList => updateTasksFromQuadrant(newList, 3)
    })
    const quadrant4Tasks = computed({
      get: () => tasks.value.filter(t => !t.important && !t.urgent),
      set: newList => updateTasksFromQuadrant(newList, 4)
    })

    const updateTasksFromQuadrant = (newList, quadrant) => {
      newList.forEach((task, index) => {
        task.position = index
        task.important = quadrant === 1 || quadrant === 2
        task.urgent = quadrant === 1 || quadrant === 3
      })
      tasks.value = [
        ...tasks.value.filter(t => {
          const belongs =
            (quadrant === 1 && t.important && t.urgent) ||
            (quadrant === 2 && t.important && !t.urgent) ||
            (quadrant === 3 && !t.important && t.urgent) ||
            (quadrant === 4 && !t.important && !t.urgent)
          return !belongs
        }),
        ...newList
      ]
    }

    const onReorder = async (quadrant) => {
      const list =
        quadrant === 1
          ? quadrant1Tasks.value
          : quadrant === 2
          ? quadrant2Tasks.value
          : quadrant === 3
          ? quadrant3Tasks.value
          : quadrant4Tasks.value

      for (let i = 0; i < list.length; i++) {
        await saveTask({ ...list[i], position: i })
      }
    }

    const saveTask = async (task) => {
      try {
        const response = await fetch(`http://localhost:8000/api/tasks/${task.id}`, {
          method: 'PATCH',
          headers: { 'Content-Type': 'application/json' },
          body: JSON.stringify(task)
        })
        if (!response.ok) throw new Error('Error updating task')
      } catch (error) {
        console.error('Error saving task:', error)
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
        important: true,
        urgent: true,
        position: 0
      }
      selectedQuadrant.value = 1
      isEditing.value = true
      showModal.value = true
    }

    const toggleEdit = async () => {
      if (!isEditing.value) {
        isEditing.value = true
        return
      }

      const task = selectedTask.value
      task.important = selectedQuadrant.value === 1 || selectedQuadrant.value === 2
      task.urgent = selectedQuadrant.value === 1 || selectedQuadrant.value === 3

      try {
        const method = task.id ? 'PATCH' : 'POST'
        const url = `http://localhost:8000/api/tasks${task.id ? '/' + task.id : ''}`
        const response = await fetch(url, {
          method,
          headers: { 'Content-Type': 'application/json' },
          body: JSON.stringify(task)
        })

        if (!response.ok) throw new Error('Error al guardar la tarea')
        const saved = await response.json()

        if (!task.id) {
          tasks.value.push(saved)
        } else {
          const index = tasks.value.findIndex(t => t.id === saved.id)
          if (index !== -1) tasks.value[index] = saved
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
          method: 'DELETE'
        })
        if (!response.ok) throw new Error('Error al eliminar la tarea')
        tasks.value = tasks.value.filter(t => t.id !== task.id)
        closeModal()
      } catch (error) {
        console.error('Error deleting task:', error)
      }
    }

    onMounted(fetchTasks)

    return {
      quadrant1Tasks,
      quadrant2Tasks,
      quadrant3Tasks,
      quadrant4Tasks,
      showModal,
      selectedTask,
      selectedQuadrant,
      isEditing,
      openTaskModal,
      closeModal,
      openNewTaskModal,
      toggleEdit,
      deleteTask,
      onReorder
    }
  }
}
</script>

<style scoped>
.task-item {
  transition: background-color 0.3s ease;
  cursor: grab;
}
</style>
