<script setup lang="ts">
import GuestLayout from "@/Layouts/GuestLayout.vue";
import { Head, Link, useForm } from "@inertiajs/vue3";

const form = useForm({
    organization_name: "",
    name: "",
    email: "",
    password: "",
    password_confirmation: "",
});

const submit = () => {
    form.post(route("register"), {
        onFinish: () => form.reset("password", "password_confirmation"),
    });
};
</script>

<template>
    <GuestLayout>

        <Head title="Register" />

        <div class="container d-flex justify-content-center align-items-center min-vh-100">
            <div class="col-md-6 col-lg-5">
                <div class="card shadow-sm border-0">
                    <div class="card-body p-4">
                        <h3 class="card-title mb-4 text-center">
                            Create an account
                        </h3>

                        <form @submit.prevent="submit" novalidate>

                            <!-- Name -->
                            <div class="mb-3">
                                <label for="organization_name" class="form-label">Organization Name</label>
                                <input type="text" id="organization_name" class="form-control" v-model="form.organization_name" required autofocus autocomplete="organization_name" />
                                <div v-if="form.errors.organization_name" class="text-danger small mt-1">
                                    {{ form.errors.organization_name }}
                                </div>
                            </div>

                            <!-- Name -->
                            <div class="mb-3">
                                <label for="name" class="form-label">User Name</label>
                                <input type="text" id="name" class="form-control" v-model="form.name" required autofocus
                                    autocomplete="name" />
                                <div v-if="form.errors.name" class="text-danger small mt-1">
                                    {{ form.errors.name }}
                                </div>
                            </div>

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
                                    required autocomplete="new-password" />
                                <div v-if="form.errors.password" class="text-danger small mt-1">
                                    {{ form.errors.password }}
                                </div>
                            </div>

                            <!-- Confirm Password -->
                            <div class="mb-3">
                                <label for="password_confirmation" class="form-label">Confirm Password</label>
                                <input type="password" id="password_confirmation" class="form-control"
                                    v-model="form.password_confirmation" required autocomplete="new-password" />
                                <div v-if="form.errors.password_confirmation" class="text-danger small mt-1">
                                    {{ form.errors.password_confirmation }}
                                </div>
                            </div>

                            <!-- Submit -->
                            <div class="d-grid mb-3">
                                <button type="submit" class="btn btn-primary bg-teal border-0" :disabled="form.processing">
                                    Register
                                </button>
                            </div>

                            <!-- Login Link -->
                            <div class="text-center small">
                                Already have an account?
                                <Link href="/login" class="text-decoration-none">Log in</Link>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </GuestLayout>
</template>
