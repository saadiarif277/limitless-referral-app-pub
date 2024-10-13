<template>
    <v-inertia-head title="Referral Type Management" />

    <div class="h-full flex flex-col">
        <v-content-body class="border-b border-gray-200">
            <v-section-heading>
                <template #title>
                    Referral Types
                </template>

                <template #description>
                    Manage referral types available in the application.
                </template>

                <template #actions>
                    <template v-if="this.$auth().can(['referral-type.store'])">
                        <v-button :href="route('panel.referral-types.create')">
                            Create Referral Type
                        </v-button>
                    </template>
                </template>
            </v-section-heading>
        </v-content-body>

        <v-app-model-table :columns="columns" :data="referralTypes.data" :links="referralTypes.links" :meta="referralTypes.meta">
            <template v-slot:column_referral_type_id="{ row: referralType }">
                <v-link :href="route('panel.referral-types.show', { referral_type: referralType.referral_type_id })" class="font-semibold">
                    {{ referralType.name }}
                </v-link>
            </template>

            <template v-slot:column_referrals_count="{ row: referralType }">
                <v-badge>
                    {{ referralType.referrals_count }}
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
        referralTypes: {
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
                referral_type_id: {
                    label: "Name",
                    formatter: (row) => row.name,
                },
                referrals_count: {
                    label: "# of Referrals",
                    align: "right",
                },
            },
        };
    },
};
</script>
