import toastr from 'toastr';
import 'toastr/build/toastr.min.css';

toastr.options = {
    closeButton: false,
    progressBar: true,
    positionClass: 'toast-top-right',
    showDuration: 300,
    hideDuration: 1000,
    timeOut: 5000,
    extendedTimeOut: 1000,
    showEasing: 'swing',
    hideEasing: 'linear',
    showMethod: 'fadeIn',
    hideMethod: 'fadeOut',
};

export const toast = {
    success: (msg: string) => toastr.success(msg),
    error:   (msg: string) => toastr.error(msg),
    info:    (msg: string) => toastr.info(msg),
    warning: (msg: string) => toastr.warning(msg),
};