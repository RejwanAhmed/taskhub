<template>
    <GuestLayout>
        <Head title="Log in - Invitation" />
        <div class="container d-flex justify-content-center align-items-center min-vh-100">
            <div class="col-md-6 col-lg-5">
                <div class="card border-0 shadow-sm">
                    <div class="invite-accent-bar"></div>
                    <div class="card-body p-4">

                        <!-- Header -->
                        <div class="text-center mb-4">
                            <h3 class="card-title mb-1">Welcome Back</h3>
                            <p class="text-muted small">
                                Log in to join <strong>{{ organizationName }}</strong>
                            </p>
                        </div>

                        <!-- Login Form -->
                        <form @submit.prevent="submit">

                            <!-- Email (Pre-filled and Read-only) -->
                            <div class="mb-3">
                                <label for="email" class="form-label">Email Address</label>
                                <input type="email" class="form-control bg-light" :class="{ 'is-invalid': form.errors.email }" id="email" v-model="form.email" readonly>
                                <div v-if="form.errors.email" class="invalid-feedback">
                                    {{ form.errors.email }}
                                </div>
                            </div>

                            <!-- Password -->
                            <div class="mb-3">
                                <label for="password" class="form-label">Password</label>
                                <input type="password" class="form-control" :class="{ 'is-invalid': form.errors.password }" id="password"  v-model="form.password" placeholder="Enter your password" required autofocus>
                                <div v-if="form.errors.password" class="invalid-feedback">
                                    {{ form.errors.password }}
                                </div>
                            </div>

                            <!-- Remember Me -->
                            <div class="mb-3 form-check">
                                <input type="checkbox" class="form-check-input" id="remember" v-model="form.remember">
                                <label class="form-check-label" for="remember">
                                    Remember me
                                </label>
                            </div>

                            <!-- Forgot Password Link -->
                            <div class="mb-4">
                                <Link :href="route('password.request')" class="text-decoration-none small">
                                    Forgot your password?
                                </Link>
                            </div>

                            <!-- Submit Button -->
                            <div class="d-grid">
                                <button type="submit" class="btn btn-primary bg-teal border-0" :disabled="form.processing">
                                    <span v-if="form.processing" class="spinner-border spinner-border-sm me-2"></span>
                                    {{ form.processing ? 'Logging in...' : 'Log in & Join Organization' }}
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </GuestLayout>
</template>

<script setup lang="ts">
import GuestLayout from '@/Layouts/GuestLayout.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';

const props = defineProps<{
    token: string;
    email: string;
    organizationName: string;
}>();


const form = useForm({
    email: props.email,
    password: '',
    remember: false,
});

const submit = () => {
    form.post(route('invitations.storeLogin', { token: props.token }), {
        onFinish: () => {
            form.reset('password');
        },
    });
};
</script>