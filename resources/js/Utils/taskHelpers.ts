export const COLORS = ['#3B82F6', '#10B981', '#F59E0B', '#EF4444', '#8B5CF6', '#EC4899', '#0d9488', '#F97316'];

export const avatarColor  = (name: string) => COLORS[name.charCodeAt(0) % COLORS.length];
export const initials     = (name: string) => name.split(' ').map(n => n[0]).slice(0, 2).join('').toUpperCase();
export const formatDate   = (d: string)    => new Date(d).toLocaleDateString('en-GB', { day: 'numeric', month: 'short' });
export const isOverdue    = (task: any)    => task.due_date && new Date(task.due_date) < new Date() && task.status !== 'completed';
export const priorityLabel = (p: string)  => ({ low: 'Low', medium: 'Medium', high: 'High', urgent: 'Urgent' }[p] ?? p);
export const priorityClass = (p: string)  => ({
    low:    'bg-secondary bg-opacity-10 text-secondary',
    medium: 'bg-primary bg-opacity-10 text-primary',
    high:   'bg-warning bg-opacity-10 text-warning',
    urgent: 'bg-danger bg-opacity-10 text-danger',
}[p] ?? '');