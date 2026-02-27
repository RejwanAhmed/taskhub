<template>
    <div class="dropdown" @hide.bs.dropdown="orgSearch = ''">
        <button class="btn btn-light dropdown-toggle d-flex align-items-center gap-2"
            type="button" data-bs-toggle="dropdown" aria-expanded="false"
            data-bs-auto-close="outside">
            <i class="bi bi-building text-teal"></i>
            <span class="d-none d-sm-inline text-truncate" style="max-width: 130px;">
                {{ currentOrg?.name ?? 'Select Org' }}
            </span>
        </button>
        <ul class="dropdown-menu dropdown-menu-end shadow-sm border-0 p-0" style="min-width: 240px;">
            <!-- Header -->
            <li class="px-3 pt-3 pb-2">
                <h6 class="dropdown-header p-0 mb-2">Switch Organization</h6>
                <!-- Search -->
                <div class="input-group input-group-sm">
                    <span class="input-group-text bg-white"><i class="bi bi-search text-muted"></i></span>
                    <input type="text" class="form-control border-start-0" placeholder="Search..." v-model="orgSearch" @click.stop/>
                </div>
            </li>

            <li><hr class="dropdown-divider m-0" /></li>
            <!-- Scrollable org list -->
            <li>
                <ul class="list-unstyled mb-0 overflow-auto" style="max-height: 220px;">
                    <li v-for="org in filteredOrganizations" :key="org.id">
                        <Link class="dropdown-item d-flex align-items-center justify-content-between py-2" method="post"
                            :href="route('organizations.switch', org.id)" as="button" @click="closeDropdown">
                            <div class="d-flex align-items-center gap-2 overflow-hidden">
                                <i class="bi bi-building flex-shrink-0 text-muted"></i>
                                <span class="text-truncate">{{ org.name }}</span>
                            </div>
                            <i v-if="org.id === currentOrg?.id" class="bi bi-check2 text-teal ms-2 flex-shrink-0"></i>
                        </Link>
                    </li>
                    <li v-if="filteredOrganizations.length === 0">
                        <p class="text-muted small text-center py-3 mb-0">No organizations found</p>
                    </li>
                </ul>
            </li>

            <li><hr class="dropdown-divider m-0" /></li>

            <!-- Footer -->
            <li class="p-1">
                <Link class="dropdown-item rounded text-teal" :href="route('organizations.index')">
                    <i class="bi bi-grid me-2"></i> All Organizations
                </Link>
            </li>
        </ul>
    </div>
</template>

<script setup lang="ts">
import { computed, ref } from 'vue';
import { Link, usePage } from '@inertiajs/vue3';

const page = usePage();
const currentOrg = computed(() => page.props.currentOrg as any);
const activeOrganizations = computed(() => page.props.activeOrganizations as any[]);

const orgSearch = ref('');

const filteredOrganizations = computed(() => {
    if (!orgSearch.value.trim()) return activeOrganizations.value ?? [];
    const q = orgSearch.value.toLowerCase();
    return (activeOrganizations.value ?? []).filter((org: any) =>
        org.name.toLowerCase().includes(q)
    );
});


const closeDropdown = (event: Event) => {
    const dropdownMenu = (event.target as HTMLElement).closest('.dropdown-menu');
    if (dropdownMenu) {
        dropdownMenu.classList.remove('show');
        dropdownMenu.closest('.dropdown')?.querySelector('[data-bs-toggle="dropdown"]')?.removeAttribute('aria-expanded');
    }
};

</script>