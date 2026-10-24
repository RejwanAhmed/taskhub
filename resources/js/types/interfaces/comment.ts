import { User } from './user';

export interface Comment {
    id: number;
    commentable_type: string;
    commentable_id: number;
    user_id: number;
    content: string;
    user: User;
    created_at: string;
    updated_at: string;
}