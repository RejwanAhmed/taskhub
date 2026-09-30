<template>
    <div class="d-flex vh-100 overflow-hidden" style="min-height: 100dvh;">
        <!-- Sidebar -->
        <div :class="['sidebar', collapsed ? 'collapsed' : 'expanded', 'bg-teal', 'd-flex', 'flex-column', 'pt-3', 'position-sticky', 'top-0', 'start-0']"
            style="flex-shrink: 0;">
            <ul class="nav nav-pills flex-column">
                <!-- Regular menu items -->
                <li class="nav-item" v-for="item in menuItems" :key="item.name">
                    <Link :href="item.route" class="nav-link fw-bold text-white">
                        <i :class="['bi', item.icon, 'me-2']"></i>
                        <span class="link-text">{{ item.name }}</span>
                    </Link>
                </li>

                <!-- Settings expandable -->
                <li class="nav-item">
                    <button class="nav-link fw-bold text-white w-100 text-start border-0 bg-transparent"
                        @click="settingsOpen = !settingsOpen">
                        <i class="bi bi-gear me-2"></i>
                        <span class="link-text">
                            Settings
                            <i :class="['bi', 'ms-1', settingsOpen ? 'bi-chevron-up' : 'bi-chevron-down']"></i>
                        </span>
                    </button>

                    <!-- Sub items -->
                    <ul v-if="settingsOpen && !collapsed" class="nav nav-pills flex-column ps-3">
                        <li class="nav-item" v-for="item in settingsItems" :key="item.name">
                            <Link :href="item.route" class="nav-link fw-bold text-white">
                                <i :class="['bi', item.icon, 'me-2']"></i>
                                <span class="link-text">{{ item.name }}</span>
                            </Link>
                        </li>
                    </ul>
                </li>
            </ul>
        </div>

        <!-- Right side (top bar + content) -->
        <div class="d-flex flex-column flex-grow-1 overflow-hidden">
            <!-- Top bar -->
            <nav class="navbar navbar-expand navbar-light bg-light px-3 d-flex justify-content-between"
                style="height: 56px;">
                <!-- Left: toggle button -->
                <button class="btn btn-light" @click="toggleSidebar" aria-label="Toggle sidebar">
                    <i class="bi bi-list fs-4"></i>
                </button>

                <!-- Right: org switcher + profile -->
                <div class="d-flex align-items-center gap-2">
                    <!-- Org Switcher -->
                    <OrganizationSwitcher></OrganizationSwitcher>

                    <!-- Profile dropdown -->
                    <div class="dropdown">
                        <button class="btn btn-light dropdown-toggle d-flex align-items-center" type="button"
                            id="userMenu" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="bi bi-person-circle fs-4 me-2"></i>
                            <span class="d-none d-sm-inline">Profile</span>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="userMenu">
                            <li>
                                <Link class="dropdown-item" :href="route('profile.edit')">
                                    <i class="bi bi-person-lines-fill me-2"></i> Profile
                                </Link>
                            </li>
                            <li>
                                <Link class="dropdown-item" method="post" :href="route('logout')" as="button">
                                    <i class="bi bi-box-arrow-right me-2"></i> Logout
                                </Link>
                            </li>
                        </ul>
                    </div>
                </div>
            </nav>

            <!-- Rendered content -->
            <main class="flex-grow-1 p-3 overflow-auto" style="overflow-x: hidden;">
                <header class="mb-4">
                    <slot name="header" />
                </header>

                <slot />
            </main>
        </div>
    </div>
</template>

<script setup lang="ts">
import { onMounted, onUnmounted, computed, ref } from 'vue';
import { Link, usePage } from '@inertiajs/vue3';
import { useUiStore } from '@/Stores/ui';
import OrganizationSwitcher from '@/Components/Button/OrganizationSwitcher.vue';

const uiStore = useUiStore();

// const collapsed = ref(false);
const collapsed = computed({
    get: () => uiStore.sidebarCollapsed,
    set: (val: boolean) => uiStore.setCollapsed(val),
});

const toggleSidebar = () => {
    collapsed.value = !collapsed.value;
};

// Auto-collapse on small devices
const handleResize = () => {
    const stored = localStorage.getItem('sidebarCollapsed');
    if (stored === null) {
        collapsed.value = window.innerWidth < 768;
    }
};

onMounted(() => {
    handleResize();
    window.addEventListener('resize', handleResize);
});

onUnmounted(() => {
    window.removeEventListener('resize', handleResize);
});

const settingsOpen = ref(false);

const menuItems = [
    { name: 'Dashboard', icon: 'bi-speedometer2', route: '/dashboard' },
    { name: 'Organization', icon: 'bi-building', route: '/organizations' },
    { name: 'Members', icon: 'bi-people', route: '/members' },
    { name: 'Projects', icon: 'bi-kanban', route: '/projects' },
    { name: 'My Tasks', icon: 'bi-check2-square', route: '/my-tasks' },
    { name: 'Reports', icon: 'bi-bar-chart-line', route: '/reports' },
    { name: 'Notifications', icon: 'bi-bell', route: '/notifications' },
];

const settingsItems = [
    { name: 'Task Types', icon: 'bi-tag', route: '/task-types' },
    { name: 'Tags', icon: 'bi-tags', route: '/tags' }
];
</script>

<style scoped>
.sidebar {
    height: 100dvh;
    transition: width 0.3s ease;
}

.sidebar.collapsed {
    width: 65px;
}

.sidebar.expanded {
    width: 190px;
}

.sidebar .nav-link {
    white-space: nowrap;
    color: #ffffff;
    /* white default */
    transition: color 0.3s ease;
}

.sidebar.collapsed .link-text {
    display: none;
}

.sidebar ::v-deep .nav-link:hover {
    color: #2b2a2a !important;
    text-decoration: none !important;
}
</style>
