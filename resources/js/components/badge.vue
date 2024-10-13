<template>
    <span :class="compiledClasses">
        <slot></slot>
    </span>
</template>

<script>
export default {
    name: "BadgeComponent",
    props: {
        color: {
            type: String,
            required: false,
            default: () => "default",
        },
    },
    computed: {
        compiledClasses() {
            const baseClasses = "v-badge inline-flex items-center rounded-full px-2 py-1 text-xs font-medium";

            const colorClasses = {
                "default":   "bg-gray-100 text-gray-600",
                "primary":   "bg-primary-100 text-primary-700",
                "secondary": "bg-secondary-100 text-secondary-700",
                "success":   "bg-green-100 text-green-700",
                "warning":   "bg-yellow-100 text-yellow-700",
                "danger":    "bg-red-100 text-red-700",
            };

            return `${baseClasses} ${colorClasses[this.color]}`;
        }
    },
};
</script>
