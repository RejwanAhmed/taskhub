<template>
    <div class="comment-item d-flex gap-3">
        <!-- Avatar -->
        <div class="avatar-circle text-white flex-shrink-0" style="width:32px;height:32px;font-size:0.75rem;" :style="{ background: avatarColor(comment.user.name) }">
            {{ initials(comment.user.name) }}
        </div>

        <!-- Content -->
        <div class="flex-grow-1">
            <div class="d-flex align-items-center justify-content-between mb-1">
                <div class="d-flex align-items-center gap-2">
                    <span class="fw-semibold small">{{ comment.user.name }}</span>
                    <span class="text-muted" style="font-size:0.7rem;">{{ timeAgo(comment.created_at) }}</span>
                </div>

                <!-- Edit and delete button — only for comment owner -->
                <div v-if="comment.user_id === currentUserId" class="d-flex gap-1">
                    <button class="btn btn-sm py-0 px-1" @click="startEdit" title="Edit">
                        <i class="bi bi-pencil text-teal"></i>
                    </button>

                    <DeleteConfirmationButton
                        confirm-route="tasks.comments.destroy"
                        :obj="props.comment"
                        :route-params="{ task: props.comment.commentable_id, comment: props.comment.id }"
                        :delete-content="' comment'"
                        :icon-only="true"
                        :use-axios="true"
                        @deleted="emit('deleted', props.comment.id)"
                    />
                </div>
            </div>

            <!-- View mode -->
            <div v-if="!editing">
                <div  class="comment-content small" v-html="comment.content"></div>
                <!-- Attachments -->
                <div v-if="props.comment.attachments?.length" class="d-flex flex-wrap gap-2 mt-2">
                    <a v-for="attachment in props.comment.attachments" :key="attachment.id" :href="`/storage/${attachment.file_path}`" target="_blank" class="attachment-item d-flex align-items-center gap-1 px-2 py-1 rounded-3 text-decoration-none">
                        <i :class="fileIconFromMime(attachment.mime_type)" class="text-teal" style="font-size:0.8rem;"></i>
                        <span style="font-size:0.75rem; max-width:120px;" class="text-truncate text-dark">
                            {{ attachment.file_name }}
                        </span>
                        <span class="text-muted" style="font-size:0.7rem;">
                            {{ formatFileSize(attachment.file_size) }}
                        </span>
                    </a>
                </div>
            </div>
            <!-- Edit mode -->
            <div v-else>
                <!-- Textarea with Add files button inside -->
                <div class="input-group mb-2">
                    <textarea v-model="editContent" class="form-control border-end-0" rows="2" style="resize:none;"></textarea>
                    <span class="input-group-text bg-white border-start-0 align-items-end pb-2">
                        <button type="button" class="btn btn-sm p-0" title="Attach file" @click="editFileInput?.click()">
                            <i class="bi bi-paperclip text-teal fs-5"></i>
                        </button>
                        <input ref="editFileInput" type="file" multiple accept=".jpg,.jpeg,.png,.pdf,.doc,.docx,.xls,.xlsx,.txt" class="d-none" @change="onEditFileSelect"/>
                    </span>
                </div>

                <!-- Existing attachments -->
                <div v-if="props.comment.attachments?.length" class="d-flex flex-wrap gap-2 mb-2">
                    <div v-for="attachment in props.comment.attachments" :key="attachment.id" class="file-preview-item d-flex align-items-center gap-1 px-2 py-1 rounded-3" :class="{ 'opacity-50': removedAttachmentIds.includes(attachment.id) }">
                        <i :class="fileIconFromMime(attachment.mime_type)" class="text-teal" style="font-size:0.8rem;"></i>
                        <span style="font-size:0.75rem; max-width:100px;" class="text-truncate">
                            {{ attachment.file_name }}
                        </span>
                        <button v-if="!removedAttachmentIds.includes(attachment.id)" type="button" class="btn btn-sm p-0 ms-1" @click="removeExistingAttachment(attachment.id)">
                            <i class="bi bi-x text-danger" style="font-size:0.8rem;"></i>
                        </button>
                        <button v-else type="button" class="btn btn-sm p-0 ms-1" @click="removedAttachmentIds.splice(removedAttachmentIds.indexOf(attachment.id), 1)" title="Undo">
                            <i class="bi bi-arrow-counterclockwise text-teal" style="font-size:0.8rem;"></i>
                        </button>
                    </div>
                </div>

                <!-- New files preview -->
                <div v-if="newFiles.length > 0" class="d-flex flex-wrap gap-2 mb-2">
                    <div v-for="(file, idx) in newFiles" :key="idx" class="file-preview-item d-flex align-items-center gap-1 px-2 py-1 rounded-3">
                        <i :class="fileIconFromMime(file.type)" class="text-teal" style="font-size:0.8rem;"></i>
                        <span style="font-size:0.75rem; max-width:100px;" class="text-truncate">
                            {{ file.name }}
                        </span>
                        <span class="text-muted" style="font-size:0.7rem;">
                            {{ formatFileSize(file.size) }}
                        </span>
                        <button type="button" class="btn btn-sm p-0 ms-1" @click="removeNewFile(idx)">
                            <i class="bi bi-x text-danger" style="font-size:0.8rem;"></i>
                        </button>
                    </div>
                </div>

                <span v-if="editErrors.content" class="text-danger d-block mb-1" style="font-size:0.75rem;">
                    {{ editErrors.content }}
                </span>

                <div class="d-flex gap-2 justify-content-end">
                    <button class="btn btn-sm btn-light" @click="cancelEdit">Cancel</button>
                    <SubmitButton label="Save" :processing="processing" @click="saveEdit" />
                </div>
            </div>
        </div>
    </div>
</template>

<script setup lang="ts">
import { reactive, ref } from 'vue';
import axios from 'axios';
import { Comment } from '@/types/interfaces/comment';
import { avatarColor, initials } from '@/Utils/taskHelpers';
import { toast } from '@/Utils/toast';
import SubmitButton from '@/Components/Button/SubmitButton.vue';
import { handleAxiosError } from '@/Utils/handleAxiosError';
import DeleteConfirmationButton from '@/Components/Button/DeleteConfirmationButton.vue';
import { fileIconFromMime, formatFileSize } from '@/Utils/fileHelper';

const props = defineProps<{
    comment: Comment;
    currentUserId: number;
}>();

const emit = defineEmits<{
    updated: [comment: Comment];
    deleted: [commentId: number];
}>();

// ── Time ago ───────────────────────────────────────────────────────────────────
const timeAgo = (date: string): string => {
    const diff = Math.floor((Date.now() - new Date(date).getTime()) / 1000);
    if (diff < 60)    return `${diff}s ago`;
    if (diff < 3600)  return `${Math.floor(diff / 60)}m ago`;
    if (diff < 86400) return `${Math.floor(diff / 3600)}h ago`;
    return `${Math.floor(diff / 86400)}d ago`;
};

// ── Edit ───────────────────────────────────────────────────────────────────────
const editing     = ref(false);
const editContent = ref('');
const processing  = ref(false);
const editErrors   = reactive<Record<string, string>>({});  

// existing attachments in edit mode
const removedAttachmentIds = ref<number[]>([]);
const newFiles             = ref<File[]>([]);
const editFileInput        = ref<HTMLInputElement | null>(null);

const startEdit = () => {
    editContent.value      = props.comment.content;
    removedAttachmentIds.value = [];
    newFiles.value         = [];
    editing.value          = true;
};

const cancelEdit = () => {
    editing.value          = false;
    editContent.value      = '';
    removedAttachmentIds.value = [];
    newFiles.value         = [];
    Object.keys(editErrors).forEach(k => delete editErrors[k]);
};

const removeExistingAttachment = (id: number) => {
    removedAttachmentIds.value.push(id);
};

const onEditFileSelect = (e: Event) => {
    const input = e.target as HTMLInputElement;
    if (!input.files) return;
    const remaining = 5 - newFiles.value.length;
    newFiles.value.push(...Array.from(input.files).slice(0, remaining));
    input.value = '';
};

const removeNewFile = (idx: number) => {
    newFiles.value.splice(idx, 1);
};

const saveEdit = async () => {
    processing.value = true;
    Object.keys(editErrors).forEach(k => delete editErrors[k]);

    try {
        const formData = new FormData();
        if (editContent.value) formData.append('content', editContent.value);
        removedAttachmentIds.value.forEach(id => formData.append('removed_attachment_ids[]', String(id)));
        newFiles.value.forEach(file => formData.append('new_files[]', file));
        formData.append('_method', 'PUT');

        const response = await axios.post(
            route('tasks.comments.update', [props.comment.commentable_id, props.comment.id]),
            formData,
            { headers: { 'Content-Type': 'multipart/form-data' } }
        );

        toast.success(response.data.success);
        const updatedComment = response.data.comment;
        editing.value = false;
        editContent.value = '';
        emit('updated', updatedComment);
    } catch (e: any) {
        handleAxiosError(e, editErrors);
    } finally {
        processing.value = false;
    }
};
</script>