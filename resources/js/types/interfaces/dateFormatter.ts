// composables/useDateFormatter.ts
export function useDateFormatter() {
  /**
   * Converts ISO date string to human-readable format.
   * @param dateStr ISO string like '2026-03-09T16:00:46.000000Z'
   * @returns formatted string like '09 Mar 2026, 04:00 PM'
   */
  const formatDate = (dateStr: string | null | undefined): string => {
    if (!dateStr) return '-';
    const options: Intl.DateTimeFormatOptions = {
      year: 'numeric',
      month: 'short',
      day: '2-digit',
      hour: '2-digit',
      minute: '2-digit',
      hour12: true,
    };
    return new Date(dateStr).toLocaleString('en-US', options);
  };

  return { formatDate };
}