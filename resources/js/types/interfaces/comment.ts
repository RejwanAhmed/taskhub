import { User } from './user';
import { Attachment } from './attachment';

export interface Comment {
    id: number;
    commentable_type: string;
    commentable_id: number;
    user_id: number;
    content: string;
    user: User;
    attachments: Attachment[];
    created_at: string;
    updated_at: string;
}