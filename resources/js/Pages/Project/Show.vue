<template>
    <Head :title="project.name" />
    <AuthenticatedLayout>
        <template #header>
            <div class="d-flex align-items-center gap-2">
                <h2 class="h5 fw-semibold mb-0">{{ project.name }}</h2>
                <span class="badge fw-normal" :class="statusColor(project.status)">
                    {{ statusLabel(project.status) }}
                </span>
            </div>
        </template>

        <div class="container-fluid">

            <!-- Action bar -->
            <div class="d-flex align-items-center justify-content-between mb-4">
                <small class="text-muted">
                    <i class="bi bi-calendar3 me-1"></i>
                    {{ project.start_date || '--' }} → {{ project.end_date || '--' }}
                    <span class="ms-3" :style="{ color: progressColor(project) }">
                        {{ progressPct(project) }}% complete
                    </span>
                </small>
                <div class="d-flex gap-2">
                    <button class="btn bg-teal text-white" @click="showMembersModal = true">
                        <i class="bi bi-person-plus me-1"></i> Members
                    </button>
                    <button class="btn bg-teal text-white" @click="showCreateTaskModal = true">
                        <i class="bi bi-plus-lg me-1"></i> New Task
                    </button>
                </div>
            </div>

            <!-- Toolbar -->
            <div class="d-flex align-items-center gap-2 mb-3 flex-wrap">
                <div class="input-group" style="max-width:260px;">
                    <span class="input-group-text bg-white border-end-0">
                        <i class="bi bi-search text-muted" style="font-size:0.8rem;"></i>
                    </span>
                    <input v-model="search" type="text" class="form-control border-start-0" placeholder="Search tasks..."/>
                </div>
                <select v-model="priorityFilter" class="form-select" style="width:auto;">
                    <option value="">All priorities</option>
                    <option value="low">Low</option>
                    <option value="medium">Medium</option>
                    <option value="high">High</option>
                    <option value="urgent">Urgent</option>
                </select>
            </div>

            <!-- Kanban Board -->
            <KanbanBoard
                :columns="columns"
                :tasks="filteredTasks"
                :loading="loading"
                @openTask="openTaskModal"
                @taskDeleted="fetchTasks"
            />

            <TaskDetailModal
                v-if="selectedTask"
                :task="selectedTask"
                :project="project"
                :tags="tags"
                :taskTypes="taskTypes"
                @close="selectedTask = null"
                @updated="fetchTasks"
            />

        </div>

        <!-- Modals -->
        <ManageProjectMembers
            v-if="showMembersModal"
            :showMemebrsModal="showMembersModal"
            :project="project"
            :activeUsers="activeUsers"
            @close="showMembersModal = false"
        />

        <CreateTaskModal
            v-if="showCreateTaskModal"
            :project="project"
            :tags="tags"
            :taskTypes="taskTypes"
            @close="showCreateTaskModal = false"
            @created="fetchTasks"
        />

    </AuthenticatedLayout>
</template>

<script setup lang="ts">
import { ref, computed, onMounted } from 'vue';
import { Head } from '@inertiajs/vue3';
import axios from 'axios';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import ManageProjectMembers from '@/Pages/Project/Modal/ManageProjectMembers.vue';
import CreateTaskModal from '@/Pages/Task/Modal/CreateTaskModal.vue';
import TaskDetailModal from '@/Pages/Task/Modal/TaskDetailModal.vue';
import KanbanBoard from '@/Components/Kanban/KanbanBoard.vue';
import { statusColor, statusLabel, progressPct, progressColor } from '@/Utils/projectStatus';
import { Project } from '@/types/interfaces/project';
import { User } from '@/types/interfaces/user';
import { Tag } from '@/types/interfaces/tag';
import { TaskType } from '@/types/interfaces/taskType';
import { Task } from '@/types/interfaces/task';

const props = defineProps<{
    project: Project;
    activeUsers: User[];
    tags: Tag[];
    taskTypes: TaskType[];
}>();

// ── State ──────────────────────────────────────────────────────────────────────
const tasks               = ref<Record<string, any>[]>([]);
const loading             = ref(true);
const search              = ref('');
const priorityFilter      = ref('');
const showMembersModal    = ref(false);
const showCreateTaskModal = ref(false);
const selectedTask    = ref<Task | null>(null);

// ── Kanban columns ─────────────────────────────────────────────────────────────
const columns = [
    { status: 'to_do',       label: 'To Do',      color: '#6B7280' },
    { status: 'in_progress', label: 'In Progress', color: '#3B82F6' },
    { status: 'in_review',   label: 'In Review',   color: '#F59E0B' },
    { status: 'on_hold',     label: 'On Hold',     color: '#EF4444' },
    { status: 'completed',   label: 'Completed',   color: '#10B981' },
];

// ── Fetch tasks ────────────────────────────────────────────────────────────────
const fetchTasks = async () => {
    loading.value = true;
    try {
        const { data } = await axios.get(route('projects.tasks.index', props.project.id));
        tasks.value = data.tasks;

        if (selectedTask.value) {
            const updated = data.tasks.find((t: any) => t.id === selectedTask.value!.id);
            if (updated) selectedTask.value = updated;
        }
    } finally {
        loading.value = false;
    }
};

const openTaskModal = (task: Task) => {
    selectedTask.value = task;
};

onMounted(fetchTasks);

// ── Filters ────────────────────────────────────────────────────────────────────
const filteredTasks = computed(() => {
    let list = tasks.value;
    const q = search.value.toLowerCase().trim();
    if (q) list = list.filter(t => t.title.toLowerCase().includes(q));
    if (priorityFilter.value) list = list.filter(t => t.priority === priorityFilter.value);
    return list;
});

// ── Actions ────────────────────────────────────────────────────────────────────
const editTask = (task: any) => {
    // Step 3 — wire up edit modal
};
</script>