<template>
    <div class="modal d-block" tabindex="-1" style="background: rgba(0,0,0,0.45);">
        <VForm @submit="submit">
            <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
                <div class="modal-content border-0 rounded-4">

                    <!-- Header -->
                    <div class="modal-header border-bottom-0 pb-0">
                        <h6 class="modal-title fw-bold">
                            <i class="bi bi-people me-2 text-teal"></i>
                            Manage Project Members
                        </h6>
                        <button type="button" class="btn-close" @click="emit('close')"></button>
                    </div>

                    <div class="modal-body pt-2">
                        <!-- Search + Filter tabs -->
                        <div class="input-group mb-2">
                            <span class="input-group-text bg-white border-end-0">
                                <i class="bi bi-search text-muted" style="font-size: 0.8rem;"></i>
                            </span>
                            <input v-model="search" type="text" class="form-control border-start-0" placeholder="Search members..."/>
                        </div>
                        <!-- Quick filter pills -->
                        <div class="d-flex gap-2 mb-3">
                            <button
                                v-for="tab in tabs" :key="tab.value" type="button" class="btn btn-sm rounded-pill" :class="activeTab === tab.value ? 'bg-teal text-white' : 'btn-light text-muted'" @click="activeTab = tab.value">
                                {{ tab.label }}
                                <span class="badge rounded-pill ms-1" :class="activeTab === tab.value ? 'bg-white text-teal' : 'bg-secondary text-white'" style="font-size: 0.65rem;">
                                    {{ tab.count }}
                                </span>
                            </button>
                        </div>
                        <!-- User List -->
                        <div class="user-list">
                            <div
                                v-for="user in filteredUsers" :key="user.id" class="user-row d-flex align-items-center gap-3 rounded-3 px-3 py-2 mb-2" :class="{ 'user-row--selected': selections[user.id].checked }" @click.stop="toggleUser(user.id)" style="cursor: pointer;">
                                <!-- Checkbox -->
                                <input class="form-check-input mt-0 flex-shrink-0" type="checkbox" :checked="selections[user.id].checked" @click.stop @change="toggleUser(user.id)" style="width: 1.1rem; height: 1.1rem; cursor: pointer;"/>

                                <!-- Avatar -->
                                <div class="flex-shrink-0 d-flex align-items-center justify-content-center rounded-circle v fw-semiboldv :style="{ background: avatarColor(user.name) }v style="width: 36px; height: 36px; font-size: 0.8rem;">
                                    {{ initials(user.name) }}
                                </div>

                                <!-- Name + Email -->
                                <div class="flex-grow-1 overflow-hidden">
                                    <div class="fw-semibold small text-truncate">{{ user.name }}</div>
                                    <div class="text-muted text-truncate" style="font-size: 0.72rem;">
                                        {{ user.email ?? '' }}
                                    </div>
                                </div>

                                <!-- Assigned badge -->
                                <span
                                    v-if="selections[user.id].checked" class="badge bg-teal-subtle text-teal border border-teal-subtle flex-shrink-0" style="font-size: 0.65rem;">
                                    Assigned
                                </span>

                                <!-- Role Select -->
                                <select v-model="selections[user.id].role" class="form-select form-select-sm flex-shrink-0" style="width: 108px;" :disabled="!selections[user.id].checked" @click.stop>
                                    <option value="member">Member</option>
                                    <option value="manager">Manager</option>
                                    <option value="owner">Owner</option>
                                </select>
                            </div>

                            <div v-if="filteredUsers.length === 0" class="text-center text-muted small py-4">
                                No users found.
                            </div>
                        </div>

                        <div v-if="formData.errors?.members" class="text-danger small mt-2">
                            {{ formData.errors.members }}
                        </div>
                    </div>

                    <!-- Footer -->
                    <div class="modal-footer border-top-0 pt-0">
                        <small class="text-muted me-auto">
                            {{ selectedCount }} member{{ selectedCount !== 1 ? 's' : '' }} selected
                        </small>
                        <button type="button" class="btn btn-light" @click="emit('close')">Cancel</button>
                        <SubmitButton label="Save Members" :processing="formData.processing" />
                    </div>

                </div>
            </div>
        </VForm>
    </div>
</template>

<script setup lang="ts">
import { ref, reactive, computed } from 'vue';
import { Form as VForm } from 'vee-validate';
import { useForm } from '@inertiajs/vue3';
import SubmitButton from '@/Components/Button/SubmitButton.vue';
import { Project } from  '@/types/interfaces/project';
import { User } from  '@/types/interfaces/user';

interface Selection {
    checked: boolean;
    role: 'member' | 'manager' | 'owner';
}

// ─── Props / Emits ────────────────────────────────────────────────────────────

const props = defineProps<{
    showMemebrsModal: boolean;
    project: Project;
    activeUsers: User[];
}>();

const emit = defineEmits<{
    close: [];
}>();

// ─── Existing members lookup ───────────────────────────────────────────────────

const existingMemberMap = computed<Record<number, string>>(() => {
    const map: Record<number, string> = {};
    (props.project.members ?? []).forEach((m) => {
        map[m.id] = m.pivot?.role ?? 'member';
    });
    return map;
});

// ─── Selection state ──────────────────────────────────────────────────────────

const selections = reactive<Record<number, Selection>>(
    Object.fromEntries(
        props.activeUsers.map((u) => [
            u.id,
            {
                checked: u.id in existingMemberMap.value,
                role: (existingMemberMap.value[u.id] as  'member' | 'manager' | 'owner') ?? 'member',
            },
        ])
    )
);

const toggleUser = (userId: number): void => {
    selections[userId].checked = !selections[userId].checked;
};

const selectedCount = computed<number>(() =>
    Object.values(selections).filter((s) => s.checked).length
);

// ─── Search + tabs ────────────────────────────────────────────────────────────

const search = ref('');
const activeTab = ref<'all' | 'assigned' | 'unassigned'>('all');

const assignedCount = computed(() =>
    props.activeUsers.filter((u) => selections[u.id]?.checked).length
);

const tabs = computed(() => [
    { label: 'All',        value: 'all'        as const, count: props.activeUsers.length },
    { label: 'Assigned',   value: 'assigned'   as const, count: assignedCount.value },
    { label: 'Unassigned', value: 'unassigned' as const, count: props.activeUsers.length - assignedCount.value },
]);

const filteredUsers = computed<User[]>(() => {
    let list = props.activeUsers;

    if (activeTab.value === 'assigned') {
        list = list.filter((u) => selections[u.id]?.checked);
    } else if (activeTab.value === 'unassigned') {
        list = list.filter((u) => !selections[u.id]?.checked);
    }

    const q = search.value.toLowerCase().trim();
    if (q) {
        list = list.filter(
            (u) =>
                u.name.toLowerCase().includes(q) ||
                (u.email ?? '').toLowerCase().includes(q)
        );
    }

    return list;
});

// ─── Helpers ──────────────────────────────────────────────────────────────────

const initials = (name: string): string =>
    name.split(' ').map((n) => n[0]).slice(0, 2).join('').toUpperCase();

const COLORS = ['#3B82F6', '#10B981', '#F59E0B', '#EF4444', '#8B5CF6', '#EC4899', '#0d9488', '#F97316'];
const avatarColor = (name: string): string => COLORS[name.charCodeAt(0) % COLORS.length];

// ─── Submit ───────────────────────────────────────────────────────────────────

const formData = useForm({} as { members: { id: number; role: string }[] });

const submit = (): void => {
    const members = Object.entries(selections)
        .filter(([, s]) => s.checked)
        .map(([id, s]) => ({ id: Number(id), role: s.role }));

    formData
        .transform(() => ({ members }))
        .put(route('projects.members.update', props.project.id), {
            preserveScroll: true,
            onSuccess: () => emit('close'),
        });
};
</script>