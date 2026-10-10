<template>
    <VForm @submit="submit">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 rounded-4">

                <div class="modal-header border-bottom-0 pb-0">
                    <h6 class="modal-title fw-bold">
                        <i class="bi bi-tag me-2 text-teal"></i>
                        {{ props.tag ? 'Edit Tag' : 'New Tag' }}
                    </h6>
                    <button type="button" class="btn-close" @click="emit('close')"></button>
                </div>

                <div class="modal-body">
                    <!-- Name -->
                    <div class="mb-3">
                        <label class="form-label fw-semibold small">
                            Name <span class="text-danger">*</span>
                        </label>
                        <Field type="text" name="name" class="form-control" placeholder="e.g. Frontend, Backend, Critical" v-model="formData.name" required />
                        <ErrorMessage :errorMessage="formData.errors.name" />
                    </div>

                    <!-- Color -->
                    <div class="mb-3">
                        <label class="form-label fw-semibold small">Color</label>
                        <div class="d-flex align-items-center gap-2">
                            <input type="color" class="form-control form-control-color w-100" v-model="formData.color" title="Pick a color" />
                            <span class="small text-muted">{{ formData.color }}</span>
                        </div>
                        <!-- Quick color presets -->
                        <div class="d-flex gap-2 flex-wrap mt-2">
                            <button v-for="preset in colorPresets" :key="preset" type="button" class="color-preset"
                                :style="{ background: preset, outline: formData.color === preset ? `3px solid ${preset}` : 'none' }"
                                @click="formData.color = preset"
                            ></button>
                        </div>
                        <ErrorMessage :errorMessage="formData.errors.color" />
                    </div>

                    <!-- Description -->
                    <div class="mb-3">
                        <label class="form-label fw-semibold small">
                            Description
                            <span class="text-muted fw-normal">(optional)</span>
                        </label>
                        <Field as="textarea" name="description" class="form-control" placeholder="Brief description of this tag..." v-model="formData.description" rows="3" />
                        <ErrorMessage :errorMessage="formData.errors.description" />
                    </div>
                </div>

                <div class="modal-footer border-top-0">
                    <button type="button" class="btn btn-light" @click="emit('close')">Cancel</button>
                    <SubmitButton :label="props.tag ? 'Update' : 'Create'" :processing="formData.processing" />
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
import { Tag } from '@/types/interfaces/tag';

const props = defineProps<{
    tag: Tag | null;
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
    name:        props.tag?.name        ?? '',
    color:       props.tag?.color       ?? '#6B7280',
    description: props.tag?.description ?? '',
});

const submit = () => {
    if (props.tag?.id) {
        formData.put(route('tags.update', props.tag.id), {
            preserveScroll: true,
            onSuccess: () => emit('close'),
        });
    } else {
        formData.post(route('tags.store'), {
            preserveScroll: true,
            onSuccess: () => emit('close'),
        });
    }
};
</script>