<template>
    <v-inertia-head title="Law Firm Management" />

    <div class="h-full flex flex-col">
        <v-content-body class="border-b border-gray-200">
            <v-section-heading>
                <template #title>
                    Law Firms
                </template>

                <template #description>
                    Manage law firms available in the application.
                </template>

                <template #actions>
                    <template v-if="this.$auth().can(['law-firm.store'])">
                        <v-button :href="route('panel.law-firms.create')">
                            Create Law Firm
                        </v-button>
                    </template>
                </template>
            </v-section-heading>
        </v-content-body>

        <v-app-model-table :columns="columns" :data="organizations.data" :links="organizations.links" :meta="organizations.meta">
            <template v-slot:column_organization_id="{ row: organization }">
                <v-link :href="route('panel.law-firms.show', { law_firm: organization.organization_id })" class="font-semibold">
                    {{ organization.name }}
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
        organizations: {
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
                organization_id: {
                    label: "Name",
                },
                address_line_1: {
                    label: "Address",
                    formatter: (row) => {
                        let address = "";

                        if (row.address_line_1) address = `${address} ${row.address_line_1}`;
                        if (row.address_line_2) address = `${address} ${row.address_line_2}`;
                        if (row.city) address = `${address}, ${row.city}`;
                        if (row.state && row.state.name) address = `${address}, ${row.state.name}`;
                        if (row.zip_code) address = `${address}, ${row.zip_code}`;

                        return address;
                    },
                },
            },
        };
    },
};
</script>
