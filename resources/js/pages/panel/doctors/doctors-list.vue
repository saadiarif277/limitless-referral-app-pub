<template>
    <v-inertia-head title="User Management" />

    <div class="h-full flex flex-col">
        <v-content-body class="border-b border-gray-200">
            <v-section-heading>
                <template #title>
                    Doctor Management
                </template>

                <template #description>
                    Manage doctors available in the application.
                </template>

                <template #actions>
                    <template v-if="this.$auth().can(['doctor.store'])">
                        <v-button :href="route('panel.doctors.create')">
                            Create Doctor
                        </v-button>
                    </template>
                </template>
            </v-section-heading>
        </v-content-body>

        <v-app-model-table :columns="columns" :data="users.data" :links="users.links" :meta="users.meta">
            <template v-slot:column_user_id="{ row: user }">
                <v-link :href="route('panel.doctors.show', { doctor: user.user_id })" class="font-semibold">
                    {{ user.name }}
                </v-link>
            </template>

            <template v-slot:column_medical_specialty_id="{ row: user }">
                <template v-if="user.medical_specialty_id">
                    {{ user.medical_specialty.name }}
                </template>

                <template v-else>
                    <span class="italic">(No Medical Specialty)</span>
                </template>
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
                email: {
                    label: "Email Address",
                },
                medical_specialty_id: {
                    label: "Medical Specialty",
                }
            },
        };
    },
};
</script>
