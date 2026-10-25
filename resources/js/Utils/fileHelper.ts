export const formatFileSize = (bytes: number): string => {
    if (bytes < 1024)    return bytes + ' B';
    if (bytes < 1048576) return (bytes / 1024).toFixed(1) + ' KB';
    return (bytes / 1048576).toFixed(1) + ' MB';
};

export const fileIconFromMime = (mimeType: string): string => {
    if (mimeType.startsWith('image/'))                              return 'bi bi-file-image';
    if (mimeType === 'application/pdf')                             return 'bi bi-file-pdf';
    if (mimeType.includes('word'))                                  return 'bi bi-file-word';
    if (mimeType.includes('excel') || mimeType.includes('sheet'))  return 'bi bi-file-excel';
    return 'bi bi-file-text';
};