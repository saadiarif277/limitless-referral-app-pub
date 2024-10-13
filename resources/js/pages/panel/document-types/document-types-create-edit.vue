<template>
    <v-inertia-head :title="(!documentType) ? 'Create Document Type' : `Editing '${documentType.data.name}' document type.`" />

    <v-form class="h-full" @submit.prevent="submitForm">
        <v-content-body class="border-b border-gray-200">
            <v-section-heading>
                <template #title>
                    <template v-if="!documentType">
                        Create Document Type
                    </template>

                    <template v-else>
                        Editing '{{ documentType.data.name }}' document type.
                    </template>
                </template>

                <template #description>
                    <template v-if="!documentType && this.$auth().can(['document-type.destroy'])">
                        Fill up the form below to create a new document type for the application.
                    </template>

                    <template v-else>
                        Change and/or modify the details for the document type.
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
                    <v-form-group class="col-span-full sm:col-span-1">
                        <v-form-label>Name</v-form-label>
                        <v-form-input type="text" v-model="form.name" :error="form.errors.name" :disabled="form.processing" />
                        <v-form-error v-if="form.errors.name">{{ form.errors.name }}</v-form-error>
                    </v-form-group>

                    <v-form-group class="col-span-full sm:col-span-1">
                        <v-form-label>Document Category</v-form-label>
                        <v-form-select
                            :options="documentCategories.data.map((documentCategory) => ({ label: documentCategory.name, value: documentCategory.document_category_id }))"
                            :error="form.errors.document_category_id"
                            :disabled="form.processing"
                            v-model="form.document_category_id"
                        />
                        <v-form-error v-if="form.errors.document_category_id">{{ form.errors.document_category_id }}</v-form-error>
                    </v-form-group>

                    <v-form-group class="col-span-full">
                        <v-form-label>Description</v-form-label>
                        <v-form-textarea v-model="form.description" :error="form.errors.description" :disabled="form.processing"></v-form-textarea>
                        <v-form-error v-if="form.errors.description">{{ form.errors.description }}</v-form-error>
                    </v-form-group>

                    <v-form-group class="col-span-full">
                        <v-form-label>States</v-form-label>
                        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-6">
                            <template v-for="(state, stateIndex) in states.data" :key="'state_' + stateIndex">
                                <v-form-checkbox v-model="form.state_ids" :value="state.state_id" :disabled="form.processing">
                                    {{ state.name }}
                                </v-form-checkbox>
                            </template>
                        </div>
                        <v-form-error v-if="form.errors.state_ids">{{ form.errors.state_ids }}</v-form-error>
                    </v-form-group>
                </v-content-body>
            </v-form-group>
        </div>

        <v-form-group class="bg-gray-50 flex items-center justify-end gap-2 text-right border-t border-gray-200">
            <v-content-body class="flex gap-4">
                <progress v-if="form.progress" :value="form.progress.percentage" max="100">
                    {{ form.progress.percentage }}%
                </progress>

                <v-button :href="route('panel.document-types.index')" color="white" :disabled="form.processing">
                    Cancel
                </v-button>

                <template v-if="!documentType">
                    <v-button :disabled="form.processing">
                        Create Document Type
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
    name: "DocumentTypesCreateEdit",
    layout: Layout,
    props: {
        documentType: {
            type: Object,
            required: false,
            default: () => {},
        },
        documentCategories: {
            type: Object,
            required: false,
            defualt: () => {},
        },
        states: {
            type: Object,
            required: false,
            defualt: () => {},
        },
    },
    data() {
        return {
            form: useForm({
                name: "",
                description: "",
                document_category_id: "",
                state_ids: [],
            }),
        };
    },
    watch: {
        documentType: {
            handler(value) {
                if (!value || !value.data) {
                    return;
                }

                this.form.name = value.data.name;
                this.form.description = value.data.description;
                this.form.document_category_id = value.data.document_category_id;
                this.form.state_ids = value.data.states.map((state) => state.state_id);
            },
            immediate: true,
        },
    },
    methods: {
        submitForm() {
            this.form.clearErrors();
            
            if (this.documentType && this.documentType.data && this.documentType.data.document_type_id) {
                this.updateRequest();
            } else {
                this.storeRequest();
            }
        },
        storeRequest() {
            this.form.post(this.route('panel.document-types.store'), {
                onSuccess: () => this.$toast().success("Document type created successfully!"),
                onError: (errors) => {
                    Object.keys(errors).forEach((key) => {
                        const error = errors[key];
                        this.$toast().error(error);
                    });
                },
            });
        },
        updateRequest() {
            this.form.patch(this.route('panel.document-types.update', { document_type: this.documentType.data.document_type_id }), {
                onSuccess: () => this.$toast().success("Document type updated successfully!"),
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
                    this.form.delete(this.route('panel.document-types.destroy', { document_type: this.documentType.data.document_type_id }), {
                        onSuccess: () => {
                            this.$toast().success("Document type deleted successfully!");
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
