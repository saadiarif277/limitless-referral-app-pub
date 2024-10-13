/**
 * Toast Plugin.
 * 
 * This is a wrapper for the chosen toast plugin.
 */

import Swal from "sweetalert2";
import "sweetalert2/dist/sweetalert2.min.css";

const useAlert = () => {
    return {
        confirm: (callback) => new Promise((resolve, reject) => {
            Swal.fire({
                title: "Are you sure?",
                text: "You won't be able to revert this!",
                icon: "warning",
                showCancelButton: true,
                confirmButtonText: "Yes, delete it!",
                cancelButtonText: "No, cancel!",
                reverseButtons: true,
                allowOutsideClick: false,
                preConfirm: () => {
                    return callback()
                        .then((response) => ({ response }))
                        .catch((error) => ({ error }));
                },
            }).then((result) => {
                if (result.isConfirmed) {
                    if(result.value.response) resolve(result.value.response);
                    if(result.value.error) reject(result.value.error);
                } else if (result.dismiss === Swal.DismissReason.cancel) {
                    resolve(null);
                }
            });
        }),
    };
};

const useToast = () => {
    const toastOptions = {
        toast: true,
        position: "bottom",
        showConfirmButton: false,
        timerProgressBar: true,
        timer: 3000,
        didOpen: (toast) => {
            toast.addEventListener('click', () => {
                Swal.close(); // This closes the toast on click
            });
        },
    };

    return {
        info: (text, title = "Attention!") => Swal.fire({
            text, title, 
            icon: "info",
            ...toastOptions,
        }),
        success: (text, title = "Success!") => Swal.fire({
            text, title, 
            icon: "success",
            ...toastOptions,
        }),
        warning: (text, title = "Warning!") => Swal.fire({
            text, title, 
            icon: "warning",
            ...toastOptions,
        }),
        error: (text, title = "Error!") => Swal.fire({
            text, title, 
            icon: "error",
            ...toastOptions,
        }),
    };
};

export default {
    install: (app) => {
        app.config.globalProperties.$alert = useAlert;
        app.provide('alert', useAlert);

        app.config.globalProperties.$toast = useToast;
        app.provide('toast', useToast);
    },
};
