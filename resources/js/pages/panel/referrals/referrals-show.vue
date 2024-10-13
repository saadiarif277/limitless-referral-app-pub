<template>
    <v-inertia-head title="Show Referral" />

    <div>
        {{ referral }}
    </div>
</template>

<script>
import { computed, ref, inject } from "vue";
import Layout from "@/layouts/panel/index.vue";
import { useForm, usePage } from "@inertiajs/vue3";

export default {
    name: "ReferralsShow",
    layout: Layout,
    props: {
        referral: {
            type: Object,
            required: true
        },
    },
    setup(props) {
        /**
         * Get the toast plugin.
         */
        const toast = inject("toast");

        /**
         * Get the page data.
         */
        const page = usePage();

        /**
         * Create the form data.
         */
        const form = useForm({
            // General Information
            referral_status_id: 2,
            referral_date: (new Date().toISOString().split("T")[0]),
            state_id: page.props.auth.user.state_id || "",
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
                    },
                    {
                        type: "select",
                        label: "Procedure",
                        model: computed({
                            get: () => form.procedure_id,
                            set: (value) => (form.procedure_id = value)
                        }),
                        options: [{ label: "Select Procedure", value: "", }, ...props.procedures.data.map(({ name: label, procedure_id: value}) => ({ label, value }))],
                        required: true,
                        class: "col-span-full sm:col-span-2"
                    },
                    {
                        type: "checkbox-array",
                        label: "Referral Reasons",
                        model: computed({
                            get: () => form.referral_reason_ids,
                            set: (value) => (form.referral_reason_ids = value)
                        }),
                        options: props.referralReasons.data.map(({ name: label, referral_reason_id: value}) => ({ label, value })),
                        required: false,
                        class: "w-full col-span-full",
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
                    },
                ],
            },
            {
                title: "Patient Information",
                fields: [
                    {
                        type: "toggle",
                        toggleLabel: "Create New Patient",
                        model: computed({
                            get: () => form.patient_create,
                            set: (value) => (form.patient_create = value)
                        }),
                        required: false,
                        error: (computed(() => form.errors.patient_create)),
                    },
                    {
                        type: "divider",
                    },
                    {
                        type: "radio-card-array",
                        model: computed({
                            get: () => form.patient_user_id,
                            set: (value) => (form.patient_user_id = value)
                        }),
                        options: props.patients.data.map(({ name: label, user_id: value, email: description }) => ({ label, value, description })),
                        required: true,
                        error: (computed(() => form.errors.patient_user_id)),
                        class: "w-full col-span-full",
                        visible: computed(() => !form.patient_create),
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
                        options: props.recipients.data.map(({ name: label, user_id: value, email: description }) => ({ label, value, description })),
                        required: true,
                        error: (computed(() => form.errors.recipient_user_id)),
                        class: "w-full col-span-full",
                    },
                ],
            },
            {
                title: "Document Information",
                fields: props.documentCategories.data.flatMap((documentCategory, documentCategoryIndex) => {
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
                        ...documentTypes.map((documentType) => ({
                            type: "file",
                            label: documentType.name,
                            model: computed({
                                get: () => form.documents[documentType.document_type_id],
                                set: (value) => form.documents[documentType.document_type_id] = value,
                            }),
                            attributes: computed(() => ({
                                disabled: form.processing,
                            })),
                            error: (computed(() => form.errors[`documents.${documentType.document_type_id}`])),
                        })),
                    ].filter((item) => item.type);
                })
            },
        ]);

        const submitForm = () => {
            form.clearErrors();
    
            form.post(route("panel.referrals.store"), {
                onSuccess: () => toast().success("Referral created successfully!"),
                onError: (errors) => {
                    Object.keys(errors).forEach((key) => {
                        const error = errors[key];
                        toast().error(error);
                    });
                },
            });
        }

        return {
            form,
            fieldGroups,
            submitForm
        };
    },
};
</script>
