import Swal, { type SweetAlertIcon, type SweetAlertPosition } from 'sweetalert2';
import 'sweetalert2/dist/sweetalert2.css';

export type NotificationType = 'success' | 'error' | 'warning' | 'info';

export interface ToastOptions {
    type?: NotificationType;
    message: string;
    title?: string;
    duration?: number;
    position?: SweetAlertPosition;
}

export interface AlertOptions {
    type?: SweetAlertIcon;
    title: string;
    message?: string;
    confirmText?: string;
}

export interface ConfirmOptions {
    title: string;
    message?: string;
    type?: SweetAlertIcon;
    confirmText?: string;
    cancelText?: string;
    confirmBtnClass?: string;
}

const typeToColorClass: Record<NotificationType, string> = {
    success: 'color-success',
    error: 'color-danger',
    warning: 'color-warning',
    info: 'color-info',
};

const isDark = (): boolean => document.body.classList.contains('dark');

const modalTheme = () => ({
    background: isDark() ? '#1b2e4b' : '#ffffff',
    color: isDark() ? '#bfc9d4' : '#3b3f5c',
});

export function useNotifications() {
    /**
     * Show a toast notification (non-blocking, auto-dismisses).
     */
    const toast = ({ type = 'success', message, title, duration = 3000, position = 'top-end' }: ToastOptions) => {
        return Swal.mixin({
            toast: true,
            position,
            showConfirmButton: false,
            timer: duration,
            timerProgressBar: true,
            showCloseButton: true,
            customClass: {
                popup: typeToColorClass[type] ?? 'color-primary',
            },
            didOpen: (el) => {
                el.addEventListener('mouseenter', Swal.stopTimer);
                el.addEventListener('mouseleave', Swal.resumeTimer);
            },
        }).fire({
            icon: type,
            title: title ?? message,
            text: title ? message : undefined,
        });
    };

    /**
     * Show a centered alert dialog (single confirm button).
     */
    const alert = ({ type = 'info', title, message, confirmText = 'OK' }: AlertOptions): Promise<void> => {
        return Swal.fire({
            ...modalTheme(),
            icon: type,
            title,
            text: message,
            confirmButtonText: confirmText,
            buttonsStyling: false,
            customClass: {
                confirmButton: 'btn btn-primary',
            },
        }).then(() => undefined);
    };

    /**
     * Show a confirmation dialog. Resolves to `true` if confirmed, `false` if cancelled.
     */
    const confirm = ({
        title,
        message,
        type = 'warning',
        confirmText = 'Confirm',
        cancelText = 'Cancel',
        confirmBtnClass = 'btn-primary',
    }: ConfirmOptions): Promise<boolean> => {
        return Swal.fire({
            ...modalTheme(),
            icon: type,
            title,
            text: message,
            showCancelButton: true,
            confirmButtonText: confirmText,
            cancelButtonText: cancelText,
            buttonsStyling: false,
            reverseButtons: true,
            customClass: {
                confirmButton: `btn ${confirmBtnClass} ltr:ml-4 rtl:mr-4`,
                cancelButton: 'btn btn-outline-danger',
            },
        }).then((result) => result.isConfirmed);
    };

    // Shorthand toast helpers
    const success = (message: string, title?: string) => toast({ type: 'success', message, title });
    const error   = (message: string, title?: string) => toast({ type: 'error',   message, title });
    const warning = (message: string, title?: string) => toast({ type: 'warning', message, title });
    const info    = (message: string, title?: string) => toast({ type: 'info',    message, title });

    return { toast, alert, confirm, success, error, warning, info };
}

export type Notifications = ReturnType<typeof useNotifications>;
