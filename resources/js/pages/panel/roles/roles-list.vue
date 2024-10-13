<template>
    <v-inertia-head title="Role Management" />

    <div class="h-full flex flex-col">
        <v-content-body class="border-b border-gray-200">
            <v-section-heading>
                <template #title>
                    Roles &amp; Permissions
                </template>

                <template #description>
                    Manage roles available in the application and their permissions.
                </template>

                <template #actions>
                    <template v-if="this.$auth().can(['role.store'])">
                        <v-button :href="route('panel.roles.create')">
                            Create Role
                        </v-button>
                    </template>
                </template>
            </v-section-heading>
        </v-content-body>

        <v-app-model-table :columns="columns" :data="roles.data" :links="roles.links" :meta="roles.meta">
            <template v-slot:column_id="{ row: role }">
                <v-link :href="route('panel.roles.show', { role: role.id })" class="font-semibold">
                    {{ role.name }}
                </v-link>
            </template>

            <template v-slot:column_users_count="{ row: role }">
                <v-badge>
                    {{ role.users_count }}
                </v-badge>
            </template>
        </v-app-model-table>
    </div>
</template>

<script>
import Layout from "@/layouts/panel/index.vue";

export default {
    layout: Layout,
    props: {
        roles: {
            type: Object,
            required: false,
            default: () => {},
        },
        links: {
            type: Object,
            required: false,
            default: () => {},
        },
    },
    data(){
        return {
            columns: {
                id: {
                    label: "Name",
                },
                users_count: {
                    label: "# of Users",
                    align: "right",
                },
            },
        };
    },
};
</script>
