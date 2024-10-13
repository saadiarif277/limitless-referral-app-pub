/**
 * Register Plugins.
 * 
 * Place and enable global plugins into this file.
 */

import AuthPlugin from "@/plugins/auth/index.js";
import ToastPlugin from "@/plugins/toast/index.js";

const registerPlugins = (app) => {
    app.use(AuthPlugin);
    app.use(ToastPlugin);
};

export default registerPlugins;