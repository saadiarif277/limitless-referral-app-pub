<template>
    <v-inertia-head title="Appointment Management" />

    <div class="h-full flex flex-col">
        <v-content-body class="border-b border-gray-200">
            <v-section-heading>
                <template #title>
                    Appointments
                </template>

                <template #description>
                    Manage patient appointments across the application.
                </template>
            </v-section-heading>
        </v-content-body>

        <div class="w-full grid grid-cols-1 md:grid-cols-1 xl:grid-cols-4">
            <div class="col-span-full xl:col-span-1">
                <template v-for="(organizationType, organizationTypeIndex) in organizationTypes.data" :key="'organizationType_' + organizationTypeIndex">
                    <template v-if="organizationType.organizations && organizationType.organizations.length">
                        <v-content-body>
                            <v-form-label>{{ organizationType.name  }}</v-form-label>
                            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-1">
                                <template v-for="(organization, organizationIndex) in organizationType.organizations" :key="'organization_' + organizationIndex">
                                    <v-form-checkbox :value="organization.organization_id" v-model="selected_organization_ids" @change="updateOrganizationFilter">
                                        <div class="line-clamp-1">
                                            {{ organization.name }}
                                        </div>
                                    </v-form-checkbox>
                                </template>
                            </div>
                        </v-content-body>
                    </template>
                </template>
            </div>
            
            <div :class="(organizationTypes.data) ? 'col-span-full xl:col-span-3' : 'col-span-full'">
                <v-content-body>
                    <v-card>
                        <v-content-body>
                            <v-calendar :events="parsedEvents" @on-event-click="onEventClick" />
                        </v-content-body>
                    </v-card>
                </v-content-body>
            </div>
        </div>
    </div>
</template>

<script>
import Layout from "@/layouts/panel/index.vue";
import Calendar from "@/components/calendar.vue";

export default {
    layout: Layout,
    components: {
        "v-calendar": Calendar,
    },
    props: {
        appointments: {
            type: Object,
            required: false,
            default: () => {},
        },
        organizationTypes: {
            type: Object,
            required: false,
            default: () => {},
        },
    },
    computed: {
        parsedEvents() {
            return this.appointments.data.map((appointment) => ({
                title: appointment.appointment_type.name,
                start: appointment.start_date,
                end: appointment.end_date,
                borderColor: appointment.appointment_type.color,
                extendedProps: {
                    appointmentId: appointment.appointment_id,
                },
            }));
        },
    },
    data() {
        return {
            selected_organization_ids: this.$page.props.query.selected_organization_ids || [],
        };
    },
    methods: {
        onEventClick(event) {
            this.$inertia.visit(route('panel.appointments.show', { appointment: event.extendedProps.appointmentId }));
        },
        updateOrganizationFilter() {
            this.$inertia.get(route(route().current()), {
                ...this.$page.props.query,
                selected_organization_ids: this.selected_organization_ids,
            }, {
                preserveState: true,
                preserveScroll: true,
                only: ['appointments']
            });
        },
    },
};
</script>
