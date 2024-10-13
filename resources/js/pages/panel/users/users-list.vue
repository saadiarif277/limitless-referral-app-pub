<template>
    <v-inertia-head title="User Management" />

    <div class="h-full flex flex-col">
        <v-content-body class="border-b border-gray-200">
            <v-section-heading>
                <template #title>
                    User Management
                </template>

                <template #description>
                    Manage users available in the application.
                </template>

                <template #actions>
                    <template v-if="this.$auth().can(['user.store'])">
                        <v-button :href="route('panel.users.create')">
                            Create User
                        </v-button>
                    </template>
                </template>
            </v-section-heading>
        </v-content-body>

        <v-app-model-table :columns="columns" :data="users.data" :links="users.links" :meta="users.meta">
            <template v-slot:column_user_id="{ row: user }">
                <v-link :href="route('panel.users.show', { user: user.user_id })" class="font-semibold">
                    {{ user.name }}
                </v-link>

                <div class="italic text-gray-500">
                    {{ user.email }}
                </div>
            </template>

            <template v-slot:column_user_type_id="{ row: user }">
                <v-badge>
                    {{ user.user_type.name }}
                </v-badge>
            </template>

            <template v-slot:column_actions="{ row: user }">
                <div class="flex gap-2">
                    <v-button
                        :href="route('impersonate', { 'id': user.user_id })"
                        method="get"
                        as="button"
                        size="xs"
                        color="white"
                        :disabled="!user.can_be_impersonated"
                    >
                        Impersonate
                    </v-button>
                </div>
            </template>
        </v-app-model-table>
    </div>
</template>

<script>
import Layout from "@/layouts/panel/index.vue";

export default {
    layout: Layout,
    props: {
        users: {
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
                user_id: {
                    label: "Name",
                },
                user_type_id: {
                    label: "User Type",
                },
                actions: {
                    label: "Actions",
                },
            },
        };
    },
};
</script>
