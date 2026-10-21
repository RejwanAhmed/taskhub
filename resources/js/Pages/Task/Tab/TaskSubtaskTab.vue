<template>
    <div>
        <!-- Subtask list -->
        <div v-if="loading" class="text-center py-4">
            <div class="spinner-border spinner-border-sm text-teal" role="status"></div>
        </div>

        <div v-else>
            <div v-if="subtasks.length === 0 && !showForm" class="text-center text-muted py-4" style="font-size:0.85rem;">
                <i class="bi bi-diagram-2 d-block fs-3 mb-2 text-teal"></i>
                No subtasks yet
            </div>

            <!-- Subtask items -->
            <div class="d-flex flex-column gap-2 mb-3">
                <div v-for="subtask in subtasks" :key="subtask.id" class="subtask-item d-flex align-items-center gap-3 p-2 rounded-3">
                    <!-- Status indicator -->
                    <span style="cursor:pointer;" @click="selectedSubtask = subtask" class="subtask-dot flex-shrink-0" :style="{ background: statusColor(subtask.status) }" :title="subtask.status"></span>

                    <!-- Title -->
                    <span style="cursor:pointer;" @click="selectedSubtask = subtask" class="small flex-grow-1 fw-semibold">{{ subtask.title }}</span>

                    <!-- Priority -->
                    <span class="badge fw-normal" :class="priorityClass(subtask.priority)">
                        {{ priorityLabel(subtask.priority) }}
                    </span>

                    <!-- Assignee -->
                    <div v-if="subtask.assignee" class="avatar-circle text-white flex-shrink-0" style="width:22px;height:22px;font-size:0.6rem;" :style="{ background: avatarColor(subtask.assignee.name) }" :title="subtask.assignee.name">
                        {{ initials(subtask.assignee.name) }}
                    </div>

                    <!-- Due date -->
                    <small v-if="subtask.due_date" class="text-muted flex-shrink-0" style="font-size:0.7rem;">
                        <i class="bi bi-calendar3 me-1"></i>{{ formatDate(subtask.due_date) }}
                    </small>

                    <!-- Delete -->
                    <DeleteConfirmationButton
                        confirm-route="projects.tasks.subtasks.destroy"
                        :obj="subtask"
                        :route-params="{ project: project.id, task: task.id, subtask: subtask.id }"
                        :delete-content="subtask.title"
                        :icon-only="true"
                        :use-axios="true"
                        @deleted="onSubtaskDeleted"
                    />
                </div>
            </div>

            <!-- Add subtask form -->
            <div v-if="showForm" class="card border-0 bg-light rounded-3 p-3 mb-3">
                <div class="row g-2">

                    <!-- Title -->
                    <div class="col-12">
                        <label class="form-label fw-semibold small">
                            Title <span class="text-danger">*</span>
                        </label>
                        <input v-model="form.title" type="text" class="form-control form-control-sm" placeholder="Subtask title" autofocus/>
                        <span v-if="formErrors.title" class="text-danger" style="font-size:0.75rem;">
                            {{ formErrors.title }}
                        </span>
                    </div>

                    <!-- Description -->
                    <div class="col-12">
                        <label class="form-label fw-semibold small">Description</label>
                        <RichTextEditor v-model="form.description" placeholder="Subtask description..." />
                    </div>

                    <!-- Task Type + Priority -->
                    <div class="col-6">
                        <label class="form-label fw-semibold small">Task Type</label>
                        <select v-model="form.task_type_id" class="form-select form-select-sm">
                            <option :value="null">— None —</option>
                            <option v-for="type in taskTypes" :key="type.id" :value="type.id">
                                {{ type.name }}
                            </option>
                        </select>
                    </div>

                    <div class="col-6">
                        <label class="form-label fw-semibold small">Priority</label>
                        <select v-model="form.priority" class="form-select form-select-sm">
                            <option value="low">Low</option>
                            <option value="medium">Medium</option>
                            <option value="high">High</option>
                            <option value="urgent">Urgent</option>
                        </select>
                    </div>

                    <!-- Assigned To + Due Date -->
                    <div class="col-6">
                        <label class="form-label fw-semibold small">Assign To</label>
                        <select v-model="form.assigned_to" class="form-select form-select-sm">
                            <option :value="null">— Unassigned —</option>
                            <option v-for="member in project.members" :key="member.id" :value="member.id">
                                {{ member.name }}
                            </option>
                        </select>
                    </div>

                    <div class="col-6">
                        <label class="form-label fw-semibold small">Due Date</label>
                        <input v-model="form.due_date" type="date" class="form-control form-control-sm" />
                    </div>

                    <!-- Estimated Hours -->
                    <div class="col-6">
                        <label class="form-label fw-semibold small">Estimated Hours</label>
                        <input v-model="form.estimated_hours" type="number" class="form-control form-control-sm" placeholder="e.g. 2" min="0" step="0.5"/>
                    </div>

                    <!-- Actions -->
                    <div class="col-12 d-flex justify-content-end gap-2 mt-1">
                        <button type="button" class="btn btn-sm btn-light" @click="cancelForm">
                            Cancel
                        </button>
                        <SubmitButton label="Add Subtask" :processing="processing" @click="submit" />
                    </div>

                </div>
            </div>

            <!-- Add subtask button -->
            <button v-if="!showForm" type="button" class="btn btn-sm btn-light w-100" @click="showForm = true">
                <i class="bi bi-plus-lg me-1 text-teal"></i> Add Subtask
            </button>

            <TaskDetailModal
                v-if="selectedSubtask"
                :task="selectedSubtask"
                :project="project"
                :tags="[]"
                :taskTypes="taskTypes"
                :use-axios="true"
                :parent-task-id="task.id"
                @close="selectedSubtask = null"
                @updated="onSubtaskUpdated"
            />
        </div>
    </div>
</template>

<script setup lang="ts">
import { ref, reactive, onMounted } from 'vue';
import axios from 'axios';
import { toast } from '@/Utils/toast';
import { handleAxiosError } from '@/Utils/handleAxiosError';
import SubmitButton from '@/Components/Button/SubmitButton.vue';
import DeleteConfirmationButton from '@/Components/Button/DeleteConfirmationButton.vue';
import RichTextEditor from '@/Components/Editor/RichTextEditor.vue';
import TaskDetailModal from '@/Pages/Task/Modal/TaskDetailModal.vue';
import { Task } from '@/types/interfaces/task';
import { Project } from '@/types/interfaces/project';
import { TaskType } from '@/types/interfaces/taskType';
import { priorityClass, priorityLabel, avatarColor, initials, formatDate } from '@/Utils/taskHelpers';

const props = defineProps<{
    task: Task;
    project: Project;
    taskTypes: TaskType[];
}>();

const emit = defineEmits<{
    updated: [];
}>();

// ── State ──────────────────────────────────────────────────────────────────────
const subtasks      = ref<any[]>([]);
const loading       = ref(true);
const showForm      = ref(false);
const processing    = ref(false);
const selectedSubtask = ref<any | null>(null);
const formErrors    = reactive<Record<string, string>>({});


// ── Status dot color ───────────────────────────────────────────────────────────
const statusColor = (status: string) => ({
    to_do:       '#6B7280',
    in_progress: '#3B82F6',
    in_review:   '#F59E0B',
    on_hold:     '#EF4444',
    completed:   '#10B981',
}[status] ?? '#6B7280');

// ── Fetch subtasks ─────────────────────────────────────────────────────────────
const fetchSubtasks = async () => {
    loading.value = true;
    try {
        const { data } = await axios.get(route('projects.tasks.subtasks.index', [props.project.id, props.task.id]));
        subtasks.value = data.subtasks;
    } finally {
        loading.value = false;
    }
};

onMounted(fetchSubtasks);

// ── Form ───────────────────────────────────────────────────────────────────────
const form = reactive({
    title:           '',
    description:     '',
    priority:        'medium' as 'low' | 'medium' | 'high' | 'urgent',
    task_type_id:    null as number | null,
    assigned_to:     null as number | null,
    due_date:        '',
    estimated_hours: null as number | null,
});

const cancelForm = () => {
     Object.assign(form, {
        title: '', 
        description: '', 
        priority: 'medium',
        task_type_id: null, 
        assigned_to: null,
        due_date: '', 
        estimated_hours: null,
    });
    Object.keys(formErrors).forEach(k => delete formErrors[k]);
    showForm.value = false;
};

const submit = async () => {
    processing.value = true;
    Object.keys(formErrors).forEach(k => delete formErrors[k]);

    try {
        const response = await axios.post(route('projects.tasks.subtasks.store', [props.project.id, props.task.id]),form);
        toast.success(response.data.success);
        cancelForm();
        fetchSubtasks();
        emit('updated');
    } catch (e: any) {
        handleAxiosError(e, formErrors);
    } finally {
        processing.value = false;
    }
};

const onSubtaskDeleted = () => {
    fetchSubtasks();
    emit('updated');
};

const onSubtaskUpdated = () => {
    selectedSubtask.value = null;
    fetchSubtasks();
    emit('updated');
};
</script>