<template>
    <button class="dropdown-item text-danger" @click="showDeleteConfirmation">
        <i class="bi bi-trash me-2"></i> {{ loading ? 'Deleting...' : 'Delete' }}
    </button>
</template>

<script setup lang="ts">
import Swal from 'sweetalert2';
import { router } from '@inertiajs/vue3';
import { ref } from 'vue';

const props = defineProps<{
    confirmRoute: string,
    obj: Record<string, any>,
    deleteContent: string,
}> ()

const loading = ref(false);

const showDeleteConfirmation = () => {
    Swal.fire({
        title: `Are you sure want to delete this ${props?.deleteContent} ?`,
        text: "You won't be able to revert this!",
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#0d9488",
        cancelButtonColor: "#d33",
        confirmButtonText: "Delete"
    }).then((result) => {
        if (result.isConfirmed) {
            loading.value = true
            router.visit(route(props.confirmRoute, props.obj?.id), {
                method: 'delete',
                data: {},
                replace: true,
                preserveScroll: true,

                onFinish: () => {
                    loading.value = false
                }
            })
        }
    });
}
</script>