<template>
    <div class="modal d-block modal-background">
        <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
            <div class="modal-content border-0 rounded-4">

                <!-- Header -->
                <div class="modal-header border-bottom-0 pb-0">
                    <div>
                        <!-- Breadcrumb -->
                        <div v-if="parentTaskTitle" class="text-muted mb-1" style="font-size:0.75rem;">
                            <i class="bi bi-diagram-2 me-1"></i>
                            {{ parentTaskTitle }} <i class="bi bi-chevron-right mx-1"></i> {{ task.title }}
                        </div>
                        <h6 class="modal-title fw-bold">
                            <i class="bi bi-chat-left-text me-2 text-teal"></i>
                            {{ parentTaskTitle ? 'Subtask Comments' : 'Task Comments' }}
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

                    <!-- Empty state -->
                    <div v-else-if="comments.length === 0" class="text-center text-muted py-4" style="font-size:0.85rem;">
                        <i class="bi bi-chat-left-text d-block fs-3 mb-2 text-teal"></i>
                        No comments yet. Be the first to comment.
                    </div>

                    <!-- Comment list -->
                    <div v-else class="d-flex flex-column gap-3">
                        <CommentItem
                            v-for="comment in comments"
                            :key="comment.id"
                            :comment="comment"
                            :current-user-id="currentUserId"
                            @updated="onCommentUpdated"
                            @deleted="onCommentDeleted"
                        />
                    </div>
                </div>

                <!-- Comment input -->
                <div class="modal-footer border-top-0 bg-light rounded-bottom-4 d-flex flex-column gap-2">

                    <div class="d-flex gap-2 align-items-start w-100">
                        <!-- Avatar -->
                        <div class="avatar-circle text-white flex-shrink-0" style="width:32px;height:32px;font-size:0.75rem;background:#0d9488;">
                            <i class="bi bi-person"></i>
                        </div>

                        <div class="flex-grow-1">
                            <div class="input-group">
                                <textarea v-model="content" class="form-control border-end-0" rows="2" placeholder="Write a comment..." style="resize:none;"></textarea>
                                <span class="input-group-text bg-white border-start-0 align-items-end pb-2">
                                    <button type="button" class="btn btn-sm p-0" title="Attach file" :disabled="selectedFiles.length >= 5" @click="fileInput?.click()">
                                        <i class="bi bi-paperclip text-teal fs-5"></i>
                                    </button>
                                    <input ref="fileInput" type="file" multiple accept=".jpg,.jpeg,.png,.pdf,.doc,.docx,.xls,.xlsx,.txt" class="d-none" @change="onFileSelect"/>
                                </span>
                            </div>

                            <!-- Validation error -->
                            <span v-if="formErrors.content" class="text-danger" style="font-size:0.8rem;">
                                {{ formErrors.content }}
                            </span>

                            <!-- File preview -->
                            <div v-if="selectedFiles.length > 0" class="d-flex flex-wrap gap-2 mt-2">
                                <div v-for="(file, idx) in selectedFiles" :key="idx" class="file-preview-item d-flex align-items-center gap-1 px-2 py-1 rounded-3">
                                    <i :class="fileIconFromMime(file.type)" class="text-teal" style="font-size:0.8rem;"></i>
                                    <span style="font-size:0.75rem; max-width:100px;" class="text-truncate">
                                        {{ file.name }}
                                    </span>
                                    <span class="text-muted" style="font-size:0.7rem;">
                                        {{ formatFileSize(file.size) }}
                                    </span>
                                    <button type="button" class="btn btn-sm p-0 ms-1" @click="removeFile(idx)" >
                                        <i class="bi bi-x text-danger" style="font-size:0.8rem;"></i>
                                    </button>
                                </div>
                            </div>

                            <!-- files error -->
                            <span v-if="formErrors['files.0'] || formErrors['files']" class="text-danger d-block" style="font-size:0.8rem;">
                                {{ formErrors['files.0'] || formErrors['files'] }}
                            </span>

                        </div>

                        <SubmitButton label="Send" :processing="processing" @click="submitComment" />
                    </div>

                </div>
            </div>
        </div>
    </div>
</template>

<script setup lang="ts">
import { ref, onMounted, reactive, computed } from 'vue';
import axios from 'axios';
import { Task } from '@/types/interfaces/task';
import { Comment } from '@/types/interfaces/comment';
import CommentItem from '@/Pages/Task/Comment/CommentItem.vue';
import { toast } from '@/Utils/toast';
import { handleAxiosError } from '@/Utils/handleAxiosError';
import SubmitButton from '@/Components/Button/SubmitButton.vue';
import { usePage } from '@inertiajs/vue3';
import { fileIconFromMime, formatFileSize } from '@/Utils/fileHelper';

const processing = ref(false);
const formErrors = reactive<Record<string, string>>({});
const content    = ref('');
const selectedFiles = ref<File[]>([]);
const fileInput = ref<HTMLInputElement | null>(null);

const page = usePage();
const currentUserId = computed(() => (page.props.auth as any).user.id);

const props = defineProps<{
    task: Task;
    parentTaskTitle?: string;
}>();

const emit = defineEmits<{
    close: [];
}>();

const comments = ref<Comment[]>([]);
const loading  = ref(true);

const onFileSelect = (e: Event) => {
    const input = e.target as HTMLInputElement;
    if (!input.files) return;

    const newFiles = Array.from(input.files);

    // max 5 files total
    const remaining = 5 - selectedFiles.value.length;
    const toAdd = newFiles.slice(0, remaining);
    selectedFiles.value.push(...toAdd);

    // reset input so same file can be selected again
    input.value = '';
};

const removeFile = (idx: number) => {
    selectedFiles.value.splice(idx, 1);
};

const submitComment = async () => {
    processing.value = true;
    Object.keys(formErrors).forEach(k => delete formErrors[k]);

    try {
        const formData = new FormData();
        if (content.value) formData.append('content', content.value);
        selectedFiles.value.forEach(file => formData.append('files[]', file));

        const response = await axios.post(
            route('tasks.comments.store', props.task.id),
            formData,
            { headers: { 'Content-Type': 'multipart/form-data' } }
        );

        toast.success(response.data.success);
        comments.value.push(response.data.comment);
        content.value = '';
        selectedFiles.value = [];
    } catch (e: any) {
        handleAxiosError(e, formErrors);
    } finally {
        processing.value = false;
    }
};

const onCommentUpdated = (updatedComment: Comment) => {
    const idx = comments.value.findIndex(c => c.id === updatedComment.id);
    if (idx !== -1) {
        comments.value.splice(idx, 1, updatedComment);
    }
};

const onCommentDeleted = (commentId: number) => {
    comments.value = comments.value.filter(c => c.id !== commentId);
};

const fetchComments = async () => {
    loading.value = true;
    try {
        const { data } = await axios.get(route('tasks.comments.index', props.task.id));
        comments.value = data.comments;
    } finally {
        loading.value = false;
    }
};

onMounted(fetchComments);
</script>