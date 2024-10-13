<template>
    <v-inertia-head :title="(!referralType) ? 'Create Referral Type' : `Editing '${referralType.data.name}' referralType.`" />

    <v-form class="h-full" @submit.prevent="submitForm">
        <v-content-body class="border-b border-gray-200">
            <v-section-heading>
                <template #title>
                    <template v-if="!referralType">
                        Create Referral Type
                    </template>

                    <template v-else>
                        Editing '{{ referralType.data.name }}' referral type.
                    </template>
                </template>

                <template #description>
                    <template v-if="!referralType">
                        Fill up the form below to create a new referral type for the application.
                    </template>

                    <template v-else>
                        Change and/or modify the details for the referral type.
                    </template>
                </template>
            </v-section-heading>
        </v-content-body>
        
        <div class="grid grid-cols-1 divide-y divide-gray-200 gap-12 pb-12">
            <v-form-group class="divide-y divide-gray-200">
                <v-section-divider>
                    General Details
                </v-section-divider>

                <v-content-body class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-x-6 gap-y-4">
                    <v-form-group class="col-span-full">
                        <v-form-label>Name</v-form-label>
                        <v-form-input type="text" v-model="form.name" :error="form.errors.name" :disabled="form.processing" />
                        <v-form-error v-if="form.errors.name">{{ form.errors.name }}</v-form-error>
                    </v-form-group>

                    <v-form-group class="col-span-full">
                        <v-form-label>Description</v-form-label>
                        <v-form-textarea v-model="form.description" :error="form.errors.description" :disabled="form.processing"></v-form-textarea>
                        <v-form-error v-if="form.errors.description">{{ form.errors.description }}</v-form-error>
                    </v-form-group>
                </v-content-body>
            </v-form-group>
        </div>

        <v-form-group class="bg-gray-50 flex items-center justify-end gap-2 text-right border-t border-gray-200">
            <v-content-body class="flex gap-4">
                <progress v-if="form.progress" :value="form.progress.percentage" max="100">
                    {{ form.progress.percentage }}%
                </progress>

                <v-button :href="route('panel.referral-types.index')" color="white" :disabled="form.processing">
                    Cancel
                </v-button>

                <template v-if="!referralType">
                    <v-button :disabled="form.processing">
                        Create Referral Type
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
    name: "ReferralTypesCreateEdit",
    layout: Layout,
    props: {
        referralType: {
            type: Object,
            required: false,
            default: () => {},
        },
    },
    data() {
        return {
            form: useForm({
                name: "",
                description: "",
            }),
        };
    },
    watch: {
        referralType: {
            handler(value) {
                if (!value || !value.data) {
                    return;
                }

                this.form.name = value.data.name;
                this.form.description = value.data.description;
            },
            immediate: true,
        },
    },
    methods: {
        submitForm() {
            this.form.clearErrors();
            
            if (this.referralType && this.referralType.data && this.referralType.data.referral_type_id) {
                this.updateRequest();
            } else {
                this.storeRequest();
            }
        },
        storeRequest() {
            this.form.post(this.route('panel.referral-types.store'), {
                onSuccess: () => this.$toast().success("Referral type created successfully!"),
                onError: (errors) => {
                    Object.keys(errors).forEach((key) => {
                        const error = errors[key];
                        this.$toast().error(error);
                    });
                },
            });
        },
        updateRequest() {
            this.form.patch(this.route('panel.referral-types.update', { referral_type: this.referralType.data.referral_type_id }), {
                onSuccess: () => this.$toast().success("Referral type updated successfully!"),
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
                    this.form.delete(this.route('panel.referral-types.destroy', { referralType: this.referralType.data.referral_type_id }), {
                        onSuccess: () => {
                            this.$toast().success("Referral type deleted successfully!");
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
