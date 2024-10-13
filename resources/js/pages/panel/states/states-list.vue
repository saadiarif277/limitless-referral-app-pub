<template>
    <v-inertia-head title="State Management" />

    <div class="h-full flex flex-col">
        <v-content-body class="border-b border-gray-200">
            <v-section-heading>
                <template #title>
                    States &amp; Document Types
                </template>

                <template #description>
                    Manage states available in the application and their document_types.
                </template>
            </v-section-heading>
        </v-content-body>

        <v-app-model-table :columns="columns" :data="states.data" :links="states.links" :meta="states.meta">
            <template v-slot:column_id="{ row: state }">
                <v-link :href="route('panel.states.show', { state: state.state_id })" class="font-semibold">
                    {{ state.name }}
                </v-link>
            </template>

            <template v-slot:column_users_count="{ row: state }">
                <v-badge>
                    {{ state.users_count }}
                </v-badge>
            </template>

            <template v-slot:column_document_types="{ row: state }">
                <div class="flex gap-2">
                    <template v-for="(documentType, documentTypeIndex) in state.document_types" :key="'documentType_' + documentTypeIndex">
                        <v-badge>
                            {{ documentType.name }}
                        </v-badge>
                    </template>
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
        states: {
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
                document_types: {
                    label: "Document Types",
                },
            },
        };
    },
};
</script>
