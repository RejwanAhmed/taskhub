<template>
    <Head title="Tags" />
    <AuthenticatedLayout>
        <template #header>
            <h2 class="h5 fw-semibold">Tags</h2>
        </template>

        <div class="container-fluid">
            <div class="d-flex align-items-center justify-content-between mb-4">
                <small class="text-muted">Manage tags for your organization</small>
                <button class="btn bg-teal text-white" @click="openModal(null)">
                    <i class="bi bi-plus-lg me-1"></i> New Tag
                </button>
            </div>

            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <AppDataTable :data="tags" :columns="columns" search-placeholder="Search tags...">

                        <template #name="{ row }">
                            <div class="d-flex align-items-center gap-2">
                                <div class="rounded-circle" :style="{ background: row.color, width: '12px', height: '12px' }"></div>
                                <strong>{{ row.name }}</strong>
                            </div>
                        </template>

                        <template #color="{ row }">
                            <div class="d-flex align-items-center gap-2">
                                <div class="rounded-circle" :style="{ background: row.color, width: '16px', height: '16px' }"></div>
                                <span class="small text-muted">{{ row.color }}</span>
                            </div>
                        </template>

                        <template #description="{ row }">
                            <span class="small text-muted">{{ row.description ?? '—' }}</span>
                        </template>

                        <template #actions="{ row }">
                            <button class="btn" @click="openModal(row as Tag)">
                                <i class="bi bi-pencil text-teal"></i>
                            </button>
                            <DeleteConfirmationButton
                                confirm-route="tags.destroy"
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
            <CreateTagModal
                :tag="selectedTag"
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
import CreateTagModal from '@/Pages/Tag/Modal/CreateTagModal.vue';
import DeleteConfirmationButton from '@/Components/Button/DeleteConfirmationButton.vue';
import { Tag } from '@/types/interfaces/tag';

const props = defineProps<{
    tags: Tag[];
}>();

const showModal = ref(false);
const selectedTag = ref<Tag | null>(null);

const columns = [
    { key: 'name',        label: 'Name',        sortable: true  },
    { key: 'color',       label: 'Color',       sortable: false },
    { key: 'description', label: 'Description', sortable: false },
    { key: 'actions',     label: 'Actions',     sortable: false, searchable: false },
];

const openModal = (tag: Tag | null) => {
    selectedTag.value = tag;
    showModal.value = true;
};
</script>