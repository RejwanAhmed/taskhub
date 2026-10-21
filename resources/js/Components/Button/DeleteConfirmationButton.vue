<template>
    <button
        :class="iconOnly ? 'btn text-danger' : 'dropdown-item text-danger'"
        @click="showDeleteConfirmation"
    >
        <i class="bi bi-trash" :class="{ 'me-2': !iconOnly }"></i>
        <span v-if="!iconOnly">{{ loading ? 'Deleting...' : 'Delete' }}</span>
    </button>
</template>

<script setup lang="ts">
import Swal from 'sweetalert2';
import { router } from '@inertiajs/vue3';
import { ref } from 'vue';
import axios from 'axios';
import { toast } from '@/Utils/toast';
import { handleAxiosError } from '@/Utils/handleAxiosError';


const props = defineProps<{
    confirmRoute: string,
    obj: Record<string, any>,
    deleteContent: string,
    iconOnly?: boolean,
    routeParams?: any,
    useAxios?: boolean;
}> ();

const emit = defineEmits<{
    deleted: [];
}>();

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
            if (props.useAxios) {
                deleteViaAxios();
            } else {
                deleteViaInertia();
            }
        }
    });
}

const deleteViaInertia = () => {
    loading.value = true;
    router.visit(route(props.confirmRoute, props.routeParams ?? props.obj?.id), {
        method: 'delete',
        data: {},
        replace: true,
        preserveScroll: true,
        onFinish: () => {
            loading.value = false;
        }
    });
};

const deleteViaAxios = async () => {
    loading.value = true;
    try {
        const response = await axios.delete(
            route(props.confirmRoute, props.routeParams ?? props.obj?.id)
        );
        toast.success(response.data.success);
        emit('deleted');
    } catch (e: any) {
        handleAxiosError(e);
    } finally {
        loading.value = false;
    }
};
</script>