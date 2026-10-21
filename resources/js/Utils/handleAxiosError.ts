import { toast } from '@/Utils/toast';

export const handleAxiosError = (e: any, formErrors?: Record<string, string>) => {
    if (e.response?.status === 422) {
        // handles both validation errors and business exceptions
        if (e.response.data.errors && formErrors) {
            const serverErrors = e.response.data.errors;
            Object.keys(serverErrors).forEach(k => {
                console.log(serverErrors[k]);
                formErrors[k] = serverErrors[k][0];
            });
        } else if (e.response.data.error) {
            // business exception — no form field, show as toast
            toast.error(e.response.data.error);
        }
    } else {
        toast.error(e.response?.data?.error ?? 'Something went wrong. Please try again.');
    }
};