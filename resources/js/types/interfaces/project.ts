export interface Project {
    id: number;
    name: string;
    status: string;
    start_date?: string;
    end_date?: string;
    tasks_count?: number;
    completed_tasks_count?: number;
    members?: Member[];
    [key: string]: unknown;
}

interface Member {
    id: number;
    name: string;
    pivot?: { role?: string };
}