# 🧠 Priorisen

**Lo que es importante rara vez es urgente y lo que es urgente rara vez es importante.**

Priorisen es una aplicación de gestión de tareas basada en la **Matriz de Eisenhower**. Su objetivo es ayudarte a priorizar de forma inteligente, diferenciando entre lo que realmente importa y lo que solo parece urgente.

---

## 🚀 Tecnologías

- ⚙️ **Backend:** [Laravel 11](https://laravel.com/)
- 🌐 **Frontend:** [Vue.js 3](https://vuejs.org/) con [Vite](https://vitejs.dev/)
- 🧪 **Base de datos:** MySQL o SQLite (a elección)
- 🔐 **Autenticación:** Laravel Sanctum (opcional)

---

## 🧩 Estructura del Proyecto

### 📁 Backend (Laravel)

- **Modelos principales:**
    - `User` (con autenticación opcional)
    - `Task`
        - Título
        - Descripción
        - Importante (booleano)
        - Urgente (booleano)
        - Fecha de vencimiento (opcional)
        - Estado (`pendiente`, `en progreso`, `completado`)
        - Cuadrante (derivado automáticamente)

- **API RESTful:**
    - `GET /api/tasks` – Listar tareas
    - `POST /api/tasks` – Crear tarea
    - `PUT /api/tasks/{id}` – Actualizar tarea
    - `DELETE /api/tasks/{id}` – Eliminar tarea

> El cuadrante se calcula automáticamente combinando los valores de "importante" y "urgente".

### 📁 Frontend (Vue.js)

- Interfaz limpia basada en los 4 cuadrantes de la matriz.
- Drag & drop entre cuadrantes (opcional).
- Crear, editar y eliminar tareas.
- Filtros por estado y fechas.

---

## 🛠️ Instalación

### 🔧 Backend (Laravel)

```bash
git clone https://github.com/tu-usuario/priorisen.git
cd priorisen/backend
composer install
cp .env.example .env
php artisan key:generate
php artisan migrate
php artisan serve
