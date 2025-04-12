<template>
  <div class="min-h-screen flex flex-col">
    <main class="flex-1 bg-gray-100 p-6">
      <div class="container mx-auto p-6">
        <h1 class="text-4xl text-gray-500 font-bold text-center mb-6">Eisenhower Matrix</h1>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-2 gap-6">
          <div class="bg-red-500 p-4 rounded-lg shadow-lg">
            <h2 class="text-2xl font-semibold text-white text-center">
              Quadrant 1: Important & Urgent
            </h2>
            <hr class="mt-3" />
            <div v-for="task in quadrant1Tasks" :key="task.id" class="text-white">
              <div class="flex justify-between items-center">
                <span>{{ task.title }}</span>
                <div class="flex space-x-2">
                  <button
                    class="text-white p-2 rounded-full text-sm"
                    @click="moveTaskToQuadrant(task, 2)"
                  >
                    🟦
                  </button>
                  <button
                    class="text-white p-2 rounded-full text-sm"
                    @click="moveTaskToQuadrant(task, 3)"
                  >
                    🟨
                  </button>
                  <button
                    class="text-white p-2 rounded-full text-sm"
                    @click="moveTaskToQuadrant(task, 4)"
                  >
                    🟩
                  </button>
                </div>
              </div>
            </div>
          </div>

          <div class="bg-blue-500 p-4 rounded-lg shadow-lg">
            <h2 class="text-2xl font-semibold text-white text-center">
              Quadrant 2: Important but Not Urgent
            </h2>
            <hr class="mt-3" />
            <div v-for="task in quadrant2Tasks" :key="task.id" class="text-white">
              <div class="flex justify-between items-center">
                <span>{{ task.title }}</span>
                <div class="flex space-x-2">
                  <button
                    class="text-white p-2 rounded-full text-sm"
                    @click="moveTaskToQuadrant(task, 1)"
                  >
                    🟥
                  </button>
                  <button
                    class="text-white p-2 rounded-full text-sm"
                    @click="moveTaskToQuadrant(task, 3)"
                  >
                    🟨
                  </button>
                  <button
                    class="text-white p-2 rounded-full text-sm"
                    @click="moveTaskToQuadrant(task, 4)"
                  >
                    🟩
                  </button>
                </div>
              </div>
            </div>
          </div>

          <div class="bg-yellow-500 p-4 rounded-lg shadow-lg">
            <h2 class="text-2xl font-semibold text-white text-center">
              Quadrant 3: Urgent but Not Important
            </h2>
            <hr class="mt-3" />
            <div v-for="task in quadrant3Tasks" :key="task.id" class="text-white">
              <div class="flex justify-between items-center">
                <span>{{ task.title }}</span>
                <div class="flex space-x-2">
                  <button
                    class="text-white p-2 rounded-full text-sm"
                    @click="moveTaskToQuadrant(task, 1)"
                  >
                    🟥
                  </button>
                  <button
                    class="text-white p-2 rounded-full text-sm"
                    @click="moveTaskToQuadrant(task, 2)"
                  >
                    🟦
                  </button>
                  <button
                    class="text-white p-2 rounded-full text-sm"
                    @click="moveTaskToQuadrant(task, 4)"
                  >
                    🟩
                  </button>
                </div>
              </div>
            </div>
          </div>

          <div class="bg-green-500 p-4 rounded-lg shadow-lg">
            <h2 class="text-2xl font-semibold text-white text-center">
              Quadrant 4: Neither Important nor Urgent
            </h2>
            <hr class="mt-3" />
            <div v-for="task in quadrant4Tasks" :key="task.id" class="text-white">
              <div class="flex justify-between items-center">
                <span>{{ task.title }}</span>
                <div class="flex space-x-2">
                  <button
                    class="text-white p-2 rounded-full text-sm"
                    @click="moveTaskToQuadrant(task, 1)"
                  >
                    🟥
                  </button>
                  <button
                    class="text-white p-2 rounded-full text-sm"
                    @click="moveTaskToQuadrant(task, 2)"
                  >
                    🟦
                  </button>
                  <button
                    class="text-white p-2 rounded-full text-sm"
                    @click="moveTaskToQuadrant(task, 3)"
                  >
                    🟨
                  </button>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </main>
  </div>
</template>

<script>
import { ref, computed, onMounted } from 'vue'

export default {
  name: 'MainComponent',
  setup() {
    const tasks = ref([])

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
        console.log('Fetching tasks...')
        const response = await fetch('http://localhost:8000/api/tasks')
        const data = await response.json()
        console.log('Fetched tasks:', data)
        tasks.value = data
      } catch (error) {
        console.error('Error fetching tasks:', error)
      }
    }

    const moveTaskToQuadrant = async (task, quadrant) => {
      console.log('Task data before update:', task)

      task.important = quadrant === 1 || quadrant === 2
      task.urgent = quadrant === 1 || quadrant === 3

      try {
        console.log('Updating task to quadrant:', quadrant)
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
        console.log('Task updated:', updatedTask)

        // Actualizar la tarea en el estado local (tasks)
        const index = tasks.value.findIndex((t) => t.id === task.id)
        if (index !== -1) {
          tasks.value[index] = updatedTask
          console.log('Updated task in local state:', tasks.value[index])
        }
      } catch (error) {
        console.error('Error updating task:', error)
      }
    }

    return {
      tasks,
      quadrant1Tasks,
      quadrant2Tasks,
      quadrant3Tasks,
      quadrant4Tasks,
      moveTaskToQuadrant,
    }
  },
}
</script>

<style scoped>
/* Estilos personalizados aquí */
</style>
