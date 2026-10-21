<template>
    <div class="modal d-block modal-background">
        <VForm @submit="submit">
            <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-lg">
                <div class="modal-content border-0 rounded-4">

                    <div class="modal-header border-bottom-0 pb-0">
                        <h6 class="modal-title fw-bold">
                            <i class="bi bi-plus-square me-2 text-teal"></i>
                            New Task
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
                                <input v-model="form.title" type="text" class="form-control" placeholder="Task title"/>
                                <span v-if="formErrors.title" class="text-danger" style="font-size:0.8rem;">
                                    {{ formErrors.title }}
                                </span>
                            </div>

                            <!-- Description -->
                            <div class="col-12">
                                <label class="form-label fw-semibold small">Description</label>
                                <RichTextEditor v-model="form.description" placeholder="What needs to be done?"/>
                            </div>

                            <!-- Status + Priority -->
                            <div class="col-6">
                                <label class="form-label fw-semibold small">Status</label>
                                <select v-model="form.status" class="form-select">
                                    <option value="to_do">To Do</option>
                                    <option value="in_progress">In Progress</option>
                                    <option value="in_review">In Review</option>
                                    <option value="on_hold">On Hold</option>
                                    <option value="completed">Completed</option>
                                </select>
                                <span v-if="formErrors.status" class="text-danger" style="font-size:0.8rem;">
                                    {{ formErrors.status }}
                                </span>
                            </div>

                            <div class="col-6">
                                <label class="form-label fw-semibold small">Priority</label>
                                <select v-model="form.priority" class="form-select">
                                    <option value="low">Low</option>
                                    <option value="medium">Medium</option>
                                    <option value="high">High</option>
                                    <option value="urgent">Urgent</option>
                                </select>
                                <span v-if="formErrors.priority" class="text-danger" style="font-size:0.8rem;">
                                    {{ formErrors.priority }}
                                </span>

                            </div>

                            <!-- Task Type + Assigned To -->
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
                                <label class="form-label fw-semibold small">Assign To</label>
                                <select v-model="form.assigned_to" class="form-select">
                                    <option :value="null">— Unassigned —</option>
                                    <option v-for="member in project.members" :key="member.id" :value="member.id">
                                        {{ member.name }}
                                    </option>
                                </select>
                            </div>

                            <!-- Due Date + Estimated Hours -->
                            <div class="col-6">
                                <label class="form-label fw-semibold small">Due Date</label>
                                <input v-model="form.due_date" type="date" class="form-control"/>
                                <span v-if="formErrors.due_date" class="text-danger" style="font-size:0.8rem;">
                                    {{ formErrors.due_date }}
                                </span>
                            </div>

                            <div class="col-6">
                                <label class="form-label fw-semibold small">Estimated Hours</label>
                                <input v-model="form.estimated_hours" type="number" class="form-control" placeholder="e.g. 4" min="0" step="0.5"/>
                                <span v-if="formErrors.estimated_hours" class="text-danger" style="font-size:0.8rem;">
                                    {{ formErrors.estimated_hours }}
                                </span>
                            </div>

                            <!-- Tags -->
                            <div class="col-12">
                                <label class="form-label fw-semibold small">Tags</label>
                                <div class="d-flex flex-wrap gap-2">
                                    <button v-for="tag in tags" :key="tag.id" type="button" class="btn btn-sm"
                                        :style="isTagSelected(tag.id)
                                            ? { background: tag.color, color: '#fff', border: '1px solid ' + tag.color }
                                            : { background: tag.color + '22', color: tag.color, border: '1px solid ' + tag.color + '44' }
                                        "
                                        @click="toggleTag(tag.id)"
                                    >
                                        {{ tag.name }}
                                    </button>
                                    <span v-if="tags.length === 0" class="text-muted small">No tags available</span>
                                </div>
                            </div>

                        </div>
                    </div>

                    <div class="modal-footer border-top-0">
                        <button type="button" class="btn btn-light" @click="emit('close')">Cancel</button>
                        <SubmitButton label="Create Task" :processing="processing" />
                    </div>

                </div>
            </div>
        </VForm>
    </div>
</template>

<script setup lang="ts">
import { Form as VForm } from 'vee-validate';
import { useForm } from '@inertiajs/vue3';
import SubmitButton from '@/Components/Button/SubmitButton.vue';
import { Project } from '@/types/interfaces/project';
import { ref, reactive } from 'vue';
import axios from 'axios';
import { Tag } from '@/types/interfaces/tag';
import { TaskType } from '@/types/interfaces/taskType';
import RichTextEditor from '@/Components/Editor/RichTextEditor.vue';
import { toast } from '@/Utils/toast';
import { handleAxiosError } from '@/Utils/handleAxiosError';

const props = defineProps<{
    project: Project;
    tags: Tag[];
    taskTypes: TaskType[];
}>();

const emit = defineEmits<{
    close: [];
    created: [];
}>();

const processing = ref(false);
const formErrors = reactive<Record<string, string>>({});


const form = reactive({
    title:            '',
    description:      '',
    status:           'to_do',
    priority:         'medium',
    task_type_id:     null as number | null,
    assigned_to:      null as number | null,
    due_date:         '',
    estimated_hours:  null as number | null,
    tags:             [] as number[],
});

const isTagSelected = (id: number) => form.tags.includes(id);

const toggleTag = (id: number) => {
    const idx = form.tags.indexOf(id);
    if (idx === -1) {
        form.tags.push(id);
    } else {
        form.tags.splice(idx, 1);
    }
};

const submit = async () => {
    processing.value = true;
    Object.keys(formErrors).forEach(k => delete formErrors[k]);

    try {
        const response = await axios.post(route('projects.tasks.store', props.project.id), form);
        toast.success(response.data.success);
        emit('created');
        emit('close');
    } catch (e: any) {
        handleAxiosError(e, formErrors);
    } finally {
        processing.value = false;
    }
};
</script>