<template>
    <Head title="Projects" />
    <AuthenticatedLayout>
        <template #header>
            <h2 class="h5 fw-semibold">Projects</h2>
        </template>
        <div class="container-fluid">
            <!-- ── Stats cards ──────────────────────────────────── -->
            <div class="row g-3 mb-4">
                <div class="col-6 col-md-3" v-for="stat in stats" :key="stat.label">
                    <div class="card border-0 shadow-sm h-100">
                        <div class="card-body shadow py-3">
                            <p class="text-muted small mb-1">{{ stat.label }}</p>
                            <p class="fw-bold fs-4 mb-0" :style="{ color: stat.color }">{{ stat.value }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="d-flex flex-wrap align-items-center gap-2 mb-4">
                <div class="input-group" style="max-width: 300px; min-width: 160px;">
                    <span class="input-group-text bg-white border-end-0">
                        <i class="bi bi-search text-muted" style="font-size: 0.8rem;"></i>
                    </span>
                    <input v-model="search" type="text" class="form-control border-start-0" placeholder="Search projects..." />
                </div>

                <select v-model="statusFilter" class="form-select" style="width: auto;">
                    <option value="">All statuses</option>
                    <option value="active">Active</option>
                    <option value="planning">Planning</option>
                    <option value="on_hold">On hold</option>
                    <option value="completed">Completed</option>
                </select>

                <button class="btn bg-teal text-white ms-auto" @click="openProjectModal(null)">
                    <i class="bi bi-plus-lg me-1"></i> New Project
                </button>
            </div>
            
            <!-- Start: Grid View -->
            <!-- Empty state -->
            <div v-if="filtered.length === 0" class="text-center py-5 text-muted">
                <i class="bi bi-kanban fs-1 d-block mb-2 text-teal"></i>
                <p class="mb-1 fw-semibold">No projects found</p>
                <small>Try adjusting your search or filters</small>
            </div>

            <div v-else class="row g-3">
                <div v-for="project in filtered" :key="project.id" class="col-sm-6 col-lg-4 col-xl-3">
                    <div class="card border-0 shadow-sm h-100 project-card" @click="projectDetails(project)">
                        <!-- Color accent top bar -->
                        <div class="project-accent" :style="{ background: project.color ?? '#0d9488' }"></div>

                        <div class="card-body d-flex flex-column">
                            <!-- Name + menu -->
                            <div class="d-flex justify-content-between align-items-start mb-1">
                                <h6 class="fw-bold mb-0 me-2" style="line-height: 1.3;">
                                    {{ project.name }}
                                </h6>
                                <div class="dropdown" @click.stop>
                                    <button class="btn btn-sm btn-light px-1 py-0" data-bs-toggle="dropdown">
                                        <i class="bi bi-three-dots-vertical"></i>
                                    </button>
                                    <ul class="dropdown-menu dropdown-menu-end shadow-sm border-0">
                                        <li>
                                            <button class="dropdown-item" @click="openProjectModal(project)">
                                                <i class="bi bi-pencil me-2 text-teal"></i> Edit
                                            </button>
                                        </li>
                                        <li><hr class="dropdown-divider" /></li>
                                        <li>
                                            <DeleteConfirmationButton confirm-route="projects.destroy" :obj="project" :delete-content="project.name" />
                                        </li>
                                    </ul>
                                </div>
                            </div>

                            <!-- Description -->
                            <p class="text-muted small mb-3 project-desc">
                                {{ project.description }}
                            </p>

                            <!-- Progress -->
                            <div class="mb-3">
                                <div class="d-flex justify-content-between mb-1">
                                    <small class="text-muted">
                                        {{ project.completed_tasks_count ?? 0 }} / {{ project.tasks_count ?? 0 }} tasks
                                    </small>
                                    <small class="fw-semibold" :style="{ color: progressColor(project) }">
                                        {{ progressPct(project) }}%
                                    </small>
                                </div>
                                <div class="progress" style="height: 5px;">
                                    <div
                                        class="progress-bar"
                                        role="progressbar"
                                        :style="{
                                            width: progressPct(project) + '%',
                                            background: progressColor(project)
                                        }">
                                    </div>
                                </div>
                            </div>

                            <!-- Footer -->
                            <div class="mt-auto d-flex align-items-center justify-content-between">

                                <!-- Stacked member avatars -->
                                <div class="avatar-stack">
                                    <template v-if="project.members_count > 0">
                                        <div
                                            v-for="n in Math.min(project.members_count, 3)"
                                            :key="n"
                                            class="avatar-circle text-white"
                                            :style="{ background: project.color ?? '#0d9488' }"
                                            style="width: 24px; height: 24px; font-size: 0.68rem; border: 2px solid white; margin-left: -6px;"
                                            :style-first="{ marginLeft: 0 }">
                                            {{ String.fromCharCode(64 + n) }}
                                        </div>
                                        <div
                                            v-if="project.members_count > 3"
                                            class="avatar-circle bg-secondary text-white"
                                            style="width: 24px; height: 24px; font-size: 0.62rem; border: 2px solid white; margin-left: -6px;">
                                            +{{ project.members_count - 3 }}
                                        </div>
                                    </template>
                                    <small v-else class="text-muted" style="font-size: 0.7rem;">No members</small>
                                </div>

                                <div class="d-flex align-items-center gap-2">
                                    <!-- End date -->
                                    <small v-if="project.end_date" class="text-muted" style="font-size: 0.7rem;">
                                        <i class="bi bi-calendar3 me-1"></i>{{ formatDate(project.end_date) }}
                                    </small>
                                    <!-- Status -->
                                    <span class="badge fw-normal" :class="statusColor(project.status)">
                                        {{ statusLabel(project.status) }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- End: Grid View -->
        </div>
    </AuthenticatedLayout>
</template>
<script setup lang="ts">
import { computed, ref } from 'vue';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import DeleteConfirmationButton from '@/Components/Button/DeleteConfirmationButton.vue';

const props = defineProps<{
    projects: Record<string, any>[],
}>()

const search = ref('');
const statusFilter = ref('');
const showModal     = ref(false);
const selectedProject = ref<any | null>(null);

// Helpers
const progressPct = (p: any): number => {
    if (!p.tasks_count) return 0;
    return Math.round(((p.completed_tasks_count ?? 0) / p.tasks_count) * 100);
};

const progressColor = (p: any): string => {
    if (isOverdue(p)) return '#ef4444';
    if (progressPct(p) === 100) return '#10b981';
    return p.color ?? '#0d9488';
};

const isOverdue = (p: any): boolean => {
    if (!p.end_date || p.status === 'completed') return false;
    return new Date(p.end_date) < new Date();
};

const formatDate = (d: string) => {
    return new Date(d).toLocaleDateString('en-GB', { day: 'numeric', month: 'short', year: '2-digit' });
}

const statusLabel = (s: string) => {
    return ({ active: 'Active', planning: 'Planning', on_hold: 'On hold', completed: 'Completed' }[s] ?? s);
}

const statusColor = (s: string) => { 
    return ({
        active:    'bg-success',
        planning:  'bg-info text-dark',
        on_hold:   'bg-warning text-dark',
        completed: 'bg-secondary',
    }[s] ?? 'bg-secondary');
}

const stats = computed(() => {
    const list = props.projects ?? [];
    return [
        {label: 'Total Projects', value: list.length, color: '#0d9488'},
        {label: 'Active Projects', value: list.filter(project => project.status == 'active').length, color: '#0d9488'},
        {label: 'Completed Projects', value: list.filter(project => project.status == 'completed').length, color: '#10b981'},
        {label: 'On hold / Planning', value: list.filter(project => project.status == 'on_hold' || project.status == 'planning').length, color: '#f59e0b'},
    ]
});

const filtered = computed(() => {
    const list = props.projects ?? [];
    const searchValue = search.value.toLowerCase();

    let result = list.filter(project => 
    (!searchValue || project.name.toLowerCase().includes(searchValue) || project.description?.toLowerCase().includes(searchValue)) && 
    (!statusFilter.value || project.status == statusFilter.value));

    return [...result];
});

const projectDetails = (project: any) => {
    
};

const openProjectModal = (project: any | null) => {
    selectedProject.value = project;
    showModal.value = true;
};
</script>