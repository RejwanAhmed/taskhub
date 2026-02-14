<script setup lang="ts">
import GuestLayout from '@/Layouts/GuestLayout.vue';
import ErrorMessage from '@/Components/ErrorMessage.vue';
import { Head, useForm } from '@inertiajs/vue3';

const props = defineProps<{
    email: string;
    token: string;
}>();

const form = useForm({
    token: props.token,
    email: props.email,
    password: '',
    password_confirmation: '',
});

const submit = () => {
    form.post(route('password.store'), {
        onFinish: () => {
            form.reset('password', 'password_confirmation');
        },
    });
};
</script>

<template>
    <GuestLayout>
        <Head title="Reset Password" />

        <div class="container d-flex justify-content-center align-items-center min-vh-100">
            <div class="col-md-6 col-lg-5">
                <div class="card shadow-sm border-0">
                    <div class="card-body p-4">
                        <!-- Icon -->
                        <div class="text-center mb-4">
                            <div class="justify-content-center">
                                <i class="bi bi-shield-lock text-teal fs-1"></i>
                            </div>
                        </div>

                        <!-- Title -->
                        <h3 class="card-title mb-3 text-center">
                            Reset Your Password
                        </h3>

                        <!-- Description -->
                        <p class="text-muted text-center mb-4">
                            Enter your new password below to reset your account password.
                        </p>

                        <!-- Form -->
                        <form @submit.prevent="submit">
                            <!-- Email Input -->
                            <div class="mb-3">
                                <label for="email" class="form-label">Email Address</label>
                                <input
                                    id="email"
                                    type="email"
                                    class="form-control"
                                    v-model="form.email"
                                    required
                                    autofocus
                                    autocomplete="username"
                                    placeholder="Enter your email"
                                />
                                <ErrorMessage class="mt-2" :errorMessage="form.errors.email" />
                            </div>

                            <!-- Password Input -->
                            <div class="mb-3">
                                <label for="password" class="form-label">New Password</label>
                                <input
                                    id="password"
                                    type="password"
                                    class="form-control"
                                    v-model="form.password"
                                    required
                                    autocomplete="new-password"
                                    placeholder="Enter new password"
                                />
                                <ErrorMessage class="mt-2" :errorMessage="form.errors.password" />
                            </div>

                            <!-- Confirm Password Input -->
                            <div class="mb-3">
                                <label for="password_confirmation" class="form-label">Confirm Password</label>
                                <input
                                    id="password_confirmation"
                                    type="password"
                                    class="form-control"
                                    v-model="form.password_confirmation"
                                    required
                                    autocomplete="new-password"
                                    placeholder="Confirm new password"
                                />
                                <ErrorMessage class="mt-2" :errorMessage="form.errors.password_confirmation" />
                            </div>

                            <!-- Submit Button -->
                            <div class="d-grid">
                                <button 
                                    type="submit" 
                                    class="btn btn-primary bg-teal border-0"
                                    :disabled="form.processing"
                                >
                                    <i class="bi bi-check-circle me-2"></i>
                                    <span v-if="form.processing">Resetting...</span>
                                    <span v-else>Reset Password</span>
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </GuestLayout>
</template>