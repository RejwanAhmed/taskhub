<script setup lang="ts">
import { useForm } from '@inertiajs/vue3';
import { nextTick, ref } from 'vue';

const confirmingUserDeletion = ref(false);
const passwordInput = ref<HTMLInputElement | null>(null);

const form = useForm({
    password: '',
});

const confirmUserDeletion = () => {
    confirmingUserDeletion.value = true;
    nextTick(() => passwordInput.value?.focus());
};

const deleteUser = () => {
    form.delete(route('profile.destroy'), {
        preserveScroll: true,
        onSuccess: () => closeModal(),
        onError: () => passwordInput.value?.focus(),
        onFinish: () => form.reset(),
    });
};

const closeModal = () => {
    confirmingUserDeletion.value = false;
    form.clearErrors();
    form.reset();
};
</script>

<template>
    <section class="mb-5">
        <div class="mb-3">
            <h2 class="h5">Delete Account</h2>
            <p class="text-secondary small">
                Once your account is deleted, all of its resources and data will be permanently deleted.
                Before deleting your account, please download any data or information that you wish to retain.
            </p>
        </div>

        <button class="btn btn-danger" @click="confirmUserDeletion">Delete Account</button>

        <!-- Modal -->
        <div class="modal fade show" v-if="confirmingUserDeletion" tabindex="-1"
            style="display: block; background-color: rgba(0, 0, 0, 0.5);" aria-modal="true" role="dialog">
            <div class="modal-dialog" role="document">
                <div class="modal-content">

                    <div class="modal-header">
                        <h5 class="modal-title">Are you sure you want to delete your account?</h5>
                        <button type="button" class="btn-close" aria-label="Close" @click="closeModal"></button>
                    </div>

                    <div class="modal-body">
                        <p class="text-secondary">
                            Once your account is deleted, all of its resources and data will be permanently deleted.
                            Please enter your password to confirm you would like to permanently delete your account.
                        </p>

                        <div class="mb-3">
                            <label for="password" class="form-label visually-hidden">Password</label>
                            <input id="password" ref="passwordInput" v-model="form.password" type="password"
                                class="form-control" placeholder="Password" @keyup.enter="deleteUser"
                                autocomplete="current-password" />
                            <div v-if="form.errors.password" class="text-danger mt-1">
                                {{ form.errors.password }}
                            </div>
                        </div>
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" @click="closeModal">Cancel</button>
                        <button type="button" class="btn btn-danger" :disabled="form.processing"
                            :class="{ disabled: form.processing }" @click="deleteUser">
                            Delete Account
                        </button>
                    </div>

                </div>
            </div>
        </div>
    </section>
</template>
