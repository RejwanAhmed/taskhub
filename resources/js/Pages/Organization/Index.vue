<template>
    <Head title="Organizations" />
    <AuthenticatedLayout>

        <template #header>
            <h2 class="h5 fw-semibold">Organizations</h2>
        </template>
        <div class="container-fluid">
            <!-- Header -->
            <div class="d-flex align-items-center justify-content-between mb-4">
                <div>
                    <small class="text-muted">Manage your organizations</small>
                </div>
                <button class="btn bg-teal text-white" @click="openOrganizationModal">
                    <i class="bi bi-plus-lg me-1"></i> New Organization
                </button>
            </div>

            <!-- Search -->
            <div class="input-group mb-4">
                <span class="input-group-text bg-white"><i class="bi bi-search text-muted"></i></span>
                <input type="text" class="form-control border-start-0" placeholder="Search organizations..."/>
            </div>

            <!-- Organization Cards -->
            <div class="row g-3">
                <div v-for="org in props?.organizations" :key="org.id" class="col-md-6 col-lg-4">
                    <div class="card h-100 border-0 shadow-sm">
                        <div class="card-body">
                            <div class="d-flex align-items-start justify-content-between">
                                <div class="rounded-3 p-2 bg-opacity-10 text-teal">
                                    <i class="bi bi-building fs-5"></i>
                                </div>
                                <div class="dropdown">
                                    <button class="btn btn-sm btn-light" data-bs-toggle="dropdown">
                                        <i class="bi bi-three-dots-vertical"></i>
                                    </button>
                                    <ul class="dropdown-menu dropdown-menu-end shadow-sm border-0">
                                        <li>
                                            <button class="dropdown-item" @click="openOrganizationModal(org)">
                                                <i class="bi bi-pencil me-2 text-teal"></i> Edit
                                            </button>
                                        </li>
                                        <li><hr class="dropdown-divider" /></li>
                                        <li>
                                            <DeleteConfirmationButton confirm-route="organizations.destroy" :obj="org" :delete-content="org.name"></DeleteConfirmationButton>
                                        </li>
                                    </ul>
                                </div>
                            </div>

                            <h6 class="fw-bold mt-3 mb-1">{{ org.name }}</h6>
                            <p class="text-muted small mb-3">{{ org.description }}</p>

                            <div class="row">
                                <div class="col-lg-3 col-md-3 col-sm-3 col-6">
                                    <span class="badge bg-light text-muted fw-normal">
                                        <i class="bi bi-people me-1"></i> {{ org.members_count }} members
                                    </span>
                                </div>
                                <div class="col-lg-3 col-md-3 col-sm-3 col-6">
                                    <span class="badge bg-light text-muted fw-normal">
                                        <i class="bi bi-check2-square me-1"></i> {{ org.tasks_count }} tasks
                                    </span>
                                </div>
                                <div class="col-lg-3 col-md-3 col-sm-3 col-6">
                                    <span class="badge bg-light text-muted fw-normal">
                                        <i class="bi-person-gear me-1"></i> {{ org.pivot.role }}
                                    </span>
                                </div>
                                <div class="col-lg-3 col-md-3 col-sm-3 col-6">
                                    <span class="badge fw-normal" :class="['ms-2', statusColor(org.status)]">{{ org.status }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div v-if="showModal" class="modal d-block modal-background">
            <CreateOrganizationModal :showModal="showModal" :organization="selectedOrg" @close="showModal = false"></CreateOrganizationModal>
        </div>

        <!-- Delete Confirm Modal -->
        <!-- <div v-if="showDeleteModal" class="modal d-block" style="background: rgba(0,0,0,0.4);">
            <div class="modal-dialog modal-dialog-centered modal-sm">
                <div class="modal-content border-0 rounded-4 text-center p-2">
                    <div class="modal-body">
                        <i class="bi bi-exclamation-triangle-fill text-danger display-5"></i>
                        <h6 class="fw-bold mt-3">Delete Organization?</h6>
                        <p class="text-muted small">
                            <strong>Acme Corp</strong> will be permanently deleted.
                        </p>
                        <div class="d-flex gap-2 mt-3">
                            <button class="btn btn-light flex-fill" @click="showDeleteModal = false">Cancel</button>
                            <button class="btn btn-danger flex-fill">Delete</button>
                        </div>
                    </div>
                </div>
            </div>
        </div> -->

    </AuthenticatedLayout>
</template>

<script setup lang="ts">
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head } from '@inertiajs/vue3';
import { ref } from 'vue';
import CreateOrganizationModal from './Modal/CreateOrganizationModal.vue';
import DeleteConfirmationButton from '@/Components/Button/DeleteConfirmationButton.vue';

const props = defineProps<{
    organizations: Record<string, any>,
}>();

const showModal = ref(false);
const showDeleteModal = ref(false);
const selectedOrg = ref();

const statusColor = (status: string) => {
    const colors = {
        active: 'bg-success',
        pending: 'bg-warning',
        suspended: 'bg-danger',
        rejected: 'bg-secondary',
    };
    return colors[status as keyof typeof colors] || 'bg-secondary';
};

const openOrganizationModal = (organization: any | null) => {
    selectedOrg.value = organization;
    showModal.value = true;
}

</script>

<style scoped>
.border-dashed {
    border-style: dashed !important;
}
</style>