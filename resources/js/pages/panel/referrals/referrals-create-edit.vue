<template>
    <div>
        <v-inertia-head :title="(referral && referral.data && referral.data.referral_id) ? 'Edit Referral' : 'Create Referral'" />

        <v-content-body>
            <v-section-heading>
                <template #title>
                    <template v-if="referral && referral.data && referral.data.referral_id">
                        Edit Referral
                    </template>

                    <template v-else>
                        Create Referral
                    </template>
                </template>

                <template #actions v-if="referral && referral.data && referral.data.referral_id">
                    <div class="flex items-center justify-between gap-2">
                        <template v-if="referral.data.appointment_id">
                            <v-button :href="route('panel.appointments.show', { appointment: referral.data.appointment_id })" color="white" size="sm">
                                <div class="flex gap-2">
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-5 h-5">
                                        <path d="M12.75 12.75a.75.75 0 1 1-1.5 0 .75.75 0 0 1 1.5 0ZM7.5 15.75a.75.75 0 1 0 0-1.5.75.75 0 0 0 0 1.5ZM8.25 17.25a.75.75 0 1 1-1.5 0 .75.75 0 0 1 1.5 0ZM9.75 15.75a.75.75 0 1 0 0-1.5.75.75 0 0 0 0 1.5ZM10.5 17.25a.75.75 0 1 1-1.5 0 .75.75 0 0 1 1.5 0ZM12 15.75a.75.75 0 1 0 0-1.5.75.75 0 0 0 0 1.5ZM12.75 17.25a.75.75 0 1 1-1.5 0 .75.75 0 0 1 1.5 0ZM14.25 15.75a.75.75 0 1 0 0-1.5.75.75 0 0 0 0 1.5ZM15 17.25a.75.75 0 1 1-1.5 0 .75.75 0 0 1 1.5 0ZM16.5 15.75a.75.75 0 1 0 0-1.5.75.75 0 0 0 0 1.5ZM15 12.75a.75.75 0 1 1-1.5 0 .75.75 0 0 1 1.5 0ZM16.5 13.5a.75.75 0 1 0 0-1.5.75.75 0 0 0 0 1.5Z" />
                                        <path fill-rule="evenodd" d="M6.75 2.25A.75.75 0 0 1 7.5 3v1.5h9V3A.75.75 0 0 1 18 3v1.5h.75a3 3 0 0 1 3 3v11.25a3 3 0 0 1-3 3H5.25a3 3 0 0 1-3-3V7.5a3 3 0 0 1 3-3H6V3a.75.75 0 0 1 .75-.75Zm13.5 9a1.5 1.5 0 0 0-1.5-1.5H5.25a1.5 1.5 0 0 0-1.5 1.5v7.5a1.5 1.5 0 0 0 1.5 1.5h13.5a1.5 1.5 0 0 0 1.5-1.5v-7.5Z" clip-rule="evenodd" />
                                    </svg>

                                    View Appointment
                                </div>
                            </v-button>
                        </template>

                        <template v-else>
                            <v-button :href="route('panel.appointments.create', { referral_id: referral.data.referral_id })" color="white" size="sm">
                                <div class="flex gap-2">
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-5 h-5">
                                        <path d="M12.75 12.75a.75.75 0 1 1-1.5 0 .75.75 0 0 1 1.5 0ZM7.5 15.75a.75.75 0 1 0 0-1.5.75.75 0 0 0 0 1.5ZM8.25 17.25a.75.75 0 1 1-1.5 0 .75.75 0 0 1 1.5 0ZM9.75 15.75a.75.75 0 1 0 0-1.5.75.75 0 0 0 0 1.5ZM10.5 17.25a.75.75 0 1 1-1.5 0 .75.75 0 0 1 1.5 0ZM12 15.75a.75.75 0 1 0 0-1.5.75.75 0 0 0 0 1.5ZM12.75 17.25a.75.75 0 1 1-1.5 0 .75.75 0 0 1 1.5 0ZM14.25 15.75a.75.75 0 1 0 0-1.5.75.75 0 0 0 0 1.5ZM15 17.25a.75.75 0 1 1-1.5 0 .75.75 0 0 1 1.5 0ZM16.5 15.75a.75.75 0 1 0 0-1.5.75.75 0 0 0 0 1.5ZM15 12.75a.75.75 0 1 1-1.5 0 .75.75 0 0 1 1.5 0ZM16.5 13.5a.75.75 0 1 0 0-1.5.75.75 0 0 0 0 1.5Z" />
                                        <path fill-rule="evenodd" d="M6.75 2.25A.75.75 0 0 1 7.5 3v1.5h9V3A.75.75 0 0 1 18 3v1.5h.75a3 3 0 0 1 3 3v11.25a3 3 0 0 1-3 3H5.25a3 3 0 0 1-3-3V7.5a3 3 0 0 1 3-3H6V3a.75.75 0 0 1 .75-.75Zm13.5 9a1.5 1.5 0 0 0-1.5-1.5H5.25a1.5 1.5 0 0 0-1.5 1.5v7.5a1.5 1.5 0 0 0 1.5 1.5h13.5a1.5 1.5 0 0 0 1.5-1.5v-7.5Z" clip-rule="evenodd" />
                                    </svg>

                                    Create Appointment
                                </div>
                            </v-button>
                        </template>

                        <v-button :href="route('panel.referrals.documents.summary', { referral: referral.data.referral_id })" target="_blank" :anchor="true" color="white" size="sm">
                            <div class="flex gap-2">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-5 h-5">
                                    <path fill-rule="evenodd" d="M10.5 3.75a6 6 0 0 0-5.98 6.496A5.25 5.25 0 0 0 6.75 20.25H18a4.5 4.5 0 0 0 2.206-8.423 3.75 3.75 0 0 0-4.133-4.303A6.001 6.001 0 0 0 10.5 3.75Zm2.25 6a.75.75 0 0 0-1.5 0v4.94l-1.72-1.72a.75.75 0 0 0-1.06 1.06l3 3a.75.75 0 0 0 1.06 0l3-3a.75.75 0 1 0-1.06-1.06l-1.72 1.72V9.75Z" clip-rule="evenodd" />
                                </svg>

                                Summary
                            </div>
                        </v-button>
                    </div>
                </template>
            </v-section-heading>
        </v-content-body>

        <v-form @submit.prevent="submitForm">
            <v-app-form-builder :field-groups="fieldGroups" />

            <v-form-group class="col-span-full flex items-center justify-end gap-2 text-right border-t border-gray-200 px-6 py-4">
                <progress v-if="form.progress" :value="form.progress.percentage" max="100">
                    {{ form.progress.percentage }}%
                </progress>

                <v-button :href="route('panel.referrals.index')" color="white" :disabled="form.processing">
                    Cancel
                </v-button>

                <template v-if="referral && referral.data">
                    <template v-if="referral.data.referral_status_id == 1">
                        <v-button type="submit" color="white" :disabled="form.processing">
                            Update Draft
                        </v-button>
                    </template>

                    <v-button type="submit" color="primary" :disabled="form.processing" @click="form.referral_status_id = 2">
                        <template v-if="referral.data.referral_status_id == 1">
                            Send Referral
                        </template>

                        <template v-else>
                            Update Referral
                        </template>
                    </v-button>
                </template>

                <template v-else>
                    <v-button type="submit" color="white" :disabled="form.processing" @click="form.referral_status_id = 1">
                        Save Draft
                    </v-button>

                    <v-button type="submit" color="primary" :disabled="form.processing" @click="form.referral_status_id = 2">
                        Send Referral
                    </v-button>
                </template>
            </v-form-group>
        </v-form>
    </div>
</template>

<script>
import { computed, inject, markRaw, reactive, ref, watch } from "vue";
import { router, useForm, usePage } from "@inertiajs/vue3";
import Layout from "@/layouts/panel/index.vue";
import _ReferralGeneralFormData from "@/components/application/referral/_referral-general-form-data.vue";
import _ReferralPatientFormData from "@/components/application/referral/_referral-patient-form-data.vue";
import _ReferralRecipientFormData from "@/components/application/referral/_referral-recipient-form-data.vue";

export default {
    name: "ReferralsCreateEdit",
    layout: Layout,
    props: {
        documentCategories: {
            type: Object,
            required: false,
        },
        patients: {
            type: Object,
            required: true,
        },
        procedures: {
            type: Object,
            required: true
        },
        recipients: {
            type: Object,
            required: true,
        },
        referralReasons: {
            type: Object,
            required: true,
        },
        referral: {
            type: Object,
            required: false,
            default: () => {},
        },
        states: {
            type: Object,
            required: true,
        },
    },
    setup(props) {
        /**
         * Get the auth plugin.
         */
        const $auth = inject("auth");

        /**
         * Get the alert plugin.
         */
         const $alert = inject("alert");

        /**
         * Get the toast plugin.
         */
         const $toast = inject("toast");

        /**
         * Get the page data.
         */
        const page = usePage();

        /**
         * Create the form data.
         */
        const form = useForm({
            // General Information
            _method: "post",
            referral_status_id: 2,
            referral_date: (new Date().toISOString().split("T")[0]),
            state_id: page.props.auth.user.state_id || page.props.query.state_id,
            procedure_id: "",
            referral_reason_ids: [],
            notes: "",

            // Patient Information
            patient_create: (!props.patients.data || !props.patients.data.length) || false,
            patient_user_id: "",
            patient_name: "",
            patient_email: "",
            patient_phone_number: "",
            patient_height: "",
            patient_weight: "",
            patient_gender: "",
            patient_birthdate: "",
            patient_address_line_1: "",
            patient_address_line_2: "",
            patient_city: "",
            patient_state_id: "",
            patient_zip_code: "",

            // Recipient Information
            recipient_user_id: "",

            // Document Information
            documents: {},
        });

        /**
         * Create the form documents.
         */
        const formDocuments = reactive({});

        if (props.referral && props.referral.data) {
            // General Information
            form._method = "patch";
            form.referral_status_id = props.referral.data.referral_status_id;
            form.referral_date = (new Date(props.referral.data.referral_date).toISOString().split("T")[0]);
            form.state_id = props.referral.data.state_id;
            form.procedure_id = props.referral.data.procedure_id;
            form.referral_reason_ids = props.referral.data.referral_reasons.map(({ referral_reason_id }) => referral_reason_id);
            form.notes = props.referral.data.notes;
            form.patient_user_id = props.referral.data.patient_user_id;
            form.recipient_user_id = props.referral.data.recipient_user_id;

            props.referral.data.documents.forEach((document) => {
                formDocuments[document.document_type_id] = document;
            });
        }

        /**
         * Watch Data
         */
        if (!props.referral || !props.referral.data) {
            watch(() => form.state_id, (stateId) => {
                if (!stateId) return;

                const payload = {
                    ...page.props.query,
                    state_id: stateId || undefined,
                };

                // Make an Inertia visit to the current route with updated query parameters
                router.visit(route(route().current()), {
                    method: 'get',
                    data: payload,
                    preserveState: true,
                    preserveScroll: true,
                    replace: true,
                });

                form.patient_user_id = "";
                form.recipient_user_id = "";
            }, {
                immediate: true,
            });
        }

        /**
         * Create the field data.
         */
        const fieldGroups = ref([
            {
                title: "General Information",
                fields: [
                    {
                        type: "date",
                        label: "Referral Date",
                        model: computed({
                            get: () => form.referral_date,
                            set: (value) => (form.referral_date = value)
                        }),
                        required: true,
                        attributes: computed(() => ({
                            disabled: form.processing || (props.referral && props.referral.data && props.referral.data.referral_status_id != 1),
                        })),
                    },
                    {
                        type: "select",
                        label: "Referral State",
                        model: computed({
                            get: () => form.state_id,
                            set: (value) => (form.state_id = value)
                        }),
                        options: [{ label: "Select State", value: "", }, ...props.states.data.map(({ name: label, state_id: value}) => ({ label, value}))],
                        required: true,
                        attributes: computed(() => ({
                            disabled: form.processing || (props.referral && props.referral.data),
                        })),
                    },
                    {
                        type: "select",
                        label: "Procedure (CPT)",
                        model: computed({
                            get: () => form.procedure_id,
                            set: (value) => (form.procedure_id = value)
                        }),
                        options: [{ label: "Select Procedure", value: "", }, ...props.procedures.data.map(({ name: label, procedure_id: value}) => ({ label, value }))],
                        required: true,
                        visible: computed(() => !(props.referral && props.referral.data && props.referral.data.referral_status_id != 1)),
                    },
                    {
                        type: "select",
                        label: "Referral Reasons (ICD)",
                        model: computed({
                            get: () => form.referral_reason_ids,
                            set: (value) => (form.referral_reason_ids = value)
                        }),
                        options: [{ label: "Select Reasons", value: "", }, ...props.referralReasons.data.map(({ name: label, referral_reason_id: value}) => ({ label, value }))],
                        required: true,
                        attributes: {
                            multiple: true,
                        },
                        visible: computed(() => !(props.referral && props.referral.data && props.referral.data.referral_status_id != 1)),
                        class: "w-full max-w-prose col-span-full",
                    },
                    {
                        type: "textarea",
                        label: "Other Notes",
                        model: computed({
                            get: () => form.notes,
                            set: (value) => (form.notes = value)
                        }),
                        required: false,
                        class: "w-full col-span-full",
                        attributes: computed(() => ({
                            placeholder: "Enter additional notes for the referral.",
                            disabled: form.processing,
                        })),
                        visible: computed(() => !(props.referral && props.referral.data && props.referral.data.referral_status_id != 1)),
                    },
                    {
                        type: "component",
                        component: markRaw(_ReferralGeneralFormData),
                        bind: {
                            referral: props.referral
                        },
                        visible: computed(() => (props.referral && props.referral.data && props.referral.data.referral_status_id != 1)),
                    },
                ],
            },
            {
                title: "Patient Information",
                fields: [
                    {
                        type: "toggle",
                        label: "Options",
                        toggleLabel: "Create New Patient",
                        model: computed({
                            get: () => form.patient_create,
                            set: (value) => (form.patient_create = value)
                        }),
                        required: false,
                        error: (computed(() => form.errors.patient_create)),
                        visible: computed(() => !(props.referral && props.referral.data && props.referral.data.referral_status_id != 1)),
                    },
                    {
                        type: "select",
                        label: "Select Patient",
                        model: computed({
                            get: () => form.patient_user_id,
                            set: (value) => (form.patient_user_id = value)
                        }),
                        options: computed(() => props.patients.data.map(({ name: label, user_id: value, email: description }) => ({ label, value, description }))),
                        required: true,
                        error: (computed(() => form.errors.patient_user_id)),
                        visible: computed(() => !form.patient_create && (!props.referral || !props.referral.data)),
                    },
                    {
                        type: "divider",
                        visible: computed(() => !(props.referral && props.referral.data && props.referral.data.referral_status_id != 1)),
                    },
                    {
                        type: "text",
                        label: "Name",
                        model: computed({
                            get: () => form.patient_name,
                            set: (value) => (form.patient_name = value)
                        }),
                        required: true,
                        error: (computed(() => form.errors.patient_name)),
                        visible: computed(() => !!form.patient_create),
                    },
                    {
                        type: "email",
                        label: "Email",
                        model: computed({
                            get: () => form.patient_email,
                            set: (value) => (form.patient_email = value)
                        }),
                        required: true,
                        error: (computed(() => form.errors.patient_email)),
                        visible: computed(() => !!form.patient_create),
                    },
                    {
                        type: "text",
                        label: "Phone Number",
                        model: computed({
                            get: () => form.patient_phone_number,
                            set: (value) => (form.patient_phone_number = value)
                        }),
                        required: false,
                        error: (computed(() => form.errors.patient_phone_number)),
                        visible: computed(() => !!form.patient_create),
                    },
                    {
                        type: "text",
                        label: "Height",
                        model: computed({
                            get: () => form.patient_height,
                            set: (value) => (form.patient_height = value)
                        }),
                        required: false,
                        error: (computed(() => form.errors.patient_height)),
                        visible: computed(() => !!form.patient_create),
                    },
                    {
                        type: "text",
                        label: "Weight",
                        model: computed({
                            get: () => form.patient_weight,
                            set: (value) => (form.patient_weight = value)
                        }),
                        required: false,
                        error: (computed(() => form.errors.patient_weight)),
                        visible: computed(() => !!form.patient_create),
                    },
                    {
                        type: "text",
                        label: "Gender",
                        model: computed({
                            get: () => form.patient_gender,
                            set: (value) => (form.patient_gender = value)
                        }),
                        required: false,
                        error: (computed(() => form.errors.patient_gender)),
                        visible: computed(() => !!form.patient_create),
                    },
                    {
                        type: "text",
                        label: "Address Line 1",
                        model: computed({
                            get: () => form.patient_address_line_1,
                            set: (value) => (form.patient_address_line_1 = value)
                        }),
                        required: false,
                        error: (computed(() => form.errors.patient_address_line_1)),
                        visible: computed(() => !!form.patient_create),
                    },
                    {
                        type: "text",
                        label: "Address Line 2",
                        model: computed({
                            get: () => form.patient_address_line_2,
                            set: (value) => (form.patient_address_line_2 = value)
                        }),
                        required: false,
                        error: (computed(() => form.errors.patient_address_line_2)),
                        visible: computed(() => !!form.patient_create),
                    },
                    {
                        type: "text",
                        label: "City",
                        model: computed({
                            get: () => form.patient_city,
                            set: (value) => (form.patient_city = value)
                        }),
                        required: false,
                        error: (computed(() => form.errors.patient_city)),
                        visible: computed(() => !!form.patient_create),
                    },
                    {
                        type: "select",
                        label: "State",
                        model: computed({
                            get: () => form.patient_state_id,
                            set: (value) => (form.patient_state_id = value)
                        }),
                        options: [{ label: "Select State", value: "", }, ...props.states.data.map(({ name: label, state_id: value}) => ({ label, value}))],
                        required: true,
                        error: (computed(() => form.errors.patient_state_id)),
                        visible: computed(() => !!form.patient_create),
                    },
                    {
                        type: "text",
                        label: "Zip Code",
                        model: computed({
                            get: () => form.patient_zip_code,
                            set: (value) => (form.patient_zip_code = value)
                        }),
                        required: false,
                        error: (computed(() => form.errors.patient_zip_code)),
                        visible: computed(() => !!form.patient_create),
                    },
                    {
                        type: "date",
                        label: "Birthdate",
                        model: computed({
                            get: () => form.patient_birthdate,
                            set: (value) => (form.patient_birthdate = value)
                        }),
                        required: false,
                        error: (computed(() => form.errors.patient_birthdate)),
                        visible: computed(() => !!form.patient_create),
                    },
                    {
                        type: "component",
                        component: markRaw(_ReferralPatientFormData),
                        bind: {
                            referral: props.referral
                        },
                        visible: computed(() => (props.referral && props.referral.data && props.referral.data.referral_status_id != 1)),
                    },
                ],
            },
            {
                title: "Recipient Information",
                fields: [
                    {
                        type: "radio-card-array",
                        model: computed({
                            get: () => form.recipient_user_id,
                            set: (value) => (form.recipient_user_id = value)
                        }),
                        options: computed(() => props.recipients.data.map(({ name: label, user_id: value, email: description }) => ({ label, value, description }))),
                        required: true,
                        error: (computed(() => form.errors.recipient_user_id)),
                        class: "w-full col-span-full",
                        visible: computed(() => !(props.referral && props.referral.data && props.referral.data.referral_status_id != 1)),
                    },
                    {
                        type: "component",
                        component: markRaw(_ReferralRecipientFormData),
                        bind: {
                            referral: props.referral
                        },
                        visible: computed(() => (props.referral && props.referral.data && props.referral.data.referral_status_id != 1)),
                    },
                ],
            },
            {
                title: "Document Information",
                fields: props.documentCategories.data
                    .filter((documentCategory) => {
                        return Object.keys(documentCategory.document_types
                            .filter((documentType) => {
                                return $auth().can([`document-type.${documentType.document_type_id}.show`]);
                            })).length;
                    })
                    .flatMap((documentCategory, documentCategoryIndex) => {
                        const documentTypes = documentCategory.document_types;

                        return [
                            (function () {
                                if (documentCategoryIndex > 0) return { type: "divider" };
                                return {};
                            })(),
                            {
                                type: "heading",
                                label: documentCategory.name,
                            },
                            ...documentTypes
                                .filter((documentType) => {
                                    return $auth().can([`document-type.${documentType.document_type_id}.show`]);
                                })
                                .map((documentType) => ({
                                    type: "file",
                                    label: documentType.name,
                                    model: computed({
                                        get: () => form.documents[documentType.document_type_id],
                                        set: (value) => form.documents[documentType.document_type_id] = value,
                                    }),
                                    modelCurrent: computed(() => formDocuments[documentType.document_type_id]),
                                    file: {
                                        showFile: (field) => route('panel.documents.download', { document: field.modelCurrent.document_id, _t: Date.now() }),
                                        destroyFile: (field) => {
                                            $alert().confirm(() => {
                                                return new Promise((resolve, reject) => {
                                                    form.delete(route("panel.documents.destroy", { document: field.modelCurrent.document_id }), {
                                                        onSuccess: () => {
                                                            $toast().success("Document deleted successfully!");
                                                            form.documents[documentType.document_type_id] = null;
                                                            formDocuments[documentType.document_type_id] = null;
                                                            router.reload({});
                                                            resolve();
                                                        },
                                                        onError: (errors) => {
                                                            Object.keys(errors).forEach((key) => {
                                                                const error = errors[key];
                                                                $toast().error(error);
                                                            });
                                                            reject(errors);
                                                        },
                                                    });
                                                });
                                            });
                                        },
                                    },
                                    attributes: computed(() => ({
                                        disabled: form.processing || (!$auth().can([`document-type.${documentType.document_type_id}.store`]) && $auth().can([`document-type.${documentType.document_type_id}.update`])),
                                    })),
                                    error: (computed(() => form.errors[`documents.${documentType.document_type_id}`])),
                                    permissions: {
                                        can_list: $auth().can([`document-type.${documentType.document_type_id}.list`]),
                                        can_show: $auth().can([`document-type.${documentType.document_type_id}.show`]),
                                        can_store: $auth().can([`document-type.${documentType.document_type_id}.store`]),
                                        can_update: $auth().can([`document-type.${documentType.document_type_id}.update`]),
                                        can_delete: $auth().can([`document-type.${documentType.document_type_id}.delete`]),
                                    },
                                })),
                        ].filter((item) => item.type);
                    })
            },
        ]);

        const submitStoreRequest = () => {
            form.post(route('panel.referrals.store'), {
                preserveState: false,
                onSuccess: () => $toast().success("Referral created successfully!"),
                onError: (errors) => {
                    Object.keys(errors).forEach((key) => {
                        const error = errors[key];
                        $toast().error(error);
                    });
                },
            });
        };

        const submitUpdateRequest = () => {
            form.post(route('panel.referrals.update', { referral: props.referral.data.referral_id }), {
                preserveState: false,
                onSuccess: () => $toast().success("Referral updated successfully!"),
                onError: (errors) => {
                    Object.keys(errors).forEach((key) => {
                        const error = errors[key];
                        $toast().error(error);
                    });
                },
            });
        };

        const submitForm = () => {
            form.clearErrors();
            const hasReferral = !(!props.referral || !props.referral.data || !props.referral.data.referral_id);
            if (hasReferral) submitUpdateRequest();
            else submitStoreRequest();
        }

        return {
            form,
            fieldGroups,
            referral: props.referral,
            submitForm,
        };
    },
};
</script>
