<template>

    <Head :title="`${currentOrganization.name} - Members`" />
    <AuthenticatedLayout>
        <template #header>
            <h2 class="h5 fw-semibold">{{ currentOrganization.name }} Members</h2>
        </template>

        <div class="container-fluid">
            <div class="d-flex align-items-center justify-content-between mb-4">
                <small class="text-muted">Manage team members</small>
                <div class="d-flex gap-2">
                    <Link :href="route('organizations.index')" class="btn btn-outline-secondary">
                        <i class="bi bi-arrow-left me-1"></i> Back
                    </Link>
                    <button class="btn bg-teal text-white" @click="showModal = true">
                        <i class="bi bi-person-plus me-1"></i> Invite Member
                    </button>
                </div>
            </div>

            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <AppDataTable :data="members" :columns="columns" search-placeholder="Search members...">
                        <template #name="{ row }">
                            <div class="d-flex align-items-center gap-2">
                                <div class="avatar-circle bg-teal text-white">{{ row.name.charAt(0).toUpperCase() }}
                                </div>
                                <strong>{{ row.name }}</strong>
                            </div>
                        </template>
                        <template #role="{ row }">
                            <span class="badge" :class="roleBadge(row.pivot.role)">{{ row.pivot.role }}</span>
                        </template>
                        <template #joined="{ row }">
                            {{ new Date(row.pivot.joined_at).toLocaleDateString() }}
                        </template>
                        <template #actions="{ row }">
                            <!-- Status change -->
                            <button class="btn" :class="row.pivot.status === 'active' ? 'text-success' : 'text-secondary'" @click="toggleStatus(row)">
                                <i class="bi fs-4" :class="row.pivot.status === 'active' ? 'bi-toggle-on' : 'bi-toggle-off'"></i>
                            </button>

                            <!-- Delete -->
                            <DeleteConfirmationButton
                                confirm-route="members.destroy"
                                :obj="row"
                                :delete-content="row.name"
                                :icon-only="true"
                            />
                        </template>
                    </AppDataTable>
                </div>
            </div>
        </div>

        <div v-if="showModal" class="modal d-block modal-background">
            <InviteMemberModal :showModal=showModal @close="showModal = false"></InviteMemberModal>
        </div>
    </AuthenticatedLayout>
</template>

<script setup lang="ts">
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link } from '@inertiajs/vue3';
import { usePage } from '@inertiajs/vue3';
import { computed, ref } from 'vue';
import AppDataTable from '@/Components/Table/DataTable.vue';
import DeleteConfirmationButton from '@/Components/Button/DeleteConfirmationButton.vue';
import InviteMemberModal from './Modal/InviteMemberModal.vue';
import { Members } from '@/types/interfaces/members';

const props = defineProps<{
    members: Members[]
}>()

const showModal = ref(false);

const page = usePage();
const currentOrganization = computed(() => page.props.currentOrg as any);

const columns = [
    { key: 'name', label: 'Name', sortable: true },
    { key: 'email', label: 'Email', sortable: true },
    { key: 'role', label: 'Role', sortable: false },
    { key: 'joined', label: 'Joined', sortable: true },
    { key: 'actions', label: 'Actions', sortable: false, searchable: false },
];

const roleBadge = (role: string) => (
    {
        owner: 'bg-danger',
        manager: 'bg-warning text-dark',
        member: 'bg-secondary'
    }
    [role] ?? 'bg-secondary'
);

const toggleStatus = (member: Record<string, any>) => {
    member.pivot.status = member.pivot.status === 'active' ? 'inactive' : 'active';
};
</script>