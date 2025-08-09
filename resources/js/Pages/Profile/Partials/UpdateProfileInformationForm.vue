<script setup lang="ts">
import { Link, useForm, usePage } from '@inertiajs/vue3';

defineProps<{
    mustVerifyEmail?: boolean;
    status?: string;
}>();

const user = usePage().props.auth.user;

const form = useForm({
    name: user.name,
    email: user.email,
});
</script>

<template>
    <section>
        <header class="mb-4">
            <h2 class="h5">Profile Information</h2>
            <p class="text-secondary small">
                Update your account's profile information and email address.
            </p>
        </header>

        <form @submit.prevent="form.patch(route('profile.update'))">
            <div class="mb-3">
                <label for="name" class="form-label">Name</label>
                <input id="name" type="text" class="form-control" v-model="form.name" required autofocus
                    autocomplete="name" />
                <div v-if="form.errors.name" class="text-danger mt-1">
                    {{ form.errors.name }}
                </div>
            </div>

            <div class="mb-3">
                <label for="email" class="form-label">Email</label>
                <input id="email" type="email" class="form-control" v-model="form.email" required
                    autocomplete="username" />
                <div v-if="form.errors.email" class="text-danger mt-1">
                    {{ form.errors.email }}
                </div>
            </div>

            <div v-if="mustVerifyEmail && user.email_verified_at === null" class="mb-3">
                <p class="text-warning small">
                    Your email address is unverified.
                    <Link :href="route('verification.send')" method="post" as="button"
                        class="btn btn-link p-0 align-baseline">
                    Click here to re-send the verification email.
                    </Link>
                </p>

                <div v-show="status === 'verification-link-sent'" class="text-success small">
                    A new verification link has been sent to your email address.
                </div>
            </div>

            <div class="d-flex align-items-center gap-3 mt-4">
                <button type="submit" class="btn btn-primary" :disabled="form.processing">
                    Save
                </button>

                <transition enter-active-class="fade show" leave-active-class="fade" enter-from-class="opacity-0"
                    leave-to-class="opacity-0">
                    <p v-if="form.recentlySuccessful" class="text-muted small mb-0">
                        Saved.
                    </p>
                </transition>
            </div>
        </form>
    </section>
</template>
