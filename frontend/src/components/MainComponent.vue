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
            <div></div>
            <div class="text-center text-lg font-semibold text-gray-700">Urgente</div>
            <div class="text-center text-lg font-semibold text-gray-700">No urgente</div>

            <div
              class="flex items-center justify-center text-lg font-semibold text-gray-700 mb-2"
              style="writing-mode: vertical-lr; transform: rotate(180deg)"
            >
              Importante
            </div>

            <!-- Cuadrante 1 -->
            <div
              class="bg-blue-500 p-4 rounded-lg shadow-lg"
              @drop="moveTaskToQuadrant(1)"
              @dragover.prevent
            >
              <h2 class="text-xl font-semibold text-white text-center">HACER YA</h2>
              <hr class="my-2 border-white" />
              <TaskCard
                v-for="task in quadrant1Tasks"
                :key="task.id"
                :task="task"
                color="blue"
                @drag-task="dragTask"
                @edit-task="openTaskModal"
                @complete-task="markAsCompleted"
                @delete-task="deleteTask"
              />
            </div>

            <!-- Cuadrante 2 -->
            <div
              class="bg-green-500 p-4 rounded-lg shadow-lg"
              @drop="moveTaskToQuadrant(2)"
              @dragover.prevent
            >
              <h2 class="text-xl font-semibold text-white text-center">PLANIFICAR</h2>
              <hr class="my-2 border-white" />
              <TaskCard
                v-for="task in quadrant2Tasks"
                :key="task.id"
                :task="task"
                color="green"
                @drag-task="dragTask"
                @edit-task="openTaskModal"
                @complete-task="markAsCompleted"
                @delete-task="deleteTask"
              />
            </div>

            <div
              class="flex items-center justify-center text-lg font-semibold text-gray-700 mb-2"
              style="writing-mode: vertical-lr; transform: rotate(180deg)"
            >
              No importante
            </div>

            <!-- Cuadrante 3 -->
            <div
              class="bg-yellow-500 p-4 rounded-lg shadow-lg"
              @drop="moveTaskToQuadrant(3)"
              @dragover.prevent
            >
              <h2 class="text-xl font-semibold text-white text-center">DELEGAR</h2>
              <hr class="my-2 border-white" />
              <TaskCard
                v-for="task in quadrant3Tasks"
                :key="task.id"
                :task="task"
                color="yellow"
                @drag-task="dragTask"
                @edit-task="openTaskModal"
                @complete-task="markAsCompleted"
                @delete-task="deleteTask"
              />
            </div>

            <!-- Cuadrante 4 -->
            <div
              class="bg-gray-500 p-4 rounded-lg shadow-lg"
              @drop="moveTaskToQuadrant(4)"
              @dragover.prevent
            >
              <h2 class="text-xl font-semibold text-white text-center">ELIMINAR</h2>
              <hr class="my-2 border-white" />
              <TaskCard
                v-for="task in quadrant4Tasks"
                :key="task.id"
                :task="task"
                color="gray"
                @drag-task="dragTask"
                @edit-task="openTaskModal"
                @complete-task="markAsCompleted"
                @delete-task="deleteTask"
              />
            </div>
          </div>
        </div>

        <!-- Lista de tareas finalizadas -->
        <div class="mt-12">
          <hr class="my-2 border-black" />
          <h2 class="text-center text-xl font-semibold text-gray-700 mb-4 mt-4">TERMINADAS</h2>
          <div v-if="loadingCompleted" class="text-center text-gray-600">Cargando tareas terminadas...</div>
          <div v-else class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            <div
              v-for="task in completedTasks"
              :key="task.id"
              class="bg-gray-200 p-4 rounded-lg shadow border border-gray-300 text-gray-500 hover:bg-gray-300 cursor-pointer"
              @click="recoverCompletedTask(task)"
            >
              <div class="text-gray-800 font-medium">{{ task.title }}</div>
              <div class="text-gray-500 text-sm">{{ task.description }}</div>
              <div class="text-sm text-gray-400">Completada el: {{ new Date(task.due_date).toLocaleString() }}</div>
            </div>
             <p v-if="completedTasks.length === 0" class="text-center text-gray-500 col-span-full">
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
import Swal from 'sweetalert2'
import TaskCard from '../components/TaskCard.vue'
import { API_URL } from '@/config'

const tasks = ref([])
const draggingTask = ref(null)
const loading = ref(false) // Nuevo estado de carga global
const loadingCompleted = ref(false); // Nuevo estado de carga para tareas completadas

// Se recomienda agrupar los filters para los computed properties
// de esa forma no tendrías un .filter() por cada computed property
const filteredTasks = computed(() =>
  tasks.value.filter((task) => task.status !== 'completed')
);

const quadrant1Tasks = computed(() =>
  filteredTasks.value.filter((task) => task.important && task.urgent)
);
const quadrant2Tasks = computed(() =>
  filteredTasks.value.filter((task) => task.important && !task.urgent)
);
const quadrant3Tasks = computed(() =>
  filteredTasks.value.filter((task) => !task.important && task.urgent)
);
const quadrant4Tasks = computed(() =>
  filteredTasks.value.filter((task) => !task.important && !task.urgent)
);
const completedTasks = computed(() => tasks.value.filter((task) => task.status === 'completed'));

const fetchTasks = async () => {
  loading.value = true; // Iniciar spinner
  loadingCompleted.value = true; // Iniciar spinner para tareas completadas

  try {
    const res = await fetch(`${API_URL}/tasks`);
    if (!res.ok) {
      throw new Error(`HTTP error! status: ${res.status}`);
    }
    tasks.value = await res.json();
  } catch (e) {
    Swal.fire('Error', 'No se pudieron cargar las tareas. Inténtalo de nuevo más tarde.', 'error');
  } finally {
    loading.value = false; // Detener spinner
    loadingCompleted.value = false; // Detener spinner para tareas completadas
  }
};

onMounted(fetchTasks);

const dragTask = (task) => {
  draggingTask.value = task;
};

const updateTaskLocally = (updatedTask) => {
  const index = tasks.value.findIndex((t) => t.id === updatedTask.id);
  if (index !== -1) {
    tasks.value[index] = { ...tasks.value[index], ...updatedTask };
  }
};

const moveTaskToQuadrant = async (quadrant) => {
  if (!draggingTask.value) return;

  const originalTask = { ...draggingTask.value }; // Guardar una copia por si falla la API
  const taskId = draggingTask.value.id;

  // Optimistic UI update
  draggingTask.value.important = quadrant === 1 || quadrant === 2;
  draggingTask.value.urgent = quadrant === 1 || quadrant === 3;
  updateTaskLocally(draggingTask.value);

  try {
    await saveTask(draggingTask.value);
    Swal.fire({
      icon: 'success',
      title: 'Tarea movida',
      showConfirmButton: false,
      timer: 1500,
    });
  } catch (error) {
    // Revertir el cambio si la API falla
    updateTaskLocally(originalTask);
    Swal.fire('Error', 'No se pudo mover la tarea. Inténtalo de nuevo.', 'error');
  } finally {
    draggingTask.value = null;
  }
};

const saveTask = async (task) => {
  const url = `${API_URL}/tasks/${task.id}`;
  const res = await fetch(url, {
    method: 'PATCH',
    headers: { 'Content-Type': 'application/json' },
    body: JSON.stringify(task),
  });
  if (!res.ok) {
    throw new Error('Error al guardar la tarea');
  }
  return res.json();
};

const openNewTaskModal = async () => {
  const { value: formValues } = await Swal.fire({
    title: 'Nueva tarea',
    width: '700px',
    html: `
      <div class="swal2-html-container-custom">
        <div class="swal2-container-custom">
          <input id="swal-input1" class="swal2-input" placeholder="Título">
        </div>
        <div class="swal2-container-custom">
          <textarea id="swal-input2" class="swal2-textarea" placeholder="Descripción" style="height: 130px;"></textarea>
        </div>
      </div>
    `,
    customClass: {
      htmlContainer: 'swal2-html-container-custom',
    },
    focusConfirm: false,
    showCancelButton: true,
    confirmButtonText: '➕ Crear',
    cancelButtonText: 'Cancelar',
    preConfirm: () => {
      const title = document.getElementById('swal-input1').value.trim();
      const description = document.getElementById('swal-input2').value.trim();
      if (!title || !description) {
        Swal.showValidationMessage('Por favor, completa todos los campos');
        return false;
      }
      return { title, description };
    },
  });

  if (formValues) {
    Swal.fire({
      title: 'Creando tarea...',
      allowOutsideClick: false,
      didOpen: () => {
        Swal.showLoading();
      },
    });

    const newTask = {
      title: formValues.title,
      description: formValues.description,
      important: true,
      urgent: true,
      status: 'pending',
    };

    try {
      const response = await fetch(`${API_URL}/tasks`, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify(newTask),
      });
      if (!response.ok) {
        throw new Error(`HTTP error! status: ${response.status}`);
      }
      const createdTask = await response.json();
      tasks.value.push(createdTask); // Actualizar solo el array

      Swal.close();
      Swal.fire('✅ Tarea creada', '', 'success');
    } catch (error) {
      Swal.close();
      Swal.fire('❌ Error al crear tarea', 'No se pudo crear la tarea. Inténtalo de nuevo.', 'error');
    }
  }
};

const openTaskModal = async (task) => {
  const { value: formValues } = await Swal.fire({
    title: 'Editar tarea',
    width: '700px',
    html: `
      <div class="swal2-html-container-custom">
        <div class="swal2-container-custom">
          <label for="swal-input1"><strong>Título</strong></label>
          <input id="swal-input1" class="swal2-input" value="${task.title || ''}" placeholder="Título">
        </div>
        <div class="swal2-container-custom">
          <label for="swal-input2"><strong>Descripción</strong></label>
          <textarea id="swal-input2" class="swal2-textarea" placeholder="Descripción" style="height: 130px;">${task.description || ''}</textarea>
        </div>
      </div>
    `,
    customClass: {
      htmlContainer: 'swal2-html-container-custom',
    },
    focusConfirm: false,
    showCancelButton: true,
    confirmButtonText: '💾 Guardar',
    cancelButtonText: '❌ Cancelar',
    preConfirm: () => {
      const title = document.getElementById('swal-input1').value.trim();
      const description = document.getElementById('swal-input2').value.trim();
      if (!title || !description) {
        Swal.showValidationMessage('Por favor, completa todos los campos');
        return false;
      }
      return { title, description };
    },
  });

  if (formValues) {
    const originalTask = { ...task }; // Guardar una copia por si falla la API
    const updatedTaskData = {
      ...task,
      title: formValues.title,
      description: formValues.description,
    };

    // Optimistic UI update
    updateTaskLocally(updatedTaskData);

    try {
      Swal.fire({
        title: 'Guardando cambios...',
        allowOutsideClick: false,
        didOpen: () => {
          Swal.showLoading();
        },
      });
      await saveTask(updatedTaskData);
      Swal.close();
      Swal.fire('✅ Tarea actualizada', '', 'success');
    } catch (error) {
      // Revertir el cambio si la API falla
      updateTaskLocally(originalTask);
      Swal.close();
      Swal.fire('❌ Error al actualizar tarea', 'No se pudieron guardar los cambios. Inténtalo de nuevo.', 'error');
    }
  }
};

const markAsCompleted = async (task) => {
  const originalStatus = task.status;
  const originalDueDate = task.due_date;

  // Optimistic UI update
  task.status = 'completed';
  task.due_date = new Date().toISOString();
  updateTaskLocally(task);

  try {
    Swal.fire({
      title: 'Marcando como completada...',
      allowOutsideClick: false,
      didOpen: () => {
        Swal.showLoading();
      },
    });
    await saveTask(task);
    Swal.close();
    Swal.fire('✅ Tarea completada', '', 'success');
  } catch (error) {
    task.status = originalStatus;
    task.due_date = originalDueDate;
    updateTaskLocally(task);
    Swal.close();
    Swal.fire('❌ Error al completar tarea', 'No se pudo marcar como completada. Inténtalo de nuevo.', 'error');
  }
};

const deleteTask = async (task) => {
  const result = await Swal.fire({
    title: '¿Eliminar tarea?',
    showDenyButton: true,
    text: 'Esta acción no se puede deshacer.',
    icon: 'warning',
    confirmButtonText: 'Sí, eliminar',
    denyButtonText: 'Cancelar',
  });

  if (result.isConfirmed) {
    try {
      Swal.fire({
        title: 'Eliminando tarea...',
        allowOutsideClick: false,
        didOpen: () => {
          Swal.showLoading();
        },
      });

      const response = await fetch(`${API_URL}/tasks/${task.id}`, { method: 'DELETE' })

      if (!response.ok) {
        throw new Error(`HTTP error! status: ${response.status}`);
      }
      tasks.value = tasks.value.filter((t) => t.id !== task.id);
      Swal.close();
      Swal.fire('¡Tarea eliminada!', '', 'success');
    } catch (error) {
      Swal.close();
      Swal.fire('❌ Error al eliminar tarea', 'No se pudo eliminar la tarea. Inténtalo de nuevo.', 'error');
    }
  } else if (result.isDenied) {
    Swal.fire('¡La tarea no ha sido eliminada!', '', 'info');
  }
};

const recoverCompletedTask = async (task) => {
  const confirm = await Swal.fire({
    title: '¿Recuperar tarea?',
    text: 'Selecciona el cuadrante al que deseas moverla.',
    input: 'select',
    inputOptions: {
      1: '🟦 Hacer ya',
      2: '🟩 Planificar',
      3: '🟨 Delegar',
      4: '⬜ Eliminar',
    },
    showCancelButton: true,
    confirmButtonText: 'Mover',
    cancelButtonText: 'Cancelar',
  });

  if (confirm.isConfirmed) {
    const originalTask = { ...task };
    const quadrant = parseInt(confirm.value);
    
    // Optimistic UI update
    task.important = quadrant === 1 || quadrant === 2;
    task.urgent = quadrant === 1 || quadrant === 3;
    task.status = 'pending';
    task.due_date = null;
    updateTaskLocally(task);

    try {
      Swal.fire({
        title: 'Recuperando tarea...',
        allowOutsideClick: false,
        didOpen: () => {
          Swal.showLoading();
        },
      });
      await saveTask(task);
      Swal.close();
      Swal.fire('✅ Tarea recuperada', '', 'success');
    } catch (error) {
      // Revertir el cambio si la API falla
      updateTaskLocally(originalTask);
      Swal.close();
      Swal.fire('❌ Error al recuperar tarea', 'No se pudo recuperar la tarea. Inténtalo de nuevo.', 'error');
    }
  }
};
</script>

<style>
.swal2-html-container-custom {
  width: 100%;
  display: flex;
  flex-direction: column;
  gap: 1.25rem;
}

.swal2-container-custom {
  display: flex;
  flex-direction: column;
  gap: 0.5rem;
}
</style>
