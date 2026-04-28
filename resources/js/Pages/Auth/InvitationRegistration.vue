<template>
    <GuestLayout>

        <Head title="Register - Invitation" />

        <div class="container d-flex justify-content-center align-items-center min-vh-100">
            <div class="col-md-6 col-lg-5">
                <div class="card border-0 shadow-sm">
                    <div class="invite-accent-bar"></div>
                    <div class="card-body p-4">

                        <!-- Header -->
                        <div class="text-center mb-4">
                            <h3 class="card-title mb-1">Complete Your Registration</h3>
                            <p class="text-muted small">
                                You've been invited to join <strong>{{ organizationName }}</strong>
                            </p>
                        </div>

                        <!-- Registration Form -->
                        <form @submit.prevent="submit">

                            <!-- Organization Name (Read-only) -->
                            <div class="mb-3">
                                <label for="organization" class="form-label">Organization Name</label>
                                <input type="text" class="form-control bg-light" id="organization" :value="organizationName" readonly disabled>
                            </div>

                            <!-- Name -->
                            <div class="mb-3">
                                <label for="name" class="form-label">User Name</label>
                                <input type="text" class="form-control" :class="{ 'is-invalid': form.errors.name }"
                                    id="name" v-model="form.name" placeholder="Enter your full name" required autofocus>
                                <ErrorMessage :errorMessage="form.errors.name"/>
                            </div>

                            <!-- Email (Pre-filled and Read-only) -->
                            <div class="mb-3">
                                <label for="email" class="form-label">Email Address</label>
                                <input type="email" class="form-control bg-light"
                                    :class="{ 'is-invalid': form.errors.email }" id="email" v-model="form.email"
                                    readonly>
                                <ErrorMessage :errorMessage="form.errors.email"/>
                            </div>

                            <!-- Password -->
                            <div class="mb-3">
                                <label for="password" class="form-label">Password</label>
                                <input type="password" class="form-control"
                                    :class="{ 'is-invalid': form.errors.password }" id="password"
                                    v-model="form.password" placeholder="Enter your password" required>
                                <ErrorMessage :errorMessage="form.errors.password"/>
                            </div>

                            <!-- Confirm Password -->
                            <div class="mb-4">
                                <label for="password_confirmation" class="form-label">Confirm Password</label>
                                <input type="password" class="form-control"
                                    :class="{ 'is-invalid': form.errors.password_confirmation }"
                                    id="password_confirmation" v-model="form.password_confirmation"
                                    placeholder="Confirm your password" required>
                                <ErrorMessage :errorMessage="form.errors.password_confirmation"/>
                            </div>

                            <!-- Submit Button -->
                            <div class="d-grid">
                                <button type="submit" class="btn btn-primary bg-teal border-0"
                                    :disabled="form.processing">
                                    <span v-if="form.processing" class="spinner-border spinner-border-sm me-2"></span>
                                    {{ form.processing ? 'Registering...' : 'Register & Join Organization' }}
                                </button>
                            </div>
                        </form>

                        <!-- Footer -->
                        <p class="text-center text-muted mt-4 mb-0" style="font-size: 0.85rem;">
                            Already have an account?
                            <!-- <Link :href="route('invitation.login', { token: token })" class="text-decoration-none"> -->
                            Log in instead
                            <!-- </Link> -->
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </GuestLayout>
</template>

<script setup lang="ts">
import GuestLayout from '@/Layouts/GuestLayout.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import ErrorMessage from '@/Components/ErrorMessage.vue';


const props = defineProps<{
    token: string;
    email: string;
    organizationName: string;
}>();

const form = useForm({
    name: '',
    email: props.email,
    password: '',
    password_confirmation: '',
});

const submit = () => {
    form.post(route('invitations.storeRegistration', { token: props.token }), {
        onFinish: () => {
            form.reset('password', 'password_confirmation');
        },
    });
};
</script>