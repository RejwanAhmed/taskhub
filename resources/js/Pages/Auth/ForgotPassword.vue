<script setup lang="ts">
import GuestLayout from '@/Layouts/GuestLayout.vue';
import InputError from '@/Components/InputError.vue';
import ErrorMessage from '@/Components/ErrorMessage.vue';
import { Head, useForm } from '@inertiajs/vue3';

defineProps<{
    status?: string;
}>();

const form = useForm({
    email: '',
});

const submit = () => {
    form.post(route('password.email'));
};
</script>

<template>
    <GuestLayout>
        <Head title="Forgot Password" />

        <div class="container d-flex justify-content-center align-items-center min-vh-100">
            <div class="col-md-6 col-lg-5">
                <div class="card shadow-sm border-0">
                    <div class="card-body p-4">
                        <!-- Icon -->
                        <div class="text-center mb-4">
                            <div class="justify-content-center">
                                <i class="bi bi-key text-teal fs-1"></i>
                            </div>
                        </div>

                        <!-- Title -->
                        <h3 class="card-title mb-3 text-center">
                            Forgot Password?
                        </h3>

                        <!-- Description -->
                        <p class="text-muted text-center mb-4">
                            No problem. Just let us know your email address and we will email you a password 
                            reset link that will allow you to choose a new one.
                        </p>

                        <!-- Success Message -->
                        <div v-if="status" class="alert alert-success d-flex align-items-center mb-4">
                            <i class="bi bi-check-circle-fill me-2"></i>
                            <span>{{ status }}</span>
                        </div>

                        <!-- Form -->
                        <form @submit.prevent="submit">
                            <!-- Email Input -->
                            <div class="mb-3">
                                <label for="email" class="form-label">Email Address</label>
                                <input id="email" type="email" class="form-control" v-model="form.email" autofocus autocomplete="username" placeholder="Enter your email"/>
                                <!-- <InputError class="mt-2" :message="form.errors.email" /> -->
                                <ErrorMessage class="mt-2" :errorMessage="form.errors.email"/>
                            </div>

                            <!-- Submit Button -->
                            <div class="d-grid">
                                <button type="submit" class="btn btn-primary bg-teal border-0" :disabled="form.processing">
                                    <i class="bi bi-envelope me-2"></i>
                                    <span v-if="form.processing">Sending...</span>
                                    <span v-else>Email Password Reset Link</span>
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </GuestLayout>
</template>