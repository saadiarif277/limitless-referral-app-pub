<template>
    <v-inertia-head :title="(!documentCategory) ? 'Create Document Category' : `Editing '${documentCategory.data.name}' document category.`" />

    <v-form class="h-full" @submit.prevent="submitForm">
        <v-content-body class="border-b border-gray-200">
            <v-section-heading>
                <template #title>
                    <template v-if="!documentCategory">
                        Create Document Category
                    </template>

                    <template v-else>
                        Editing '{{ documentCategory.data.name }}' document category.
                    </template>
                </template>

                <template #description>
                    <template v-if="!documentCategory && this.$auth().can(['document-category.destroy'])">
                        Fill up the form below to create a new document category for the application.
                    </template>

                    <template v-else>
                        Change and/or modify the details for the document category.
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

                <v-button :href="route('panel.document-categories.index')" color="white" :disabled="form.processing">
                    Cancel
                </v-button>

                <template v-if="!documentCategory">
                    <v-button :disabled="form.processing">
                        Create Document Category
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
    name: "DocumentCategoriesCreateEdit",
    layout: Layout,
    props: {
        documentCategory: {
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
        documentCategory: {
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
            
            if (this.documentCategory && this.documentCategory.data && this.documentCategory.data.document_category_id) {
                this.updateRequest();
            } else {
                this.storeRequest();
            }
        },
        storeRequest() {
            this.form.post(this.route('panel.document-categories.store'), {
                onSuccess: () => this.$toast().success("Document category created successfully!"),
                onError: (errors) => {
                    Object.keys(errors).forEach((key) => {
                        const error = errors[key];
                        this.$toast().error(error);
                    });
                },
            });
        },
        updateRequest() {
            this.form.patch(this.route('panel.document-categories.update', { document_category: this.documentCategory.data.document_category_id }), {
                onSuccess: () => this.$toast().success("Document category updated successfully!"),
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
                    this.form.delete(this.route('panel.document-categories.destroy', { document_category: this.documentCategory.data.document_category_id }), {
                        onSuccess: () => {
                            this.$toast().success("Document category deleted successfully!");
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
