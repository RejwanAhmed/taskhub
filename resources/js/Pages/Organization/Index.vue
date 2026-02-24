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
                <button class="btn bg-teal text-white" @click="showModal = true">
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
                <div v-for="org in organizations" :key="org.id" class="col-md-6 col-lg-4">
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
                                            <button class="dropdown-item" @click="showModal = true">
                                                <i class="bi bi-pencil me-2 text-teal"></i> Edit
                                            </button>
                                        </li>
                                        <li><hr class="dropdown-divider" /></li>
                                        <li>
                                            <button class="dropdown-item text-danger" @click="showDeleteModal = true">
                                                <i class="bi bi-trash me-2"></i> Delete
                                            </button>
                                        </li>
                                    </ul>
                                </div>
                            </div>

                            <h6 class="fw-bold mt-3 mb-1">{{ org.name }}</h6>
                            <p class="text-muted small mb-3">{{ org.description }}</p>

                            <div class="d-flex gap-2">
                                <span class="badge bg-light text-muted fw-normal">
                                    <i class="bi bi-people me-1"></i> {{ org.members_count }} members
                                </span>
                                <span class="badge bg-light text-muted fw-normal">
                                    <i class="bi bi-check2-square me-1"></i> {{ org.tasks_count }} tasks
                                </span>
                                <span class="badge bg-light text-muted fw-normal">
                                    <i class="bi-person-gear me-1"></i> {{ org.pivot.role }}
                                </span>
                                <span class="badge fw-normal" :class="['ms-2', statusColor(org.status)]">{{ org.status }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div v-if="showModal" class="modal d-block modal-background">
            <CreateOrganizationModal :showModal="showModal" @close="showModal = false"></CreateOrganizationModal>
        </div>
        <!-- Create / Edit Modal -->
        <!-- <div v-if="showModal" class="modal d-block" style="background: rgba(0,0,0,0.4);">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content border-0 rounded-4">
                    <div class="modal-header border-bottom-0 pb-0">
                        <h6 class="modal-title fw-bold">
                            <i class="bi bi-building me-2 text-teal"></i> New Organization
                        </h6>
                        <button type="button" class="btn-close" @click="showModal = false"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label fw-semibold small">
                                Organization Name <span class="text-danger">*</span>
                            </label>
                            <input type="text" class="form-control" placeholder="e.g. Acme Corp" />
                        </div>
                        <div class="mb-1">
                            <label class="form-label fw-semibold small">Description</label>
                            <textarea class="form-control" rows="3" placeholder="What does this organization do?"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer border-top-0">
                        <button class="btn btn-light" @click="showModal = false">Cancel</button>
                        <button class="btn bg-teal text-white">Create</button>
                    </div>
                </div>
            </div>
        </div> -->

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

const props = defineProps({
    organizations: Object,
});

const showModal = ref(false);
const showDeleteModal = ref(false);

const statusColor = (status: string) => {
    const colors = {
        active: 'bg-success',
        pending: 'bg-warning',
        suspended: 'bg-danger',
        rejected: 'bg-secondary',
    };
    return colors[status as keyof typeof colors] || 'bg-secondary';
};

const eventHandler = () => {
    showModal.value = false
}
</script>

<style scoped>
.border-dashed {
    border-style: dashed !important;
}
</style>