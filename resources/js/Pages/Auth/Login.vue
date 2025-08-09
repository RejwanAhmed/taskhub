<script setup lang="ts">
import GuestLayout from "@/Layouts/GuestLayout.vue";
import { Head, Link, useForm } from "@inertiajs/vue3";

defineProps<{
    canResetPassword?: boolean;
    status?: string;
}>();

const form = useForm({
    email: "",
    password: "",
    remember: false,
});

const submit = () => {
    form.post(route("login"), {
        onFinish: () => form.reset("password"),
    });
};
</script>

<template>
    <GuestLayout>

        <Head title="Login" />

        <div class="container d-flex justify-content-center align-items-center min-vh-100">
            <div class="col-md-6 col-lg-5">
                <div class="card shadow-sm border-0">
                    <div class="card-body p-4">
                        <h3 class="card-title mb-4 text-center">
                            Sign in to your account
                        </h3>

                        <div v-if="status" class="alert alert-success text-sm">
                            {{ status }}
                        </div>

                        <form @submit.prevent="submit" novalidate>
                            <!-- Email -->
                            <div class="mb-3">
                                <label for="email" class="form-label">Email address</label>
                                <input type="email" id="email" class="form-control" v-model="form.email" required
                                    autocomplete="username" />
                                <div v-if="form.errors.email" class="text-danger small mt-1">
                                    {{ form.errors.email }}
                                </div>
                            </div>

                            <!-- Password -->
                            <div class="mb-3">
                                <label for="password" class="form-label">Password</label>
                                <input type="password" id="password" class="form-control" v-model="form.password"
                                    required autocomplete="current-password" />
                                <div v-if="form.errors.password" class="text-danger small mt-1">
                                    {{ form.errors.password }}
                                </div>
                            </div>

                            <!-- Remember Me -->
                            <div class="form-check mb-3">
                                <input class="form-check-input" type="checkbox" id="remember" v-model="form.remember" />
                                <label class="form-check-label" for="remember">
                                    Remember me
                                </label>
                            </div>

                            <!-- Actions -->
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <Link v-if="canResetPassword" :href="route('password.request')"
                                    class="text-decoration-none small">
                                Forgot password?
                                </Link>
                            </div>

                            <!-- Submit Button -->
                            <div class="d-grid">
                                <button type="submit" class="btn btn-primary" :disabled="form.processing">
                                    Log in
                                </button>
                            </div>
                        </form>

                        <!-- Register Link -->
                        <div class="mt-4 text-center small">
                            Don’t have an account?
                            <Link href="/register" class="text-decoration-none">Register</Link>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </GuestLayout>
</template>
