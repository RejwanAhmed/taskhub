import { User } from   './user';
import { Tag } from   './tag';
import { TaskType } from   './taskType';

export interface Task {
    id: number,
    project_id: number,
    organization_id: number,
    parent_task_id: number | null,
    title: string,
    description: string | null,
    status: 'to_do' | 'in_progress' | 'in_review' | 'on_hold' | 'completed';
    priority: 'low' | 'medium' | 'high' | 'urgent';
    assigned_to: number | null;
    created_by: number;
    due_date: string | null;
    estimated_hours: number | null;
    actual_hours: number | null;
    completed_at: string | null;
    position: number;
    assignee: User | null;
    creator: User | null;
    task_type: TaskType | null;
    task_type_id: number | null;
    tags: Tag[];
    sub_tasks: Task[];
    sub_tasks_count: number;
    comments_count: number;
    attachments_count: number;
    created_at: string;
    updated_at: string;
}