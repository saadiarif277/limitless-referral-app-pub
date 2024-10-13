/**
 * Toast Plugin.
 * 
 * This is a wrapper for the chosen toast plugin.
 */

export default {
    install: (app) => {
        const useAuth = () => ({
            can: (permissionArray) => {
                let result = false;
                const permissions = JSON.parse(JSON.stringify(app.config.globalProperties.$page.props.auth.permissions));
        
                permissionArray.forEach((permission) => {
                    if (permissions.includes(permission)) {
                        result = true;
                    };
                });
        
                return result;
            },
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
        });

        app.config.globalProperties.$auth = useAuth;
        app.provide('auth', useAuth);
    },
};
