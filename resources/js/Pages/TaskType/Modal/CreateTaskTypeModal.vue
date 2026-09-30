<template>
    <VForm @submit="submit">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 rounded-4">

                <div class="modal-header border-bottom-0 pb-0">
                    <h6 class="modal-title fw-bold">
                        <i class="bi bi-tag me-2 text-teal"></i>
                        {{ props.taskType ? 'Edit Task Type' : 'New Task Type' }}
                    </h6>
                    <button type="button" class="btn-close" @click="emit('close')"></button>
                </div>

                <div class="modal-body">
                    <!-- Name -->
                    <div class="mb-3">
                        <label class="form-label fw-semibold small">
                            Name <span class="text-danger">*</span>
                        </label>
                        <Field type="text" name="name" class="form-control" placeholder="e.g. Bug, Feature, Improvement" v-model="formData.name" required/>
                        <ErrorMessage :errorMessage="formData.errors.name" />
                    </div>

                    <!-- Icon -->
                    <div class="mb-3">
                        <label class="form-label fw-semibold small">
                            Icon
                            <span class="text-muted fw-normal">(Bootstrap icon class)</span>
                        </label>
                        <div class="input-group">
                            <span class="input-group-text bg-white">
                                <i v-if="formData.icon" :class="['bi', formData.icon]" :style="{ color: formData.color }"></i>
                                <i v-else class="bi bi-question text-muted"></i>
                            </span>
                            <Field type="text" name="icon" class="form-control" placeholder="e.g. bi-bug, bi-star, bi-lightning" v-model="formData.icon"/>
                        </div>
                        <ErrorMessage :errorMessage="formData.errors.icon" />
                        <!-- Quick icon presets -->
                        <div class="d-flex gap-2 flex-wrap mt-2">
                            <button v-for="preset in iconPresets" :key="preset.icon" type="button" class="btn btn-sm btn-light d-flex align-items-center gap-1" :class="{ 'border-teal': formData.icon === preset.icon }" @click="formData.icon = preset.icon" :title="preset.label">
                                <i :class="['bi', preset.icon]" :style="{ color: formData.color }"></i>
                                <span style="font-size: 0.7rem;">{{ preset.label }}</span>
                            </button>
                        </div>
                    </div>

                    <!-- Color -->
                    <div class="mb-3">
                        <label class="form-label fw-semibold small">Color</label>
                        <div class="d-flex align-items-center gap-2">
                            <input type="color" class="form-control form-control-color w-100" v-model="formData.color" title="Pick a color"
                            />
                            <span class="small text-muted">{{ formData.color }}</span>
                        </div>
                        <!-- Quick color presets -->
                        <div class="d-flex gap-2 flex-wrap mt-2">
                            <button v-for="preset in colorPresets" :key="preset" type="button" class="color-preset" :style="{ background: preset, outline: formData.color === preset ? `3px solid ${preset}` : 'none' }" @click="formData.color = preset"
                            ></button>
                        </div>
                        <ErrorMessage :errorMessage="formData.errors.color" />
                    </div>

                    <!-- Is Default -->
                    <div class="border rounded-3 p-3 d-flex align-items-center justify-content-between bg-light">
                        <label class="form-check-label small fw-semibold mb-0" for="isDefault">
                            Set as default type
                        </label>
                        <div class="form-check form-switch mb-0 fs-5">
                            <input class="form-check-input" type="checkbox" id="isDefault" v-model="formData.is_default"/>
                        </div>
                    </div>
                </div>

                <div class="modal-footer border-top-0">
                    <button type="button" class="btn btn-light" @click="emit('close')">Cancel</button>
                    <SubmitButton :label="props.taskType ? 'Update' : 'Create'" :processing="formData.processing"/>
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
import { TaskType } from '@/types/interfaces/taskType';

const props = defineProps<{
    taskType: TaskType | null;
}>();

const emit = defineEmits<{
    close: [];
}>();

const colorPresets = [
    '#3B82F6', '#10B981', '#F59E0B',
    '#EF4444', '#8B5CF6', '#EC4899',
    '#0d9488', '#F97316', '#6B7280',
];

const iconPresets = [
    { icon: 'bi-bug',         label: 'Bug'         },
    { icon: 'bi-star',        label: 'Feature'     },
    { icon: 'bi-lightning',   label: 'Improvement' },
    { icon: 'bi-check2',      label: 'Task'        },
    { icon: 'bi-fire',        label: 'Urgent'      },
    { icon: 'bi-wrench',      label: 'Chore'       },
];

const formData = useForm({
    name:       props.taskType?.name       ?? '',
    color:      props.taskType?.color      ?? '#6B7280',
    icon:       props.taskType?.icon       ?? '',
    is_default: props.taskType?.is_default ?? false,
});

const submit = () => {
    if (props.taskType?.id) {
        formData.put(route('task-types.update', props.taskType.id), {
            preserveScroll: true,
            onSuccess: () => emit('close'),
        });
    } else {
        formData.post(route('task-types.store'), {
            preserveScroll: true,
            onSuccess: () => emit('close'),
        });
    }
};
</script>