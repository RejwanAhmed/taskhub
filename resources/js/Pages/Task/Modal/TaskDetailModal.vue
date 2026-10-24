<template>
    <div class="modal d-block modal-background">
        <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
            <div class="modal-content border-0 rounded-4">
                <!-- Header -->
                <div class="modal-header border-bottom-0 pb-0">
                    <div>
                        <h6 class="modal-title fw-bold mb-1">
                            <i class="bi bi-check2-square me-2 text-teal"></i>
                            {{ task.title }}
                        </h6>
                        <div class="d-flex align-items-center gap-2">
                            <span  v-if="task.task_type" class="badge fw-normal"
                                :style="{
                                    background: task.task_type?.color + '22',
                                    color: task.task_type?.color,
                                    border: '1px solid ' + task.task_type?.color + '44'
                                }" >
                                {{ task.task_type.name }}
                            </span>
                            <span class="badge fw-normal" :class="priorityClass(task.priority)">
                                {{ priorityLabel(task.priority) }}
                            </span>
                        </div>
                    </div>
                    <!-- <button type="button" class="btn-close" @click="emit('close')"></button> -->
                    <div class="d-flex align-items-center gap-2 ms-auto">
                        <button class="btn btn-sm btn-light" @click="showCommentModal = true">
                            <i class="bi bi-chat-left-text me-1 text-teal"></i> Comments
                        </button>
                        <button type="button" class="btn-close" @click="emit('close')"></button>
                    </div>
                </div>

                <!-- Tabs -->
                <div class="modal-body pt-2">
                    <ul class="nav nav-tabs mb-3" v-if="!task.parent_task_id">
                        <li class="nav-item">
                            <button class="nav-link" :class="{ active: activeTab === 'details' }" @click="activeTab = 'details'">
                                <i class="bi bi-pencil-square me-1"></i> Details
                            </button>
                        </li>
                        <li class="nav-item">
                            <button class="nav-link" :class="{ active: activeTab === 'subtasks' }" @click="activeTab = 'subtasks'">
                                <i class="bi bi-diagram-2 me-1"></i> Subtasks
                                <span v-if="task.sub_tasks_count > 0" class="badge bg-secondary bg-opacity-10 text-secondary ms-1">
                                    {{ task.sub_tasks_count }}
                                </span>
                            </button>
                        </li>
                    </ul>

                    <!-- Tab content -->
                    <TaskDetailTab
                        v-if="activeTab === 'details'"
                        :task="task"
                        :project="project"
                        :tags="tags"
                        :taskTypes="taskTypes"
                        :use-axios="useAxios"
                        :parent-task-id="parentTaskId"
                        @updated="emit('updated')"
                    />

                    <TaskSubtaskTab
                        v-else-if="activeTab === 'subtasks'"
                        :task="task"
                        :project="project"
                        :taskTypes="taskTypes"
                        @updated="emit('updated')"
                    />
                </div>
            </div>
        </div>
    </div>
    <CommentModal
        v-if="showCommentModal"
        :task="task"
        @close="showCommentModal = false"
    />
</template>

<script setup lang="ts">
import { ref } from 'vue';
import { Task } from '@/types/interfaces/task';
import { Project } from '@/types/interfaces/project';
import { Tag } from '@/types/interfaces/tag';
import { TaskType } from '@/types/interfaces/taskType';
import { priorityClass, priorityLabel } from '@/Utils/taskHelpers';
import TaskDetailTab from '@/Pages/Task/Tab/TaskDetailTab.vue';
import TaskSubtaskTab from '@/Pages/Task/Tab/TaskSubtaskTab.vue';
import CommentModal from '@/Pages/Task/Comment/Modal/CommentModal.vue';

const props = defineProps<{
    task: Task;
    project: Project;
    tags: Tag[];
    taskTypes: TaskType[];
    useAxios?: boolean;
    parentTaskId?: number;
}>();

const emit = defineEmits<{
    close: [];
    updated: [];
}>();

const activeTab = ref<'details' | 'subtasks'>('details');
const showCommentModal = ref(false);
</script>