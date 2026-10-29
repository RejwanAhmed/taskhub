<template>
    <div class="kanban-board d-flex gap-3 pb-4">
        <div v-for="col in columns" :key="col.status" class="kanban-column flex-shrink-0">
            <!-- Column header -->
            <div class="d-flex align-items-center gap-2 mb-2 px-1">
                <span class="kanban-col-dot" :style="{ background: col.color }"></span>
                <span class="fw-semibold small">{{ col.label }}</span>
                <span class="badge bg-secondary bg-opacity-10 text-secondary">
                    {{ columnTasks[col.status]?.length ?? 0 }}
                </span>
            </div>

            <!-- Cards container -->
            <VueDraggable v-model="columnTasks[col.status]" 
                group="tasks"
                class="kanban-cards d-flex flex-column gap-2"
                :animation="200"
                item-key="id"
                :data-status="col.status"
                @end="(evt) => onDragEnd(evt)"
            >
                <!-- Skeleton loader -->
                <template v-if="loading">
                    <div v-for="n in 2" :key="n" class="kanban-card card border-0 shadow-sm p-3">
                        <div class="skeleton skeleton-text mb-2" style="width:70%;"></div>
                        <div class="skeleton skeleton-text mb-3" style="width:45%;"></div>
                        <div class="d-flex gap-2">
                            <div class="skeleton skeleton-badge"></div>
                            <div class="skeleton skeleton-badge"></div>
                        </div>
                    </div>
                </template>

                <!-- Task cards -->
                <template v-else>
                    <div v-for="task in columnTasks[col.status]" :key="task.id" :data-id="task.id" class="kanban-card card border-0 shadow-sm p-3">
                        <!-- Top row: task type + three dot menu -->
                        <div class="d-flex align-items-center justify-content-between mb-2">
                            <span v-if="task.task_type" class="badge fw-normal small"
                                :style="{
                                    background: task.task_type.color + '22',
                                    color: task.task_type.color,
                                    border: '1px solid ' + task.task_type.color + '44'
                                }"
                            >
                                <i v-if="task.task_type.icon" :class="['bi', task.task_type.icon]" class="me-1"></i>
                                {{ task.task_type.name }}
                            </span>
                            <span v-else></span>

                            <!-- Three dot menu -->
                            <div class="dropdown">
                                <button class="btn btn-sm btn-light py-0 px-1" data-bs-toggle="dropdown" @click.stop>
                                    <i class="bi bi-three-dots-vertical"></i>
                                </button>
                                <ul class="dropdown-menu dropdown-menu-end shadow-sm border-0">
                                    <li>
                                        <button class="dropdown-item small" @click.stop="emit('openTask', task)">
                                            <i class="bi bi-pencil me-2 text-teal"></i> Edit
                                        </button>
                                    </li>
                                    <li><hr class="dropdown-divider" /></li>
                                    <li>
                                        <DeleteConfirmationButton
                                            confirm-route="projects.tasks.destroy"
                                            :obj="task"
                                            :route-params="{ project: task.project_id, task: task.id }"
                                            :use-axios="true"
                                            :delete-content="task.title"
                                            @deleted="emit('taskDeleted')"
                                            class="dropdown-item small"/>
                                    </li>
                                </ul>
                            </div>
                        </div>

                        <!-- Title -->
                        <p class="fw-semibold small mb-2" style="line-height:1.4;">
                            {{ task.title }}
                        </p>

                        <!-- Tags -->
                        <div v-if="task.tags?.length" class="d-flex flex-wrap gap-1 mb-2">
                            <span v-for="tag in task.tags.slice(0, 3)" :key="tag.id" class="badge fw-normal" style="font-size:0.65rem;"
                                :style="{
                                    background: tag.color + '22',
                                    color: tag.color,
                                    border: '1px solid ' + tag.color + '44'
                                }"
                            >
                                {{ tag.name }}
                            </span>
                            <span v-if="task.tags.length > 3" class="badge bg-secondary bg-opacity-10 text-secondary" style="font-size:0.65rem;"
                            >
                                +{{ task.tags.length - 3 }}
                            </span>
                        </div>

                        <!-- Footer: assignee + priority + due date -->
                        <div class="d-flex align-items-center justify-content-between mt-auto pt-1 border-top">
                            <div class="d-flex align-items-center gap-2">
                                <div v-if="task.assignee" class="avatar-circle text-white" style="width:24px;height:24px;font-size:0.65rem;flex-shrink:0;" :style="{ background: avatarColor(task.assignee.name) }" :title="task.assignee.name">
                                    {{ initials(task.assignee.name) }}
                                </div>
                                <span v-else class="text-muted" style="font-size:0.7rem;">Unassigned</span>

                                <span v-if="task.sub_tasks_count > 0" class="text-muted" style="font-size:0.7rem;">
                                    <i class="bi bi-diagram-2 me-1"></i>{{ task.sub_tasks_count }}
                                </span>
                            </div>

                            <div class="d-flex align-items-center gap-2">
                                <span class="badge fw-normal" :class="priorityClass(task.priority)">
                                    {{ priorityLabel(task.priority) }}
                                </span>
                                <small v-if="task.due_date" class="text-muted" :class="{ 'text-danger': isOverdue(task) }" style="font-size:0.7rem;">
                                    <i class="bi bi-calendar3 me-1"></i>{{ formatDate(task.due_date) }}
                                </small>
                            </div>
                        </div>
                    </div>

                    <!-- Empty column -->
                    <div v-if="columnTasks[col.status]?.length === 0" class="kanban-empty text-center text-muted" style="font-size:0.8rem;">
                        No tasks
                    </div>
                </template>
            </VueDraggable>
        </div>
    </div>
</template>

<script setup lang="ts">
import { VueDraggable } from 'vue-draggable-plus';
import DeleteConfirmationButton from '@/Components/Button/DeleteConfirmationButton.vue';
import { avatarColor, initials, formatDate, isOverdue, priorityLabel, priorityClass } from '@/Utils/taskHelpers';
import { Column } from '@/types/interfaces/column';
import { Project } from '@/types/interfaces/project';
import { ref, watch  } from 'vue';

const props = defineProps<{
    columns: Column[];
    tasks: Record<string, any>[];
    loading: boolean;
    project: Project;
}>();

const emit = defineEmits<{
    openTask: [task: any];
    taskDeleted: [];
    taskMoved: [taskId: number, status: string, position: number];
}>();

const columnTasks = ref<Record<string, any[]>>({});

const buildColumnTasks = () => {
    const map: Record<string, any[]> = {};
    props.columns.forEach(col => {
        map[col.status] = props.tasks
            .filter(t => t.status === col.status)
            .sort((a, b) => a.position - b.position);
    });
    columnTasks.value = map;
};

watch(() => props.tasks, buildColumnTasks, { immediate: true });


const onDragEnd = (evt: any) => {
    const taskId    = Number(evt.item.dataset.id);
    const newIndex  = evt.newIndex ?? 0;
    const status    = evt.to.dataset.status; // ← target column status

    console.log('onDragEnd', { taskId, status, newIndex });

    if (isNaN(taskId)) return;
    emit('taskMoved', taskId, status, newIndex);
};
</script>