<template>
    <div class="modal d-block modal-background">
        <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
            <div class="modal-content border-0 rounded-4">

                <!-- Header -->
                <div class="modal-header border-bottom-0 pb-0">
                    <div>
                        <div v-if="parentTaskTitle" class="text-muted mb-1" style="font-size:0.75rem;">
                            <i class="bi bi-diagram-2 me-1"></i>
                            {{ parentTaskTitle }} <i class="bi bi-chevron-right mx-1"></i> {{ task.title }}
                        </div>
                        <h6 class="modal-title fw-bold">
                            <i class="bi bi-paperclip me-2 text-teal"></i>
                            {{ parentTaskTitle ? 'Subtask Attachments' : 'Task Attachments' }}
                        </h6>
                        <small class="text-muted">{{ task.title }}</small>
                    </div>
                    <button type="button" class="btn-close" @click="emit('close')"></button>
                </div>

                <div class="modal-body">
                    <!-- Loading -->
                    <div v-if="loading" class="text-center py-4">
                        <div class="spinner-border spinner-border-sm text-teal" role="status"></div>
                    </div>

                    <div v-else>
                        <!-- Empty state -->
                        <div v-if="attachments.length === 0" class="text-center text-muted py-4" style="font-size:0.85rem;">
                            <i class="bi bi-paperclip d-block fs-3 mb-2 text-teal"></i>
                            No attachments yet.
                        </div>

                        <!-- Attachment list -->
                        <div v-else class="d-flex flex-column gap-2 mb-3">
                            <div v-for="attachment in attachments" :key="attachment.id" class="attachment-row d-flex align-items-center gap-3 p-2 rounded-3">
                                <!-- Icon -->
                                <i :class="fileIconFromMime(attachment.mime_type)" class="text-teal fs-5 flex-shrink-0"></i>

                                <!-- File info -->
                                <div class="flex-grow-1 overflow-hidden">
                                    <div class="fw-semibold small text-truncate">{{ attachment.file_name }}</div>
                                    <div class="text-muted" style="font-size:0.7rem;">{{ formatFileSize(attachment.file_size) }}</div>
                                </div>

                                <!-- Download -->
                                <a :href="`/storage/${attachment.file_path}`" target="_blank" class="btn btn-sm" title="Download">
                                    <i class="bi bi-download text-teal"></i>
                                </a>

                                <!-- Delete — own attachments only -->
                                <DeleteConfirmationButton
                                    v-if="attachment.user_id === currentUserId"
                                    confirm-route="tasks.attachments.destroy"
                                    :obj="attachment"
                                    :route-params="{ task: task.id, attachment: attachment.id }"
                                    :delete-content="attachment.file_name"
                                    :icon-only="true"
                                    :use-axios="true"
                                    @deleted="onAttachmentDeleted(attachment.id)"
                                />
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Upload section — fixed at bottom -->
                <div class="modal-footer border-top-0 bg-light rounded-bottom-4 d-flex flex-column align-items-start gap-2">
                    <div class="d-flex align-items-center gap-2 mb-2">
                        <button type="button" class="btn btn-sm btn-light" :disabled="selectedFiles.length >= 5" @click="fileInput?.click()">
                            <i class="bi bi-paperclip text-teal me-1"></i> Add Files
                        </button>
                        <small class="text-muted">Max 5 files, 5MB each</small>
                        <input ref="fileInput" type="file" multiple accept=".jpg,.jpeg,.png,.pdf,.doc,.docx,.xls,.xlsx,.txt" class="d-none" @change="onFileSelect"/>
                    </div>

                    <!-- Selected files preview -->
                    <div v-if="selectedFiles.length > 0" class="d-flex flex-wrap gap-2 mb-2">
                        <div v-for="(file, idx) in selectedFiles" :key="idx" class="file-preview-item d-flex align-items-center gap-1 px-2 py-1 rounded-3">
                            <i :class="fileIconFromMime(file.type)" class="text-teal" style="font-size:0.8rem;"></i>
                            <span style="font-size:0.75rem; max-width:100px;" class="text-truncate">{{ file.name }}</span>
                            <span class="text-muted" style="font-size:0.7rem;">{{ formatFileSize(file.size) }}</span>
                            <button type="button" class="btn btn-sm p-0 ms-1" @click="removeFile(idx)">
                                <i class="bi bi-x text-danger" style="font-size:0.8rem;"></i>
                            </button>
                        </div>
                    </div>

                    <!-- Upload errors -->
                    <span v-if="formErrors['files'] || formErrors['files.0']" class="text-danger d-block mb-2" style="font-size:0.8rem;">
                        {{ formErrors['files'] || formErrors['files.0'] }}
                    </span>

                    <!-- Upload button -->
                    <div v-if="selectedFiles.length > 0" class="d-flex justify-content-end">
                        <SubmitButton label="Upload" :processing="processing" @click="uploadFiles" />
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup lang="ts">
import { ref, reactive, onMounted, computed } from 'vue';
import axios from 'axios';
import { usePage } from '@inertiajs/vue3';
import { Task } from '@/types/interfaces/task';
import { Attachment } from '@/types/interfaces/attachment';
import { fileIconFromMime, formatFileSize } from '@/Utils/fileHelper';
import { toast } from '@/Utils/toast';
import { handleAxiosError } from '@/Utils/handleAxiosError';
import SubmitButton from '@/Components/Button/SubmitButton.vue';
import DeleteConfirmationButton from '@/Components/Button/DeleteConfirmationButton.vue';

const props = defineProps<{
    task: Task;
    parentTaskTitle?: string;
}>();

const emit = defineEmits<{
    close: [];
    updated: [];
}>();

const page          = usePage();
const currentUserId = computed(() => (page.props.auth as any).user.id);

// ── State ──────────────────────────────────────────────────────────────────────
const attachments   = ref<Attachment[]>([]);
const loading       = ref(true);
const processing    = ref(false);
const selectedFiles = ref<File[]>([]);
const fileInput     = ref<HTMLInputElement | null>(null);
const formErrors    = reactive<Record<string, string>>({});

// ── Fetch ──────────────────────────────────────────────────────────────────────
const fetchAttachments = async () => {
    loading.value = true;
    try {
        const { data } = await axios.get(route('tasks.attachments.index', props.task.id));
        attachments.value = data.attachments;
    } finally {
        loading.value = false;
    }
};

onMounted(fetchAttachments);

// ── File selection ─────────────────────────────────────────────────────────────
const onFileSelect = (e: Event) => {
    const input = e.target as HTMLInputElement;
    if (!input.files) return;
    const remaining = 5 - selectedFiles.value.length;
    selectedFiles.value.push(...Array.from(input.files).slice(0, remaining));
    input.value = '';
};

const removeFile = (idx: number) => {
    selectedFiles.value.splice(idx, 1);
};

// ── Upload ─────────────────────────────────────────────────────────────────────
const uploadFiles = async () => {
    processing.value = true;
    Object.keys(formErrors).forEach(k => delete formErrors[k]);

    try {
        const formData = new FormData();
        selectedFiles.value.forEach(file => formData.append('files[]', file));

        const response = await axios.post(
            route('tasks.attachments.store', props.task.id),
            formData,
            { headers: { 'Content-Type': 'multipart/form-data' } }
        );

        toast.success(response.data.success);
        attachments.value.push(...response.data.attachments);
        selectedFiles.value = [];
        emit('updated');
    } catch (e: any) {
        handleAxiosError(e, formErrors);
    } finally {
        processing.value = false;
    }
};

// ── Delete ─────────────────────────────────────────────────────────────────────
const onAttachmentDeleted = (attachmentId: number) => {
    attachments.value = attachments.value.filter(a => a.id !== attachmentId);
    emit('updated');
};
</script>