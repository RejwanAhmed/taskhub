<template>
    <Head title="Projects" />
    <AuthenticatedLayout>
        <template #header>
            <h2 class="h5 fw-semibold">Project Details</h2>
        </template>
        <div class="container-fluid">
            <!-- Project Info -->
            <div class="card border-0 shadow rounded-3 mb-3">
                <div class="card-body py-3 px-4">
                    <div class="d-flex justify-content-between align-items-start flex-wrap gap-2">
                        <div>
                            <div class="d-flex align-items-center gap-2 flex-wrap">
                                <h2 class="h5 fw-bold mb-0">
                                    {{ project.name }}
                                </h2>
                                <span class="badge" :class="statusColor(project.status)">
                                    {{ statusLabel(project.status) }}
                                </span>
                            </div>
                        </div>

                        <!-- Dates -->
                        <div class="d-flex align-items-center gap-4 small">
                            <div>
                                <span class="text-muted d-block">
                                    Start
                                </span>
                                <span class="fw-semibold">
                                    {{ project.start_date || '--' }}
                                </span>
                            </div>
                            <div>
                                <span class="text-muted d-block">
                                    End
                                </span>
                                <span class="fw-semibold">
                                    {{ project.end_date || '--' }}
                                </span>
                            </div>
                        </div>
                    </div>

                    <!-- Progress -->
                    <div class="mt-3">
                        <div class="d-flex justify-content-between mb-1">
                            <small class="text-muted">
                                {{ project.completed_tasks_count ?? 0 }} / {{ project.tasks_count ?? 0 }} tasks
                            </small>
                            <small class="fw-semibold" :style="{ color: progressColor(project) }">
                                {{ progressPct(project) }}%
                            </small>
                        </div>
                        <div class="progress" style="height: 5px;">
                            <div class="progress-bar" role="progressbar" :style="{ width: progressPct(project) + '%', background: progressColor(project)}">
                            </div>
                        </div>
                    </div>
                </div>
                <!-- <div class="project-accent" :style="{ background: project.color ?? '#0d9488' }"></div> -->
            </div>

            <div class="d-flex flex-wrap align-items-center gap-4 mb-4 mt-3">
                <div class="input-group" style="max-width: 300px; min-width: 160px;">
                    <span class="input-group-text bg-white border-end-0">
                        <i class="bi bi-search text-muted" style="font-size: 0.8rem;"></i>
                    </span>
                    <input v-model="search" type="text" class="form-control border-start-0" placeholder="Search tasks..." />
                </div>

                <select v-model="statusFilter" class="form-select" style="width: auto;">
                    <option value="">All statuses</option>
                    <option value="active">Active</option>
                    <option value="planning">Planning</option>
                    <option value="on_hold">On hold</option>
                    <option value="completed">Completed</option>
                </select>

                <button class="btn bg-teal text-white ms-auto" @click="showMemebrsModal = true">
                    <i class="bi bi-person-plus me-1"></i> Assign User
                </button>
            </div>
        </div>
        <div v-if="showMemebrsModal">
            <ManageProjectMembers
            :showMemebrsModal="showMemebrsModal"
            :project="project"
            :activeUsers="activeUsers"
            @close="showMemebrsModal = false"
        />
        </div>
    </AuthenticatedLayout>
</template>

<script setup lang="ts">
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head } from '@inertiajs/vue3';
import { ref } from 'vue';
import ManageProjectMembers from '@/Pages/Project/Modal/ManageProjectMembers.vue';
import { statusColor, statusLabel, progressPct, progressColor } from '@/Utils/projectStatus';
import { Project } from  '@/types/interfaces/project';
import { User } from  '@/types/interfaces/user';

const props = defineProps<{
    project: Project;
    activeUsers: User[];
}>();

const search = ref('');
const statusFilter = ref('');
const showMemebrsModal = ref(false);
</script>