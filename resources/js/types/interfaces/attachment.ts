export interface Attachment {
    id: number;
    attachable_type: string;
    attachable_id: number;
    user_id: number;
    file_name: string;
    file_path: string;
    file_size: number;
    mime_type: string;
    created_at: string;
}