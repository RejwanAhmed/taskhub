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
            <div v-if="!editing" class="comment-content small" v-html="comment.content"></div>

            <!-- Edit mode -->
            <div v-else>
                <textarea v-model="editContent" class="form-control form-control-sm mb-2" rows="2" style="resize:none;"
                ></textarea>
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

const startEdit = () => {
    editContent.value = props.comment.content;
    editing.value = true;
};

const cancelEdit = () => {
    editing.value = false;
    editContent.value = '';
};

const saveEdit = async () => {
    processing.value = true;
    Object.keys(editErrors).forEach(k => delete editErrors[k]);

    try {
        const response = await axios.put(route('tasks.comments.update', [props.comment.commentable_id, props.comment.id]),{ content: editContent.value });
        toast.success(response.data.success);
        emit('updated', { ...props.comment, content: editContent.value });
        editing.value = false;
    } catch (e: any) {
        handleAxiosError(e, editErrors);
    } finally {
        processing.value = false;
    }
};
</script>