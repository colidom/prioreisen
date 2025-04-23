<template>
	<div class="min-h-screen flex flex-col">
		<main class="flex-1 bg-gray-100 p-6">
			<div class="container mx-auto p-6">
				<div class="flex justify-center my-4">
					<button @click="openNewTaskModal"
						class="bg-indigo-600 hover:bg-indigo-700 text-white font-semibold py-2 px-4 rounded-lg shadow-md transition duration-200">
						+ Crear nueva tarea
					</button>
				</div>

				<!-- Matriz Eisenhower -->
				<div class="mt-10">
					<div class="grid grid-cols-[auto,1fr,1fr] gap-4">
						<div></div>
						<div class="text-center text-lg font-semibold text-gray-700">Urgente</div>
						<div class="text-center text-lg font-semibold text-gray-700">No urgente</div>

						<div class="flex items-center justify-center text-lg font-semibold text-gray-700 mb-2"
							style="writing-mode: vertical-lr; transform: rotate(180deg)">
							Importante
						</div>

						<!-- Cuadrante 1 -->
						<div class="bg-blue-500 p-4 rounded-lg shadow-lg" @drop="moveTaskToQuadrant(1)"
							@dragover.prevent>
							<h2 class="text-xl font-semibold text-white text-center">HACER YA</h2>
							<hr class="my-2 border-white" />
							<TaskCard v-for="task in quadrant1Tasks" :key="task.id" :task="task" color="blue"
								@drag-task="dragTask" @edit-task="openTaskModal" @complete-task="markAsCompleted"
								@delete-task="deleteTask" />
						</div>

						<!-- Cuadrante 2 -->
						<div class="bg-green-500 p-4 rounded-lg shadow-lg" @drop="moveTaskToQuadrant(2)"
							@dragover.prevent>
							<h2 class="text-xl font-semibold text-white text-center">PLANIFICAR</h2>
							<hr class="my-2 border-white" />
							<TaskCard v-for="task in quadrant2Tasks" :key="task.id" :task="task" color="green"
								@drag-task="dragTask" @edit-task="openTaskModal" @complete-task="markAsCompleted"
								@delete-task="deleteTask" />
						</div>

						<div class="flex items-center justify-center text-lg font-semibold text-gray-700 mb-2"
							style="writing-mode: vertical-lr; transform: rotate(180deg)">
							No importante
						</div>

						<!-- Cuadrante 3 -->
						<div class="bg-yellow-500 p-4 rounded-lg shadow-lg" @drop="moveTaskToQuadrant(3)"
							@dragover.prevent>
							<h2 class="text-xl font-semibold text-white text-center">DELEGAR</h2>
							<hr class="my-2 border-white" />
							<TaskCard v-for="task in quadrant3Tasks" :key="task.id" :task="task" color="yellow"
								@drag-task="dragTask" @edit-task="openTaskModal" @complete-task="markAsCompleted"
								@delete-task="deleteTask" />
						</div>

						<!-- Cuadrante 4 -->
						<div class="bg-gray-500 p-4 rounded-lg shadow-lg" @drop="moveTaskToQuadrant(4)"
							@dragover.prevent>
							<h2 class="text-xl font-semibold text-white text-center">ELIMINAR</h2>
							<hr class="my-2 border-white" />
							<TaskCard v-for="task in quadrant4Tasks" :key="task.id" :task="task" color="gray"
								@drag-task="dragTask" @edit-task="openTaskModal" @complete-task="markAsCompleted"
								@delete-task="deleteTask" />
						</div>
					</div>
				</div>

				<!-- Lista de tareas finalizadas -->
				<div class="mt-12">
					<hr class="my-2 border-black" />
					<h2 class="text-center text-xl font-semibold text-gray-700 mb-4 mt-4">
						TERMINADAS
					</h2>
					<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
						<div v-for="task in completedTasks" :key="task.id"
							class="bg-gray-200 p-4 rounded-lg shadow border border-gray-300 text-gray-500 hover:bg-gray-300 cursor-pointer"
							@click="recoverCompletedTask(task)">
							<div class="text-gray-800 font-medium">{{ task.title }}</div>
							<div class="text-gray-500 text-sm">{{ task.description }}</div>
						</div>
					</div>
				</div>
			</div>
		</main>
	</div>
</template>

<script setup>
import { ref, computed, onMounted } from "vue";
import Swal from "sweetalert2";
import TaskCard from "../components/TaskCard.vue";

const selectedTask = ref(null);
const isEditing = ref(false);
const isCreating = ref(false);
const showForm = ref(false);

const tasks = ref([]);
const draggingTask = ref(null);

const quadrant1Tasks = computed(() =>
	tasks.value.filter(
		(task) => task.important && task.urgent && task.status !== "completed"
	)
);
const quadrant2Tasks = computed(() =>
	tasks.value.filter(
		(task) => task.important && !task.urgent && task.status !== "completed"
	)
);
const quadrant3Tasks = computed(() =>
	tasks.value.filter(
		(task) => !task.important && task.urgent && task.status !== "completed"
	)
);
const quadrant4Tasks = computed(() =>
	tasks.value.filter(
		(task) => !task.important && !task.urgent && task.status !== "completed"
	)
);
const completedTasks = computed(() =>
	tasks.value.filter((task) => task.status === "completed")
);

const fetchTasks = async () => {
	try {
		const res = await fetch("http://localhost:8000/api/tasks");
		tasks.value = await res.json();
	} catch (e) {
		console.error("Error fetching tasks:", e);
	}
};

onMounted(fetchTasks);

const dragTask = (task) => {
	draggingTask.value = task;
};

const moveTaskToQuadrant = (quadrant) => {
	if (!draggingTask.value) return;
	draggingTask.value.important = quadrant === 1 || quadrant === 2;
	draggingTask.value.urgent = quadrant === 1 || quadrant === 3;
	saveTask(draggingTask.value);
	draggingTask.value = null;
};

const saveTask = async (task) => {
	const url = `http://localhost:8000/api/tasks/${task.id}`;
	await fetch(url, {
		method: "PATCH",
		headers: { "Content-Type": "application/json" },
		body: JSON.stringify(task),
	});
	fetchTasks();
};

const openNewTaskModal = async () => {
	const { value: formValues } = await Swal.fire({
		title: "Nueva tarea",
		width: "700px",
		html: `
            <div class="swal2-html-container-custom">
                <div class="swal2-container-custom">
                <label for="swal-input1"><strong>Título</strong></label>
                <input id="swal-input1" class="swal2-input" placeholder="Título">
                </div>
                <div class="swal2-container-custom">
                <label for="swal-input2"><strong>Descripción</strong></label>
                <textarea id="swal-input2" class="swal2-textarea" placeholder="Descripción" style="height: 130px;"></textarea>
                </div>
            </div>
            `,
		customClass: {
			htmlContainer: "swal2-html-container-custom",
		},
		focusConfirm: false,
		showCancelButton: true,
		confirmButtonText: "➕ Crear",
		cancelButtonText: "Cancelar",
		preConfirm: () => {
			const title = document.getElementById("swal-input1").value.trim();
			const description = document.getElementById("swal-input2").value.trim();
			if (!title || !description) {
				Swal.showValidationMessage("Por favor, completa todos los campos");
				return false;
			}
			return { title, description };
		},
	});

	if (formValues) {
		const newTask = {
			title: formValues.title,
			description: formValues.description,
			important: true, // Por defecto: cuadrante 1
			urgent: true,
			status: "pending",
		};

		await fetch("http://localhost:8000/api/tasks", {
			method: "POST",
			headers: { "Content-Type": "application/json" },
			body: JSON.stringify(newTask),
		});

		fetchTasks();
	}
};

const openTaskModal = async (task) => {
	const { value: formValues } = await Swal.fire({
		title: "Editar tarea",
		width: "700px",
		html: `
            <div class="swal2-html-container-custom">
                <div class="swal2-container-custom">
                <label for="swal-input1"><strong>Título</strong></label>
                <input id="swal-input1" class="swal2-input" value="${task.title}" placeholder="Título">
                </div>
                <div class="swal2-container-custom">
                <label for="swal-input2"><strong>Descripción</strong></label>
                <textarea id="swal-input2" class="swal2-textarea" placeholder="Descripción" style="height: 130px;">${task.description}</textarea>
                </div>
            </div>
            `,
		customClass: {
			htmlContainer: "swal2-html-container-custom",
		},
		focusConfirm: false,
		showCancelButton: true,
		confirmButtonText: "💾 Guardar",
		cancelButtonText: "❌ Cancelar",
		preConfirm: () => {
			const title = document.getElementById("swal-input1").value.trim();
			const description = document.getElementById("swal-input2").value.trim();
			if (!title || !description) {
				Swal.showValidationMessage("Por favor, completa todos los campos");
				return false;
			}
			return { title, description };
		},
	});

	if (formValues) {
		task.title = formValues.title;
		task.description = formValues.description;
		await saveTask(task);
	}
};

const markAsCompleted = async (task) => {
	task.status = "completed";
	await saveTask(task);
};

const deleteTask = async (task) => {
	const confirm = await Swal.fire({
		title: "¿Eliminar tarea?",
		text: "Esta acción no se puede deshacer.",
		icon: "warning",
		showCancelButton: true,
		confirmButtonText: "Sí, eliminar",
		cancelButtonText: "Cancelar",
	});

	if (confirm.isConfirmed) {
		await fetch(`http://localhost:8000/api/tasks/${task.id}`, { method: "DELETE" });
		tasks.value = tasks.value.filter((t) => t.id !== task.id);
	}
};

const recoverCompletedTask = async (task) => {
	const confirm = await Swal.fire({
		title: "¿Recuperar tarea?",
		text: "Selecciona el cuadrante al que deseas moverla.",
		input: "select",
		inputOptions: {
			1: "🟦 Hacer ya",
			2: "🟩 Planificar",
			3: "🟨 Delegar",
			4: "⬜ Eliminar",
		},
		showCancelButton: true,
		confirmButtonText: "Mover",
		cancelButtonText: "Cancelar",
	});

	if (confirm.isConfirmed) {
		const quadrant = parseInt(confirm.value);
		task.important = quadrant === 1 || quadrant === 2;
		task.urgent = quadrant === 1 || quadrant === 3;
		task.status = "pending";
		await saveTask(task);
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
