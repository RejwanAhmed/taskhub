<template>
    <GuestLayout>
        <Head title="Invitation" />
        <div class="container d-flex justify-content-center align-items-center min-vh-100">
            <div class="col-md-6 col-lg-5">
                <div class="card border-0 shadow-sm">

                    <!-- Top accent strip -->
                    <div class="invite-accent-bar"></div>

                    <div class="card-body p-4">

                        <!-- Icon -->
                        <div class="text-center mb-3">
                            <div class="invite-icon-wrap mx-auto">
                                <i class="bi bi-envelope-paper text-teal fs-3"></i>
                            </div>
                        </div>

                        <!-- Title -->
                        <h3 class="card-title text-center mb-1">Organization Invitation</h3>
                        <p class="text-muted text-center small mb-4">
                            You have been invited to join a team. Review the details below.
                        </p>

                        <!-- Details -->
                        <div class="invite-details mb-4">
                            <div class="invite-detail-row">
                                <span class="invite-detail-label">
                                    <i class="bi bi-building me-1"></i> Company
                                </span>
                                <span class="text-end">
                                    {{ props.invitation.organization.name }}
                                </span>
                            </div>
                            <div class="invite-detail-row">
                                <span class="invite-detail-label">
                                    <i class="bi bi-shield-check me-1"></i> Role
                                </span>
                                <span class="badge invite-role-badge" :class="roleBadge(props.invitation.role)">
                                    {{ role }}
                                </span>
                            </div>

                            <div class="invite-detail-row">
                                <span class="invite-detail-label">
                                    <i class="bi bi-person me-1"></i> Invited by
                                </span>
                                <div class="text-end">
                                    <div class="fw-medium text-dark small">{{ props.invitation.inviter.name }}</div>
                                    <div class="text-muted" style="font-size: 0.78rem;">{{ props.invitation.inviter.email }}</div>
                                </div>
                            </div>

                            <div class="invite-detail-row">
                                <span class="invite-detail-label">
                                    <i class="bi bi-calendar me-1"></i> Sent at
                                </span>
                                <span class="small text-dark">{{ formatDate(props.invitation.created_at) }}</span>
                            </div>

                            <div class="invite-detail-row">
                                <span class="invite-detail-label">
                                    <i class="bi bi-clock me-1"></i> Expires at
                                </span>
                                <span class="small text-danger fw-medium">{{ formatDate(props.invitation.expires_at) }}</span>
                            </div>
                        </div>

                        <!-- Actions -->
                        <div class="d-grid gap-2">
                            <button class="btn btn-primary bg-teal border-0" @click="handleAccept" :disabled="isProcessing">
                                <i class="bi bi-check-lg me-2"></i>Accept Invitation
                            </button>
                        </div>
                    </div>
                </div>

                <p class="text-center text-muted mt-3" style="font-size: 0.75rem;">
                    This invitation link is private. Please do not share it. 
                </p>
            </div>
        </div>
    </GuestLayout>
</template>

<script setup lang="ts">
import GuestLayout from '@/Layouts/GuestLayout.vue';
import { Head, router } from '@inertiajs/vue3';
import { computed, ref } from 'vue';
import { Invitation } from '@/types/interfaces/invitation';
import { useDateFormatter } from '@/types/interfaces/dateFormatter';

const props = defineProps<{
    token: String,
    invitation: Invitation,
}>()

const isProcessing = ref(false);
const { formatDate } = useDateFormatter();
const roleBadge = (role: string) => ({
    manager: 'bg-info text-dark',
    member:  'bg-secondary',
}[role] ?? 'bg-secondary');

const role = computed(() => {
    const r = props.invitation.role;
    return r.charAt(0).toUpperCase() + r.slice(1);
});

const handleAccept = () => {
    router.post(route('invitations.accept', {token: props.token}),
        {},
        { onFinish: () => {isProcessing.value = false}}
    )
}
</script>