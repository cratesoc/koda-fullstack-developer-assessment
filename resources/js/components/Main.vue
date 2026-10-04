<script setup>
import { ref, onMounted } from 'vue';

const tasks = ref([]);
const user = ref(null);

const loading = ref(false);
const saving = ref(false);
const loggingOut = ref(false);
const error = ref('');
const modalOpen = ref(false);
const editing = ref(false);

const emptyTask = () => ({
    id: null,
    title: '',
    description: '',
    completed: false,
});

const form = ref(emptyTask());

const fetchUser = async () => {
    try {
        const response = await fetch('/api/user', {
            credentials: 'include',
            headers: {
                Accept: 'application/json',
            },
        });

        if (!response.ok) {
            window.location.href = '/login';
            return;
        }

        const data = await response.json();
        user.value = data.user;
    } catch {
        window.location.href = '/login';
    }
};

const fetchTasks = async () => {
    loading.value = true;
    error.value = '';

    try {
        const response = await fetch('/api/tasks', {
            credentials: 'include',
            headers: {
                Accept: 'application/json',
            },
        });

        if (response.status === 401) {
            window.location.href = '/login';
            return;
        }

        if (!response.ok) {
            throw new Error('Unable to load tasks');
        }

        tasks.value = await response.json();
    } catch (e) {
        error.value = e.message;
    } finally {
        loading.value = false;
    }
};

const logout = async () => {
    loggingOut.value = true;

    try {
        await fetch('/api/logout', {
            method: 'POST',
            credentials: 'include',
            headers: {
                Accept: 'application/json',
            },
        });

        window.location.href = '/login';
    } catch {
        error.value = 'Unable to logout.';
        loggingOut.value = false;
    }
};

const openTask = (task = null) => {
    form.value = task ? { ...task } : emptyTask();
    editing.value = !task;
    modalOpen.value = true;
};

const closeModal = () => {
    modalOpen.value = false;
    error.value = '';
};

const saveTask = async () => {
    saving.value = true;
    error.value = '';

    const isExisting = !!form.value.id;

    try {
        const response = await fetch(
            isExisting
                ? `/api/tasks/${form.value.id}`
                : '/api/tasks',
            {
                method: isExisting ? 'PUT' : 'POST',
                credentials: 'include',
                headers: {
                    'Content-Type': 'application/json',
                    Accept: 'application/json',
                },
                body: JSON.stringify({
                    title: form.value.title,
                    description: form.value.description,
                    completed: form.value.completed,
                }),
            }
        );

        if (response.status === 401) {
            window.location.href = '/login';
            return;
        }

        const data = await response.json();

        if (!response.ok) {
            throw new Error(
                Object.values(data.errors || {}).flat().join(' ')
                || data.message
                || 'Unable to save task'
            );
        }

        closeModal();
        await fetchTasks();
    } catch (e) {
        error.value = e.message;
    } finally {
        saving.value = false;
    }
};

const toggleDone = async (task) => {
    try {
        const response = await fetch(`/api/tasks/${task.id}`, {
            method: 'PUT',
            credentials: 'include',
            headers: {
                'Content-Type': 'application/json',
                Accept: 'application/json',
            },
            body: JSON.stringify({
                title: task.title,
                description: task.description,
                completed: !task.completed,
            }),
        });

        if (response.status === 401) {
            window.location.href = '/login';
            return;
        }

        if (!response.ok) {
            throw new Error('Unable to update task');
        }

        await fetchTasks();
    } catch (e) {
        error.value = e.message;
    }
};

const deleteTask = async () => {
    if (!form.value.id) return;

    if (!confirm('Are you sure you want to delete this task?')) {
        return;
    }

    try {
        const response = await fetch(
            `/api/tasks/${form.value.id}`,
            {
                method: 'DELETE',
                credentials: 'include',
                headers: {
                    Accept: 'application/json',
                },
            }
        );

        if (response.status === 401) {
            window.location.href = '/login';
            return;
        }

        if (!response.ok) {
            throw new Error('Unable to delete task');
        }

        closeModal();
        await fetchTasks();
    } catch (e) {
        error.value = e.message;
    }
};

onMounted(async () => {
    await fetchUser();

    if (user.value) {
        await fetchTasks();
    }
});
</script>

<template>
    <div class="min-h-screen bg-white text-gray-800">
        <div class="mx-auto max-w-5xl px-4 py-8">

            <!-- Header -->
            <div class="mb-6 flex items-center justify-between">
                <div>
                    <h1 class="text-2xl font-semibold">
                        Project Tasks
                    </h1>

                    <p class="mt-1 text-sm text-gray-500">
                        Manage your tasks
                    </p>
                </div>

                <!-- User -->
                <div class="flex items-center gap-4">
                    <div class="text-right">
                        <p class="text-sm font-medium text-gray-800">
                            {{ user?.name }}
                        </p>

                        <p class="text-xs text-gray-500">
                            {{ user?.email }}
                        </p>
                    </div>

                    <button
                        @click="logout"
                        :disabled="loggingOut"
                        class="rounded-md border border-gray-300
                               px-3 py-2 text-sm font-medium
                               text-gray-700 hover:bg-gray-50
                               disabled:opacity-50"
                    >
                        {{ loggingOut ? 'Logging out...' : 'Logout' }}
                    </button>
                </div>
            </div>

            <!-- Error -->
            <div
                v-if="error"
                class="mb-4 rounded bg-red-50 p-3
                       text-sm text-red-700"
            >
                {{ error }}
            </div>

            <!-- New Task -->
            <div class="mb-4 flex justify-end">
                <button
                    @click="openTask()"
                    class="rounded-md bg-slate-800 px-4 py-2
                           text-sm font-medium text-white
                           hover:bg-slate-700"
                >
                    + New Task
                </button>
            </div>

            <!-- Tasks -->
            <div class="overflow-x-auto border-y border-gray-200">
                <table
                    class="w-full min-w-[450px]
                           border-collapse text-sm"
                >
                    <thead>
                        <tr
                            class="h-11 border-b border-gray-200
                                text-left text-xs font-semibold
                                uppercase text-gray-500"
                        >
                            <th class="w-20 px-4">
                                Completed
                            </th>

                            <th class="w-48 px-3">
                                User
                            </th>

                            <th class="px-3">
                                Task Name
                            </th>
                        </tr>
                    </thead>

                    <tbody>
                        <tr v-if="loading">
                            <td
                                colspan="3"
                                class="py-10 text-center text-gray-500"
                            >
                                Loading tasks...
                            </td>
                        </tr>

                        <tr v-else-if="tasks.length === 0">
                            <td
                                colspan="3"
                                class="py-10 text-center text-gray-500"
                            >
                                No tasks yet
                            </td>
                        </tr>

                        <tr v-for="task in tasks"
                            :key="task.id"
                            @click="openTask(task)"
                            class="h-[54px] cursor-pointer border-b
                                border-gray-100 hover:bg-slate-50"
                            :class="{
                                'opacity-70': task.completed
                            }"
                        >
                            <td class="px-4"
                                @click.stop
                            >
                                <input
                                    type="checkbox"
                                    :checked="task.completed"
                                    @change="toggleDone(task)"
                                    :aria-label="`Complete ${task.title}`"
                                    class="h-5 w-5 cursor-pointer
                                        accent-slate-700"
                                />
                            </td>

                            <td class="px-3">
                                <span class="text-gray-600">
                                    {{ task.user?.name }}
                                </span>
                            </td>

                            <td class="px-3">
                                <span
                                    class="font-medium"
                                    :class="
                                        task.completed
                                            ? 'text-gray-400 line-through'
                                            : 'text-gray-700'
                                    "
                                >
                                    {{ task.title }}
                                </span>
                            </td>
                        </tr>
                    </tbody>
                </table>

                <button
                    @click="openTask()"
                    class="flex h-12 w-full items-center gap-3
                           px-4 text-gray-500 hover:bg-gray-50"
                >
                    <span
                        class="flex h-7 w-7 items-center
                               justify-center rounded-full
                               bg-gray-500 text-xl text-white"
                    >
                        +
                    </span>

                    New Task
                </button>
            </div>
        </div>

        <!-- Modal -->
        <div
            v-if="modalOpen"
            class="fixed inset-0 z-50 flex items-center
                   justify-center bg-black/40 p-4"
            @click.self="closeModal"
        >
            <div
                class="w-full max-w-lg rounded-xl bg-white
                       shadow-2xl"
                role="dialog"
                aria-modal="true"
            >
                <div
                    class="flex items-center justify-between
                           border-b border-gray-200 px-6 py-4"
                >
                    <h2 class="text-lg font-semibold">
                        {{
                            editing
                                ? (
                                    form.id
                                        ? 'Edit Task'
                                        : 'New Task'
                                )
                                : 'Task Details'
                        }}
                    </h2>

                    <button
                        @click="closeModal"
                        class="rounded p-1 text-xl
                               text-gray-400 hover:bg-gray-100"
                    >
                        ×
                    </button>
                </div>

                <form
                    @submit.prevent="saveTask"
                    class="space-y-4 p-6"
                >
                    <div>
                        <label
                            class="mb-1 block text-sm
                                   font-medium"
                        >
                            Task Name
                        </label>

                        <input
                            v-model="form.title"
                            :readonly="!editing"
                            required
                            maxlength="255"
                            placeholder="Enter task name"
                            class="w-full rounded-lg border
                                   border-gray-300 px-3 py-2
                                   outline-none
                                   focus:border-blue-500
                                   read-only:bg-gray-50"
                        />
                    </div>

                    <div>
                        <label
                            class="mb-1 block text-sm
                                   font-medium"
                        >
                            Description
                        </label>

                        <textarea
                            v-model="form.description"
                            :readonly="!editing"
                            rows="4"
                            placeholder="Enter description..."
                            class="w-full rounded-lg border
                                   border-gray-300 px-3 py-2
                                   outline-none
                                   focus:border-blue-500
                                   read-only:bg-gray-50"
                        ></textarea>
                    </div>

                    <div class="flex items-center gap-2">
                        <input
                            id="completed"
                            v-model="form.completed"
                            :disabled="!editing"
                            type="checkbox"
                            class="h-4 w-4 accent-slate-700"
                        />

                        <label
                            for="completed"
                            class="text-sm"
                        >
                            Completed
                        </label>
                    </div>

                    <div
                        v-if="error"
                        class="rounded bg-red-50 p-3
                               text-sm text-red-700"
                    >
                        {{ error }}
                    </div>

                    <div
                        class="flex items-center justify-between
                               border-t border-gray-100 pt-4"
                    >
                        <button
                            v-if="form.id && !editing"
                            type="button"
                            @click="deleteTask"
                            class="text-sm font-medium
                                   text-red-600
                                   hover:text-red-800"
                        >
                            Delete
                        </button>

                        <span v-else></span>

                        <div class="flex gap-2">
                            <button
                                type="button"
                                @click="
                                    editing
                                        ? (
                                            form.id
                                                ? editing = false
                                                : closeModal()
                                        )
                                        : closeModal()
                                "
                                class="rounded-lg border
                                       border-gray-300 px-4 py-2
                                       text-sm hover:bg-gray-50"
                            >
                                {{ editing ? 'Cancel' : 'Close' }}
                            </button>

                            <button
                                v-if="editing"
                                type="submit"
                                :disabled="saving"
                                class="rounded-lg bg-slate-800
                                       px-4 py-2 text-sm
                                       font-medium text-white
                                       hover:bg-slate-700
                                       disabled:opacity-50"
                            >
                                {{ saving ? 'Saving...' : 'Save' }}
                            </button>

                            <button
                                v-else
                                type="button"
                                @click="editing = true"
                                class="rounded-lg bg-slate-800
                                       px-4 py-2 text-sm
                                       font-medium text-white
                                       hover:bg-slate-700"
                            >
                                Edit
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</template>