export const statusColor = (s: string): string => {
    return ({
        active: 'bg-success',
        planning: 'bg-info text-dark',
        on_hold: 'bg-warning text-dark',
        completed: 'bg-secondary',
    }[s] ?? 'bg-secondary');
};

export const statusLabel = (s: string): string => {
    return ({
        active: 'Active',
        planning: 'Planning',
        on_hold: 'On Hold',
        completed: 'Completed',
    }[s] ?? s);
};

export const progressPct = (p: any): number => {
    if (!p.tasks_count) return 0;
    return Math.round(((p.completed_tasks_count ?? 0) / p.tasks_count) * 100);
};

export const progressColor = (p: any): string => {
    if (isOverdue(p)) return '#ef4444';
    if (progressPct(p) === 100) return '#10b981';
    return p.color ?? '#0d9488';
};

export const isOverdue = (p: any): boolean => {
    if (!p.end_date || p.status === 'completed') return false;
    return new Date(p.end_date) < new Date();
};