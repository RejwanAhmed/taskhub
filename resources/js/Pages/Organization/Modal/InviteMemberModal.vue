<template>
    <VForm @submit="submit">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 rounded-4">
                <div class="modal-header border-bottom-0 pb-0">
                    <h6 class="modal-title fw-bold">
                        <i class="bi bi-person-plus me-2 text-teal"></i> Invite Member
                    </h6>
                    <button type="button" class="btn-close" @click="emit('close')"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="email" class="form-label fw-semibold small">
                            User Email <span class="text-danger">*</span>
                        </label>
                        <Field type="email" name="email" class="form-control" placeholder="abc@example.com" v-model="formData.email" required></Field>
                        <ErrorMessage :errorMessage="formData.errors.email"/>
                    </div>
                    <div class="mb-1">
                        <label for="role" class="form-label fw-semibold small">
                            User Role <span class="text-danger">*</span>
                        </label>
                        <Field as="select" name="role" class="form-select" v-model="formData.role">
                            <option value="" disabled>Select a role</option>
                            <option v-for="role in roles" :key="role" :value="role">
                                {{ role.charAt(0).toUpperCase() + role.slice(1) }}
                            </option>
                        </Field>
                        <ErrorMessage :errorMessage="formData.errors.role"/>
                    </div>
                </div>
                <div class="modal-footer border-top-0">
                    <button class="btn btn-light" @click="emit('close')">Cancel</button>
                    <SubmitButton label="Invite" :processing="formData.processing" />
                </div>
            </div>
        </div>
    </VForm>
</template>

<script setup lang="ts">
import { Field, Form as VForm } from "vee-validate";
import { useForm } from "@inertiajs/vue3";
import SubmitButton from "@/Components/Button/SubmitButton.vue";
import ErrorMessage from "@/Components/ErrorMessage.vue";
import { ref } from "vue";

const emit = defineEmits<{
    close: []
}>();

const roles = ref(['manager', 'member']);

const formData = useForm({
    email: '',
    role: ''
});
const submit = () => {
    formData.post(route('invitations.store'), {
        preserveScroll: true,
        onSuccess: () => emit('close'),
    })
}
</script>