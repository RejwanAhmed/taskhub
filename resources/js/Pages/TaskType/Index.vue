<template>
    <Head title="Task Types" />
    <AuthenticatedLayout>
        <template #header>
            <h2 class="h5 fw-semibold">Task Types</h2>
        </template>

        <div class="container-fluid">
            <div class="d-flex align-items-center justify-content-between mb-4">
                <small class="text-muted">Manage task types for your organization</small>
                <button class="btn bg-teal text-white" @click="openModal(null)">
                    <i class="bi bi-plus-lg me-1"></i> New Task Type
                </button>
            </div>

            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <AppDataTable :data="taskTypes" :columns="columns" search-placeholder="Search task types...">
                        <template #name="{ row }">
                            <div class="d-flex align-items-center gap-2">
                                <i v-if="row.icon" :class="['bi', row.icon]" :style="{ color: row.color }"></i>
                                <i v-else class="bi bi-tag text-muted"></i>
                                <strong>{{ row.name }}</strong>
                            </div>
                        </template>

                        <template #color="{ row }">
                            <div class="d-flex align-items-center gap-2">
                                <div class="rounded-circle" :style="{ background: row.color, width: '16px', height: '16px' }"></div>
                                <span class="small text-muted">{{ row.color }}</span>
                            </div>
                        </template>

                        <template #is_default="{ row }">
                            <span v-if="row.is_default" class="badge bg-teal">Default</span>
                            <span v-else class="text-muted small">—</span>
                        </template>

                       <template #actions="{ row }">
                            <button class="btn" @click="openModal(row as TaskType)">
                                <i class="bi bi-pencil text-teal"></i>
                            </button>
                            <DeleteConfirmationButton
                                confirm-route="task-types.destroy"
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
            <CreateTaskTypeModal
                :taskType="selectedType"
                @close="showModal = false"
            />
        </div>
    </AuthenticatedLayout>
</template>

<script setup lang="ts">
import { ref } from 'vue';
import { Head } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import AppDataTable from '@/Components/Table/DataTable.vue';
import CreateTaskTypeModal from '@/Pages/TaskType/Modal/CreateTaskTypeModal.vue';
import { TaskType } from '@/types/interfaces/taskType';
import DeleteConfirmationButton from '@/Components/Button/DeleteConfirmationButton.vue';

const props = defineProps<{
    taskTypes: TaskType[];
}>();

const showModal = ref(false);
const selectedType = ref<TaskType | null>(null);

const columns = [
    { key: 'name',       label: 'Name',    sortable: true  },
    { key: 'color',      label: 'Color',   sortable: false },
    { key: 'is_default', label: 'Default', sortable: false },
    { key: 'actions',    label: 'Actions', sortable: false, searchable: false },
];

const openModal = (type: TaskType | null) => {
    selectedType.value = type;
    showModal.value = true;
};
</script>