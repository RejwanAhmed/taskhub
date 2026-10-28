<template>
    <div class="modal d-block modal-background">
        <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
            <div class="modal-content border-0 rounded-4">

                <!-- Header -->
                <div class="modal-header border-bottom-0 pb-0">
                    <h6 class="modal-title fw-bold">
                        <i class="bi bi-diagram-2 me-2 text-teal"></i>
                        New Subtask
                    </h6>
                    <button type="button" class="btn-close" @click="emit('close')"></button>
                </div>

                <div class="modal-body">
                    <div class="row g-3">
                        <!-- Title -->
                        <div class="col-12">
                            <label class="form-label fw-semibold small">
                                Title <span class="text-danger">*</span>
                            </label>
                            <input v-model="form.title" type="text" class="form-control" placeholder="Subtask title" autofocus/>
                            <span v-if="formErrors.title" class="text-danger" style="font-size:0.8rem;">
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
                            <select v-model="form.task_type_id" class="form-select">
                                <option :value="null">— None —</option>
                                <option v-for="type in taskTypes" :key="type.id" :value="type.id">
                                    {{ type.name }}
                                </option>
                            </select>
                        </div>

                        <div class="col-6">
                            <label class="form-label fw-semibold small">Priority</label>
                            <select v-model="form.priority" class="form-select">
                                <option value="low">Low</option>
                                <option value="medium">Medium</option>
                                <option value="high">High</option>
                                <option value="urgent">Urgent</option>
                            </select>
                        </div>

                        <!-- Assigned To + Due Date -->
                        <div class="col-6">
                            <label class="form-label fw-semibold small">Assign To</label>
                            <select v-model="form.assigned_to" class="form-select">
                                <option :value="null">— Unassigned —</option>
                                <option v-for="member in project.members" :key="member.id" :value="member.id">
                                    {{ member.name }}
                                </option>
                            </select>
                        </div>

                        <div class="col-6">
                            <label class="form-label fw-semibold small">Due Date</label>
                            <input v-model="form.due_date" type="date" class="form-control" />
                        </div>

                        <!-- Estimated Hours -->
                        <div class="col-6">
                            <label class="form-label fw-semibold small">Estimated Hours</label>
                            <input v-model="form.estimated_hours" type="number" class="form-control" placeholder="e.g. 2" min="0" step="0.5"/>
                            <span v-if="formErrors.estimated_hours" class="text-danger" style="font-size:0.8rem;">
                                {{ formErrors.estimated_hours }}
                            </span>
                        </div>

                    </div>
                </div>

                <div class="modal-footer border-top-0">
                    <button type="button" class="btn btn-light" @click="emit('close')">Cancel</button>
                    <SubmitButton label="Add Subtask" :processing="processing" @click="submit" />
                </div>

            </div>
        </div>
    </div>
</template>

<script setup lang="ts">
import { ref, reactive } from 'vue';
import axios from 'axios';
import RichTextEditor from '@/Components/Editor/RichTextEditor.vue';
import SubmitButton from '@/Components/Button/SubmitButton.vue';
import { toast } from '@/Utils/toast';
import { handleAxiosError } from '@/Utils/handleAxiosError';
import { Task } from '@/types/interfaces/task';
import { Project } from '@/types/interfaces/project';
import { TaskType } from '@/types/interfaces/taskType';

const props = defineProps<{
    task: Task;
    project: Project;
    taskTypes: TaskType[];
}>();

const emit = defineEmits<{
    close: [];
    created: [];
}>();

const processing = ref(false);
const formErrors = reactive<Record<string, string>>({});

const form = reactive({
    title:           '',
    description:     '',
    priority:        'medium' as 'low' | 'medium' | 'high' | 'urgent',
    task_type_id:    null as number | null,
    assigned_to:     null as number | null,
    due_date:        '',
    estimated_hours: null as number | null,
});

const submit = async () => {
    processing.value = true;
    Object.keys(formErrors).forEach(k => delete formErrors[k]);

    try {
        const response = await axios.post(
            route('projects.tasks.subtasks.store', [props.project.id, props.task.id]),
            form
        );
        toast.success(response.data.success);
        emit('created');
    } catch (e: any) {
        handleAxiosError(e, formErrors);
    } finally {
        processing.value = false;
    }
};
</script>