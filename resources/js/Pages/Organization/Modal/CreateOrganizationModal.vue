<template>
    <VForm @submit="submit">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 rounded-4">
                <div class="modal-header border-bottom-0 pb-0">
                    <h6 class="modal-title fw-bold">
                        <i class="bi bi-building me-2 text-teal"></i> New Organization
                    </h6>
                    <button type="button" class="btn-close" @click="emit('close')"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="name" class="form-label fw-semibold small">
                            Organization Name <span class="text-danger">*</span>
                        </label>
                        <Field type="text" name="name" class="form-control" placeholder="e.g. Acme Corp" v-model="formData.name" required></Field>
                        <ErrorMessage :errorMessage="formData.errors.name"/>
                    </div>
                    <div class="mb-1">
                        <label for="description" class="form-label fw-semibold small">Description</label>
                        <textarea id="description" class="form-control" rows="3" placeholder="What does this organization do?"></textarea>
                        <ErrorMessage :errorMessage="formData.errors.description"/>
                    </div>
                </div>

                <div class="modal-footer border-top-0">
                    <button class="btn btn-light" @click="emit('close')">Cancel</button>
                    <SubmitButton label="Create" :processing="formData.processing" />
                </div>
            </div>
        </div>
    </VForm>
    
</template>

<script setup lang="ts">
import { Field, Form as VForm } from "vee-validate";
import { useForm  } from '@inertiajs/vue3';
import ErrorMessage from "@/Components/ErrorMessage.vue";
import SubmitButton from "@/Components/Button/SubmitButton.vue";

const props = defineProps({
    showModal: Boolean,
})

const emit = defineEmits(['close']);

const formData = useForm({
    name: '',
    description: '',
})

const submit = () => {
    formData.post(route('organizations.store'), {
        preserveScroll:true,
        onSuccess: () => emit('close')
    });
}
</script>