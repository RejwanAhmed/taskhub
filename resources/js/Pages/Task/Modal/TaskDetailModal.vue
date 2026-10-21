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
                    <button type="button" class="btn-close" @click="emit('close')"></button>
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
                        <li class="nav-item">
                            <button class="nav-link" :class="{ active: activeTab === 'comments' }" @click="activeTab = 'comments'">
                                <i class="bi bi-chat-left-text me-1"></i> Comments
                            </button>
                        </li>
                        <li class="nav-item">
                            <button class="nav-link" :class="{ active: activeTab === 'attachments' }" @click="activeTab = 'attachments'">
                                <i class="bi bi-paperclip me-1"></i> Attachments
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

                    <!-- <TaskCommentTab
                        v-else-if="activeTab === 'comments'"
                        :task="task"
                    />

                    <TaskAttachmentTab
                        v-else-if="activeTab === 'attachments'"
                        :task="task"
                    /> -->

                </div>

            </div>
        </div>
    </div>
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
// import TaskCommentTab from '@/Pages/Task/Components/TaskCommentTab.vue';
// import TaskAttachmentTab from '@/Pages/Task/Components/TaskAttachmentTab.vue';

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

const activeTab = ref<'details' | 'subtasks' | 'comments' | 'attachments'>('details');
</script>