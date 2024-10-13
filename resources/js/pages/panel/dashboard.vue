<template>
    <v-inertia-head title="Dashboard" />

    <v-section>
        <v-content-body class="border-b border-gray-200">
            <v-section-heading>
                <template #title>
                    Dashboard
                </template>

                <template #description>
                    Welcome back, {{ $page.props.auth.user.name }}.
                </template>
            </v-section-heading>
        </v-content-body>

        <v-content-body class="grid grid-cols-1 gap-6">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <template v-for="(stat, statIndex) in stats" :key="'stat_' + statIndex">
                    <v-card>
                        <v-content-body>
                            <v-section-heading>
                                <template #title>
                                    {{ stat.title }}
                                </template>
                            </v-section-heading>
                        </v-content-body>

                        <v-content-body>
                            <component
                                :is="stat.component"
                                :data="stat.data"
                                :options="stat.options"
                            />
                        </v-content-body>
                    </v-card>
                </template>
            </div>
        </v-content-body>
    </v-section>
</template>

<script>
import Layout from "@/layouts/panel/index.vue";

export default {
    layout: Layout,
    props: {
        stats: {
            type: Array,
            required: false,
            default: () => [],
        },
    },
};
</script>
