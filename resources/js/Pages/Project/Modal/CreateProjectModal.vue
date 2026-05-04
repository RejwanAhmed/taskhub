<template>
    <VForm @submit="submit">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 rounded-4">

                <div class="modal-header border-bottom-0 pb-0">
                    <h6 class="modal-title fw-bold">
                        <i class="bi bi-kanban me-2 text-teal"></i>
                        {{ props.project ? 'Edit Project' : 'New Project' }}
                    </h6>
                    <button type="button" class="btn-close" @click="emit('close')"></button>
                </div>

                <div class="modal-body">
                    <!-- Name -->
                    <div class="mb-3">
                        <label class="form-label fw-semibold small">
                            Project Name <span class="text-danger">*</span>
                        </label>
                        <Field type="text" name="name" class="form-control" placeholder="e.g. Website Redesign" v-model="formData.name" rules="required" />
                        <ErrorMessage :errorMessage="formData.errors.name" />
                    </div>

                    <!-- Description -->
                    <div class="mb-3">
                        <label class="form-label fw-semibold small">Description</label>
                        <textarea class="form-control" rows="3" placeholder="What is this project about?" v-model="formData.description">
                        </textarea>
                        <ErrorMessage :errorMessage="formData.errors.description" />
                    </div>

                    <!-- Status + Color (side by side) -->
                    <div class="row g-3 mb-3">
                        <div class="col-8">
                            <label class="form-label fw-semibold small">Status</label>
                            <Field as="select" name="status" class="form-select" v-model="formData.status">
                                <option value="planning">Planning</option>
                                <option value="active">Active</option>
                                <option value="on_hold">On Hold</option>
                                <option value="completed">Completed</option>
                            </Field>
                            <ErrorMessage :errorMessage="formData.errors.status" />
                        </div>

                        <div class="col-4">
                            <label class="form-label fw-semibold small">Color</label>
                            <div class="d-flex align-items-center gap-2">
                                <input type="color" class="form-control form-control-color w-100" v-model="formData.color" title="Pick project color" />
                            </div>
                            <ErrorMessage :errorMessage="formData.errors.color" />
                        </div>
                    </div>

                    <!-- Quick color presets -->
                    <div class="mb-3">
                        <label class="form-label fw-semibold small d-block">Quick colors</label>
                        <div class="d-flex gap-2 flex-wrap">
                            <button v-for="preset in colorPresets" :key="preset" type="button" class="color-preset" :style="{ background: preset, outline: formData.color === preset ? `3px solid ${preset}` : 'none' }" :class="{ 'color-preset--active': formData.color === preset }" @click="formData.color = preset">
                            </button>
                        </div>
                    </div>

                    <!-- Start date + End date -->
                    <div class="row g-3">
                        <div class="col-6">
                            <label class="form-label fw-semibold small">Start Date</label>
                            <Field type="date" name="start_date" class="form-control" v-model="formData.start_date" />
                            <ErrorMessage :errorMessage="formData.errors.start_date" />
                        </div>
                        <div class="col-6">
                            <label class="form-label fw-semibold small">End Date</label>
                            <Field type="date" name="end_date" class="form-control" :min="formData.start_date" v-model="formData.end_date" />
                            <ErrorMessage :errorMessage="formData.errors.end_date" />
                        </div>
                    </div>

                </div>

                <div class="modal-footer border-top-0">
                    <button type="button" class="btn btn-light" @click="emit('close')">Cancel</button>
                    <SubmitButton :label="props.project?.id ? 'Update' : 'Create'" :processing="formData.processing" />
                </div>
            </div>
        </div>
    </VForm>
</template>

<script setup lang="ts">
import { Field, Form as VForm } from 'vee-validate';
import { useForm } from '@inertiajs/vue3';
import ErrorMessage from '@/Components/ErrorMessage.vue';
import SubmitButton from '@/Components/Button/SubmitButton.vue';

const props = defineProps<{
    project: Record<string, any> | null;
}>();

const emit = defineEmits<{
    close: [];
}>();

const colorPresets = [
    '#3B82F6', '#10B981', '#F59E0B',
    '#EF4444', '#8B5CF6', '#EC4899',
    '#0d9488', '#F97316', '#6B7280',
];

const formData = useForm({
    name:        props.project?.name        ?? '',
    description: props.project?.description ?? '',
    status:      props.project?.status      ?? 'planning',
    color:       props.project?.color       ?? '#3B82F6',
    start_date:  props.project?.start_date  ?? '',
    end_date:    props.project?.end_date    ?? '',
});

const submit = () => {
    if (props.project?.id) {
        formData.put(route('projects.update', props.project.id), {
            preserveScroll: true,
            onSuccess: () => emit('close'),
        });
    } else {
        formData.post(route('projects.store'), {
            preserveScroll: true,
            onSuccess: () => emit('close'),
        });
    }
};
</script>