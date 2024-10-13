<template>
    <v-inertia-head :title="(!appointment) ? 'Create Appointment' : 'Editing appointment.'" />

    <v-form class="h-full" @submit.prevent="submitForm">
        <v-content-body class="border-b border-gray-200">
            <v-section-heading>
                <template #title>
                    <template v-if="!appointment">
                        Create Appointment

                        <template v-if="form.referral_id">
                            <span>
                                for the referral <v-link :href="route('panel.referrals.show', { referral: form.referral_id })">"REF#{{ form.referral_id }}"</v-link>
                            </span>
                        </template>
                    </template>

                    <template v-else>
                        Editing appointment.
                    </template>
                </template>

                <template #description>
                    <template v-if="!appointment">
                        Fill up the form below to create a new appointment for the application.
                    </template>

                    <template v-else>
                        Change and/or modify the details for the appointment.
                    </template>
                </template>

                <template #actions>
                    <template v-if="appointment">
                        <div class="flex gap-2">
                            <template v-if="this.$auth().can(['referral.show', 'referral.show-own'])">
                                <v-button :href="route('panel.referrals.show', { referral: form.referral_id })" :disabled="form.processing" color="white" v-if="form.referral_id">
                                    View Referral
                                </v-button>
                            </template>

                            <template v-if="this.$auth().can(['appointment.destroy'])">
                                <v-button color="danger" :disabled="form.processing" @click.prevent="deleteRequest">
                                    Delete Appointment
                                </v-button>
                            </template>
                        </div>
                    </template>
                </template>
            </v-section-heading>
        </v-content-body>

        <template v-if="!appointment && !form.referral_id">
            <v-content-body>
                <v-paragraph>
                    <span class="text-red-500 font-bold">Error: Referral not found.</span> <br /> Please create an appointment via a referral <v-link :href="route('panel.referrals.index')">here.</v-link>
                </v-paragraph>
            </v-content-body>
        </template>

        <template v-else>
            <div class="grid grid-cols-1 divide-y divide-gray-200 gap-12 pb-12">
                <v-form-group class="divide-y divide-gray-200">
                    <v-section-divider>
                        General Details
                    </v-section-divider>

                    <v-content-body class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-x-6 gap-y-4">
                        <v-form-group>
                            <v-form-label>Appointment Type</v-form-label>
                            <v-form-select
                                :options="appointmentTypes.data.map((appointment_type) => ({ label: appointment_type.name, value: appointment_type.appointment_type_id }))"
                                :error="form.errors.appointment_type_id"
                                :disabled="form.processing"
                                v-model="form.appointment_type_id"
                            />
                            <v-form-error v-if="form.errors.appointment_type_id">{{ form.errors.appointment_type_id }}</v-form-error>
                        </v-form-group>

                        <v-form-group>
                            <v-form-label>
                                Organization
                            </v-form-label>

                            <v-form-select
                                :options="organizations.data.map(({ name: label, organization_id: value }) => ({ label, value }))"
                                :error="form.errors.organization_id"
                                :disabled="form.processing"
                                :required="true"
                                v-model="form.organization_id"
                            />
                            <v-form-error v-if="form.errors.organization_id">{{ form.errors.organization_id }}</v-form-error>
                        </v-form-group>

                        <v-form-group class="md:col-span-2">
                            <v-form-label>
                                Patient
                                <span class="italic" v-if="form.referral_id">
                                    (Inherited from <v-link :href="route('panel.referrals.show', { referral: form.referral_id })">"REF#{{ form.referral_id }}"</v-link>)
                                </span>
                            </v-form-label>

                            <v-form-select
                                :options="patients.data.map((user) => ({ label: user.name, value: user.user_id }))"
                                :error="form.errors.patient_user_id"
                                :disabled="form.processing || !!form.referral_id"
                                v-model="form.patient_user_id"
                            />
                            <v-form-error v-if="form.errors.patient_user_id">{{ form.errors.patient_user_id }}</v-form-error>
                        </v-form-group>

                        <v-form-group class="md:col-span-2">
                            <v-form-label>Start Date</v-form-label>
                            <v-form-input type="datetime-local" v-model="form.start_date" :error="form.errors.start_date" :disabled="form.processing" />
                            <v-form-error v-if="form.errors.start_date">{{ form.errors.start_date }}</v-form-error>
                        </v-form-group>

                        <v-form-group class="md:col-span-2">
                            <v-form-label>End Date</v-form-label>
                            <v-form-input type="datetime-local" v-model="form.end_date" :error="form.errors.end_date" :disabled="form.processing" />
                            <v-form-error v-if="form.errors.end_date">{{ form.errors.end_date }}</v-form-error>
                        </v-form-group>

                        <v-form-group class="col-span-full">
                            <v-form-label>Description</v-form-label>
                            <v-form-textarea v-model="form.description" :error="form.errors.description" :disabled="form.processing"></v-form-textarea>
                            <v-form-error v-if="form.errors.description">{{ form.errors.description }}</v-form-error>
                        </v-form-group>
                    </v-content-body>
                </v-form-group>
            </div>
        </template>

        <v-form-group class="bg-gray-50 flex items-center justify-end gap-2 text-right border-t border-gray-200">
            <v-content-body class="flex gap-4">
                <progress v-if="form.progress" :value="form.progress.percentage" max="100">
                    {{ form.progress.percentage }}%
                </progress>

                <v-button :href="route('panel.appointments.index')" color="white" :disabled="form.processing">
                    Cancel
                </v-button>

                <template v-if="!appointment">
                    <v-button :disabled="form.processing || !form.referral_id">
                        Create Appointment
                    </v-button>
                </template>

                <template v-else>
                    <v-button :disabled="form.processing">
                        Save Changes
                    </v-button>
                </template>
            </v-content-body>
        </v-form-group>
    </v-form>
</template>

<script>
import Layout from "@/layouts/panel/index.vue";
import { useForm } from "@inertiajs/vue3";

export default {
    name: "AppointmentsCreateEdit",
    layout: Layout,
    props: {
        appointment: {
            type: Object,
            required: false,
            default: () => {},
        },
        appointmentTypes: {
            type: Object,
            required: true,
        },
        organizations: {
            type: Object,
            required: true,
        },
        patients: {
            type: Object,
            required: true,
        },
        referral: {
            type: Object,
            required: false,
            default: () => {},
        },
    },
    data() {
        return {
            form: useForm({
                referral_id: "",
                appointment_type_id: "",
                organization_id: "",
                description: "",
                patient_user_id: "",
                start_date: "",
                end_date: "",
            }),
        };
    },
    watch: {
        appointment: {
            handler(value) {
                if (!value || !value.data) {
                    return;
                }

                this.form.description = value.data.description;
                this.form.appointment_type_id = value.data.appointment_type_id;
                this.form.organization_id = value.data.organization_id;
                this.form.patient_user_id = value.data.patient_user_id;
                this.form.start_date = value.data.start_date;
                this.form.end_date = value.data.end_date;

                if (value.data.referral) {
                    this.form.referral_id = value.data.referral.referral_id;                    
                }
            },
            immediate: true,
        },
        referral: {
            handler(value) {
                if (!value || !value.data) {
                    return;
                }

                if (!this.appointment) {
                    this.form.referral_id = value.data.referral_id;
                    this.form.organization_id = value.data.organization_id;
                    this.form.patient_user_id = value.data.patient_user_id;
                }
            },
            immediate: true,
        },
        "form.start_date": {
            handler(value) {
                let dateString = value + 'Z'; // Add 'Z' to indicate UTC
                let dateObject = new Date(dateString);

                // Adding one hour in UTC
                dateObject.setUTCHours(dateObject.getUTCHours() + 1);

                // Formatting back to a similar string in UTC
                let newDateString = dateObject.toISOString().split('.')[0];

                this.form.end_date = newDateString;
            },
        },
    },
    methods: {
        submitForm() {
            this.form.clearErrors();
            
            if (this.appointment && this.appointment.data && this.appointment.data.appointment_id) {
                this.updateRequest();
            } else {
                this.storeRequest();
            }
        },
        storeRequest() {
            this.form.post(this.route('panel.appointments.store'), {
                onSuccess: () => this.$toast().success("Appointment created successfully!"),
                onError: (errors) => {
                    Object.keys(errors).forEach((key) => {
                        const error = errors[key];
                        this.$toast().error(error);
                    });
                },
            });
        },
        updateRequest() {
            this.form.patch(this.route('panel.appointments.update', { appointment: this.appointment.data.appointment_id }), {
                onSuccess: () => this.$toast().success("Appointment updated successfully!"),
                onError: (errors) => {
                    Object.keys(errors).forEach((key) => {
                        const error = errors[key];
                        this.$toast().error(error);
                    });
                },
            });
        },
        deleteRequest() {
            this.$alert().confirm(() => {
                return new Promise((resolve, reject) => {
                    this.form.delete(this.route('panel.appointments.destroy', { appointment: this.appointment.data.appointment_id }), {
                        onSuccess: () => {
                            this.$toast().success("Appointment deleted successfully!");
                            resolve();
                        },
                        onError: (errors) => {
                            Object.keys(errors).forEach((key) => {
                                const error = errors[key];
                                this.$toast().error(error);
                            });

                            reject();
                        },
                    });
                });
            });
        },
    },
};
</script>
