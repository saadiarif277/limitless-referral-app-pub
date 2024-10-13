<template>
    <v-inertia-head title="Document Category Management" />

    <div class="h-full flex flex-col">
        <v-content-body class="border-b border-gray-200">
            <v-section-heading>
                <template #title>
                    Document Categories
                </template>

                <template #description>
                    Manage document categories available in the application.
                </template>

                <template #actions>
                    <template v-if="this.$auth().can(['document-category.store'])">
                        <v-button :href="route('panel.document-categories.create')">
                            Create Document Category
                        </v-button>
                    </template>
                </template>
            </v-section-heading>
        </v-content-body>

        <v-app-model-table :columns="columns" :data="documentCategories.data" :links="documentCategories.links" :meta="documentCategories.meta">
            <template v-slot:column_document_category_id="{ row: documentCategory }">
                <v-link :href="route('panel.document-categories.show', { document_category: documentCategory.document_category_id })" class="font-semibold">
                    {{ documentCategory.name }}
                </v-link>
            </template>
        </v-app-model-table>
    </div>
</template>

<script>
import Layout from "@/layouts/panel/index.vue";

export default {
    layout: Layout,
    props: {
        documentCategories: {
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
                document_category_id: {
                    label: "Name",
                    formatter: (row) => row.name,
                },
                document_category_id: {
                    label: "Document Category",
                    formatter: (row) => row.document_category.name,
                },
            },
        };
    },
};
</script>
