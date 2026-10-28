<template>
    <div>
        <!-- Subtask list -->
        <div v-if="loading" class="text-center py-4">
            <div class="spinner-border spinner-border-sm text-teal" role="status"></div>
        </div>

        <div v-else class="d-flex flex-column" style="height: calc(100vh - 220px);">
            <div class="flex-grow-1 overflow-y-auto pe-1">
                <div v-if="subtasks.length === 0" class="text-center text-muted py-4" style="font-size:0.85rem;">
                    <i class="bi bi-diagram-2 d-block fs-3 mb-2 text-teal"></i>
                    No subtasks yet
                </div>

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
                        <button class="btn btn-sm py-0 px-1" @click.stop="selectedCommentTask = subtask" title="Comments">
                            <i class="bi bi-chat-left-text text-teal"></i>
                            <span v-if="subtask.comments_count > 0" class="badge bg-secondary bg-opacity-10 text-secondary ms-1" style="font-size:0.65rem;">
                                {{ subtask.comments_count }}
                            </span>
                        </button>
                        <button
                            class="btn btn-sm py-0 px-1"
                            @click.stop="selectedAttachmentTask = subtask"
                            title="Attachments"
                        >
                            <i class="bi bi-paperclip text-teal"></i>
                            <span v-if="subtask.attachments_count > 0" class="badge bg-secondary bg-opacity-10 text-secondary ms-1" style="font-size:0.65rem;">
                                {{ subtask.attachments_count }}
                            </span>
                        </button>
                    </div>
                </div>
            </div>
            
            <div class="border-top pt-2 mt-1">
                <button type="button" class="btn btn-sm btn-light w-100" @click="showCreateSubtaskModal = true">
                    <i class="bi bi-plus-lg me-1 text-teal"></i> Add Subtask
                </button>
            </div>

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

            <CommentModal
                v-if="selectedCommentTask"
                :task="selectedCommentTask"
                :parent-task-title="task.title"
                @close="selectedCommentTask = null"
                @updated="onSubtaskCountUpdated"
            />

            <AttachmentModal
                v-if="selectedAttachmentTask"
                :task="selectedAttachmentTask"
                :parent-task-title="task.title"
                @close="selectedAttachmentTask = null"
                @updated="onSubtaskCountUpdated"
            />

            <CreateSubtaskModal
                v-if="showCreateSubtaskModal"
                :task="task"
                :project="project"
                :taskTypes="taskTypes"
                @close="showCreateSubtaskModal = false"
                @created="onSubtaskCreated"
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
import CommentModal from '@/Pages/Task/Comment/Modal/CommentModal.vue';
import AttachmentModal from '@/Pages/Task/Attachment/Modal/AttachmentModal.vue';
import CreateSubtaskModal from '@/Pages/Task/Modal/CreateSubtaskModal.vue';

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
const selectedSubtask = ref<any | null>(null);
const selectedCommentTask = ref<any | null>(null);
const selectedAttachmentTask = ref<any | null>(null);
const showCreateSubtaskModal = ref(false);


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

const onSubtaskCreated = () => {
    showCreateSubtaskModal.value = false;
    fetchSubtasks();
    emit('updated');
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

const onSubtaskCountUpdated = () => {
    fetchSubtasks();
    emit('updated');
};
</script>