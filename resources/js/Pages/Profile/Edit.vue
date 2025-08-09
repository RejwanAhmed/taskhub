<script setup lang="ts">
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import DeleteUserForm from './Partials/DeleteUserForm.vue';
import UpdatePasswordForm from './Partials/UpdatePasswordForm.vue';
import UpdateProfileInformationForm from './Partials/UpdateProfileInformationForm.vue';
import { Head } from '@inertiajs/vue3';

defineProps<{
    mustVerifyEmail?: boolean;
    status?: string;
}>();
</script>

<template>

    <Head title="Profile" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="h5 fw-semibold text-dark mb-0">Profile</h2>
        </template>
        
        <div class="container-fluid">
            <!-- Update Profile Information -->
            <div class="col-12 mb-3">
                <div class="card shadow-sm">
                    <div class="card-body">
                        <UpdateProfileInformationForm :must-verify-email="mustVerifyEmail" :status="status"/>
                    </div>
                </div>
            </div>

            <!-- Update Password -->
            <div class="col-12 mb-3">
                <div class="card shadow-sm">
                    <div class="card-body">
                        <UpdatePasswordForm />
                    </div>
                </div>
            </div>

            <!-- Delete User -->
            <div class="col-12">
                <div class="card shadow-sm">
                    <div class="card-body">
                        <DeleteUserForm  />
                    </div>
                </div>
            </div>
        </div>
       
    </AuthenticatedLayout>
</template>
