<script setup lang="ts">
import { computed } from 'vue';
import GuestLayout from '@/Layouts/GuestLayout.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';

const props = defineProps<{
    status?: string;
}>();

const form = useForm({});

const submit = () => {
    form.post(route('verification.send'));
};

const verificationLinkSent = computed(
    () => props.status === 'verification-link-sent',
);
</script>

<template>
    <GuestLayout>
        <Head title="Email Verification" />

        <div class="container d-flex justify-content-center align-items-center min-vh-100">
            <div class="col-md-6 col-lg-5">
                <div class="card shadow-sm border-0">
                    <div class="card-body p-4">
                        <!-- Icon -->
                        <div class="text-center mb-4">
                            <div class="justify-content-center">
                                <i class="bi bi-envelope-check text-teal fs-1"></i>
                            </div>
                        </div>

                        <!-- Title -->
                        <h3 class="card-title mb-3 text-center">
                            Verify Your Email
                        </h3>

                        <!-- Description -->
                        <p class="text-muted text-center mb-4">
                            Thanks for signing up! Before getting started, could you verify your email address by
                            clicking on the link we just emailed to you? If you didn't receive the email, we will
                            gladly send you another.
                        </p>

                        <!-- Success Message -->
                        <div v-if="verificationLinkSent" class="alert alert-success d-flex align-items-center mb-4">
                            <i class="bi bi-check-circle-fill me-2"></i>
                            <span>A new verification link has been sent to your email address.</span>
                        </div>

                        <!-- Resend Button -->
                        <form @submit.prevent="submit">
                            <div class="d-grid mb-3">
                                <button type="submit" class="btn btn-primary bg-teal border-0"
                                    :disabled="form.processing">
                                    <i class="bi bi-arrow-clockwise me-2"></i>
                                    <span v-if="form.processing">Sending...</span>
                                    <span v-else>Resend Verification Email</span>
                                </button>
                            </div>
                        </form>

                        <!-- Logout Link -->
                        <div class="text-center">
                            <Link :href="route('logout')" method="post" as="button"
                                class="btn btn-link text-decoration-none text-muted small">
                            <i class="bi bi-box-arrow-right me-1"></i>
                            Log Out
                            </Link>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </GuestLayout>
</template>
