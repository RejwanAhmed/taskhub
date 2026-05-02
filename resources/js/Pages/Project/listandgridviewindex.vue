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
                            <p class="fw-bold fs-4 mb-0" :style="{ color: stat.accent }">{{ stat.value }}</p>
                            <p class="text-muted mb-0" style="font-size: 0.72rem;">{{ stat.sub }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- ── Toolbar ──────────────────────────────────────── -->
            <div class="d-flex flex-wrap align-items-center gap-2 mb-4">
                <div v-if="view === 'grid'" class="input-group" style="max-width: 300px; min-width: 160px;">
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

                <select v-model="sortBy" class="form-select" style="width: auto;">
                    <option value="name">Sort: Name</option>
                    <option value="tasks_count">Sort: Tasks</option>
                    <option value="progress">Sort: Progress</option>
                    <option value="start_date">Sort: Start date</option>
                    <option value="end_date">Sort: End date</option>
                </select>

                <div class="btn-group">
                    <button type="button" class="btn" :class="view === 'grid' ? 'bg-teal text-white' : 'btn-outline-secondary'" @click="view = 'grid'" title="Grid view">
                        <i class="bi bi-grid-3x3-gap"></i>
                    </button>
                    <button type="button" class="btn" :class="view === 'list' ? 'bg-teal text-white' : 'btn-outline-secondary'" @click="view = 'list'" title="List view">
                        <i class="bi bi-list-ul"></i>
                    </button>
                </div>

                <button class="btn bg-teal text-white ms-auto" @click="openProjectModal(null)">
                    <i class="bi bi-plus-lg me-1"></i> New Project
                </button>
            </div>

            <!-- ── Empty state ──────────────────────────────────── -->
            <div v-if="filtered.length === 0" class="text-center py-5 text-muted">
                <i class="bi bi-kanban fs-1 d-block mb-2 text-teal"></i>
                <p class="mb-1 fw-semibold">No projects found</p>
                <small>Try adjusting your search or filters</small>
            </div>

            <!-- ══ GRID VIEW ════════════════════════════════════════ -->
            <div v-else-if="view === 'grid'" class="row g-3">
                <div v-for="project in filtered" :key="project.id" class="col-sm-6 col-lg-4 col-xl-3">

                    <div class="card border-0 shadow-sm h-100 project-card" @click="goToProject(project)">

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
                                            <DeleteConfirmationButton
                                                confirm-route="projects.destroy"
                                                :obj="project"
                                                :delete-content="project.name" />
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

            <!-- ══ LIST VIEW ════════════════════════════════════════ -->
            <div v-else class="card border-0 shadow-sm">
    <div class="card-body">
        <AppDataTable
            :data="filtered"
            :columns="listColumns"
            search-placeholder="Search projects...">

            <!-- Name + description -->
            <template #name="{ row }">
                <p class="fw-semibold mb-0" style="cursor:pointer;" @click="goToProject(row)">
                    {{ row.name }}
                </p>
            </template>

            <!-- Status badge -->
            <template #status="{ row }">
                <span class="badge fw-normal" :class="statusColor(row.status)">
                    {{ statusLabel(row.status) }}
                </span>
            </template>

            <!-- Progress bar -->
            <template #progress="{ row }">
                <div class="d-flex align-items-center gap-2" style="min-width: 110px;">
                    <div class="progress flex-grow-1" style="height: 5px;">
                        <div
                            class="progress-bar"
                            :style="{
                                width: progressPct(row) + '%',
                                background: progressColor(row)
                            }">
                        </div>
                    </div>
                    <small class="text-muted" style="min-width: 28px; font-size: 0.72rem;">
                        {{ progressPct(row) }}%
                    </small>
                </div>
            </template>

            <!-- Tasks done/total -->
            <template #tasks="{ row }">
                <small class="text-muted">
                    {{ row.completed_tasks_count ?? 0 }}/{{ row.tasks_count ?? 0 }}
                </small>
            </template>

            <!-- Members count -->
            <template #members="{ row }">
                <span class="badge bg-light text-muted fw-normal">
                    <i class="bi bi-people me-1"></i>{{ row.members_count ?? 0 }}
                </span>
            </template>

            <!-- Start date -->
            <template #start_date="{ row }">
                <small class="text-muted">
                    {{ row.start_date ? formatDate(row.start_date) : '—' }}
                </small>
            </template>

            <!-- End date — red if overdue -->
            <template #end_date="{ row }">
                <small :class="isOverdue(row) ? 'text-danger fw-semibold' : 'text-muted'">
                    {{ row.end_date ? formatDate(row.end_date) : '—' }}
                    <i v-if="isOverdue(row)" class="bi bi-exclamation-circle ms-1"></i>
                </small>
            </template>

            <!-- Actions -->
            <template #actions="{ row }">
                <div class="dropdown" @click.stop>
                    <button class="btn btn-sm btn-light" data-bs-toggle="dropdown">
                        <i class="bi bi-three-dots-vertical"></i>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end shadow-sm border-0">
                        <li>
                            <button class="dropdown-item" @click="openProjectModal(row)">
                                <i class="bi bi-pencil me-2 text-teal"></i> Edit
                            </button>
                        </li>
                        <li><hr class="dropdown-divider" /></li>
                        <li>
                            <DeleteConfirmationButton
                                confirm-route="projects.destroy"
                                :obj="row"
                                :delete-content="row.name" />
                        </li>
                    </ul>
                </div>
            </template>

        </AppDataTable>
    </div>
</div>

        </div>

        <!-- Modal -->
        <!-- <div v-if="showModal" class="modal d-block modal-background">
            <CreateProjectModal
                :show-modal="showModal"
                :project="selectedProject"
                @close="showModal = false" />
        </div> -->

    </AuthenticatedLayout>
</template>

<script setup lang="ts">
import { computed, ref } from 'vue';
import { Head, router } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import DeleteConfirmationButton from '@/Components/Button/DeleteConfirmationButton.vue';
// import CreateProjectModal from './Modal/CreateProjectModal.vue';
import AppDataTable from '@/Components/Table/DataTable.vue';
// ─── Props ────────────────────────────────────────────────────────────────────
const props = defineProps<{
    projects: Record<string, any>[];
}>();

// ─── State ────────────────────────────────────────────────────────────────────
const view          = ref<'grid' | 'list'>('grid');
const search        = ref('');
const statusFilter  = ref('');
const sortBy        = ref('name');
const showModal     = ref(false);
const selectedProject = ref<any | null>(null);

// ─── Helpers ──────────────────────────────────────────────────────────────────
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

const formatDate = (d: string): string =>
    new Date(d).toLocaleDateString('en-GB', { day: 'numeric', month: 'short', year: '2-digit' });

const statusLabel = (s: string): string =>
    ({ active: 'Active', planning: 'Planning', on_hold: 'On hold', completed: 'Completed' }[s] ?? s);

const statusColor = (s: string): string =>
    ({
        active:    'bg-success',
        planning:  'bg-info text-dark',
        on_hold:   'bg-warning text-dark',
        completed: 'bg-secondary',
    }[s] ?? 'bg-secondary');

// ─── Stats ────────────────────────────────────────────────────────────────────
const stats = computed(() => {
    const list = props.projects ?? [];
    return [
        { label: 'Total projects', value: list.length,                                        sub: 'in this organization', accent: '#0d9488' },
        { label: 'Active',         value: list.filter(p => p.status === 'active').length,    sub: 'in progress',          accent: '#0d9488' },
        { label: 'Completed',      value: list.filter(p => p.status === 'completed').length, sub: 'finished',             accent: '#10b981' },
        { label: 'On hold / Planning', value: list.filter(p => ['on_hold','planning'].includes(p.status)).length, sub: 'not yet active', accent: '#f59e0b' },
    ];
});

// ─── Filtered + sorted ───────────────────────────────────────────────────────
const filtered = computed(() => {
    const list = props.projects ?? [];
    const q = search.value.toLowerCase();

    let result = list.filter(p =>
        (!q || p.name.toLowerCase().includes(q) || p.description?.toLowerCase().includes(q)) &&
        (!statusFilter.value || p.status === statusFilter.value)
    );

    const sorters: Record<string, (a: any, b: any) => number> = {
        name:        (a, b) => a.name.localeCompare(b.name),
        tasks_count: (a, b) => (b.tasks_count ?? 0) - (a.tasks_count ?? 0),
        progress:    (a, b) => progressPct(b) - progressPct(a),
        start_date:  (a, b) => new Date(a.start_date ?? 0).getTime() - new Date(b.start_date ?? 0).getTime(),
        end_date:    (a, b) => new Date(a.end_date ?? 0).getTime() - new Date(b.end_date ?? 0).getTime(),
    };

    return [...result].sort(sorters[sortBy.value] ?? sorters.name);
});

// ─── Actions ──────────────────────────────────────────────────────────────────
const openProjectModal = (project: any | null) => {
    selectedProject.value = project;
    showModal.value = true;
};

const goToProject = (project: any) => {
    router.visit(`/projects/${project.id}`);
};

const listColumns = [
    { key: 'color_dot', label: '',         sortable: false, searchable: false },
    { key: 'name',      label: 'Project',  sortable: true  },
    { key: 'status',    label: 'Status',   sortable: true  },
    { key: 'progress',  label: 'Progress', sortable: false, searchable: false },
    { key: 'tasks',     label: 'Tasks',    sortable: false, searchable: false },
    { key: 'members',   label: 'Members',  sortable: false, searchable: false },
    { key: 'start_date',label: 'Start',    sortable: true  },
    { key: 'end_date',  label: 'End',      sortable: true  },
    { key: 'actions',   label: '',         sortable: false, searchable: false },
];
</script>

<style scoped>

</style>