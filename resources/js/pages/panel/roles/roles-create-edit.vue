<template>
    <v-inertia-head :title="(!role) ? 'Create Role' : `Editing '${role.data.name}' role.`" />

    <v-form class="h-full" @submit.prevent="submitForm">
        <v-content-body class="border-b border-gray-200">
            <v-section-heading>
                <template #title>
                    <template v-if="!role">
                        Create Role
                    </template>

                    <template v-else>
                        Editing '{{ role.data.name }}' role.
                    </template>
                </template>

                <template #description>
                    <template v-if="!role">
                        Fill up the form below to create a new role for the application.
                    </template>

                    <template v-else>
                        Change and/or modify the details for the role.
                    </template>
                </template>

                <template #actions>
                    <template v-if="role && this.$auth().can(['role.destroy'])">
                        <div class="flex gap-2">
                            <v-button color="danger" :disabled="form.processing" @click.prevent="deleteRequest">
                                Delete Role
                            </v-button>
                        </div>
                    </template>
                </template>
            </v-section-heading>
        </v-content-body>

        <div class="grid grid-cols-1 divide-y divide-gray-200 gap-12 pb-12">
            <v-form-group class="divide-y divide-gray-200">
                <v-section-divider>
                    General Details
                </v-section-divider>

                <v-content-body class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4">
                    <v-form-group>
                        <v-form-label>Name</v-form-label>
                        <v-form-input type="text" v-model="form.name" :error="form.errors.name" :disabled="form.processing || role" />
                        <v-form-error v-if="form.errors.name">{{ form.errors.name }}</v-form-error>
                    </v-form-group>
                </v-content-body>
            </v-form-group>

            <v-form-group class="divide-y divide-gray-200">
                <v-section-divider>
                    Document Access
                </v-section-divider>
                
                <v-form-group>
                    <v-table class="border-gray-200">
                        <v-table-row>
                            <v-table-header class="hidden md:table-cell">Document Category</v-table-header>
                            <v-table-header class="border-l border-gray-200">Document Type</v-table-header>
                            <v-table-header class="border-l border-gray-200">Permissions</v-table-header>
                        </v-table-row>

                        <template v-for="(documentCategory, documentCategoryIndex) in documentCategories.data" :key="'documentCategory_' + documentCategoryIndex">
                            <v-table-row>
                                <v-table-header colspan="100%">
                                    {{ documentCategory.name }}
                                </v-table-header>
                            </v-table-row>

                            <template v-for="(documentType, documentTypeIndex) in documentCategory.document_types" :key="'documentType_' + documentTypeIndex">
                                <v-table-row>
                                    <v-table-data class="hidden md:table-cell" :class="parseInt(documentTypeIndex) + 1 < parseInt(documentCategory.document_types.length) ? 'border-white border-b' : ''"></v-table-data>

                                    <v-table-data class="border-l border-b border-gray-200">
                                        {{ documentType.name }}
                                    </v-table-data>

                                    <v-table-data class="border-l border-b border-gray-200">
                                        <template v-if="getDocumentTypePermission(documentType.document_type_id, 'show')">
                                            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4">
                                                <v-form-checkbox v-model="form.permission_ids" :value="getDocumentTypePermission(documentType.document_type_id, 'show').id" :disabled="form.processing">
                                                    Show
                                                </v-form-checkbox>

                                                <v-form-checkbox v-model="form.permission_ids" :value="getDocumentTypePermission(documentType.document_type_id, 'store').id" :disabled="form.processing">
                                                    Store
                                                </v-form-checkbox>

                                                <v-form-checkbox v-model="form.permission_ids" :value="getDocumentTypePermission(documentType.document_type_id, 'update').id" :disabled="form.processing">
                                                    Update
                                                </v-form-checkbox>

                                                <v-form-checkbox v-model="form.permission_ids" :value="getDocumentTypePermission(documentType.document_type_id, 'delete').id" :disabled="form.processing">
                                                    Delete
                                                </v-form-checkbox>
                                            </div>
                                        </template>
                                    </v-table-data>
                                </v-table-row>
                            </template>
                        </template>
                    </v-table>

                    <v-form-error v-if="form.errors.document_category_ids">{{ form.errors.document_category_ids }}</v-form-error>
                </v-form-group>
            </v-form-group>

            <v-form-group class="divide-y divide-gray-200">
                <v-section-divider>
                    Application Permissions
                </v-section-divider>

                <v-form-group>
                    <v-table>
                        <v-table-body>
                            <template v-for="(permissionGroup, permissionGroupIndex) in permissionGroups" :key="'permissionGroup_' + permissionGroupIndex">
                                <v-table-row>
                                    <v-table-data>
                                        <span class="capitalize">
                                            {{ permissionGroupIndex.replace("-", " ") }}
                                        </span>
                                    </v-table-data>

                                    <v-table-data>
                                        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4">
                                            <template v-for="(permission, permissionIndex) in _.orderBy(permissionGroup, ['name'], ['asc'])" :key="'permission_' + permissionIndex">
                                                <v-form-checkbox v-model="form.permission_ids" :value="permission.id" :disabled="form.processing">
                                                    <span class="capitalize">
                                                        {{ _.startCase(permission.name.replace(`${permissionGroupIndex}.`, "")) }}
                                                    </span>
                                                </v-form-checkbox>
                                            </template>
                                        </div>
                                    </v-table-data>
                                </v-table-row>
                            </template>
                        </v-table-body>
                    </v-table>
                    
                    <template v-if="form.errors.permission_ids">
                        <v-content-body>
                            <v-form-error>
                                {{ form.errors.permission_ids }}
                            </v-form-error>
                        </v-content-body>
                    </template>
                </v-form-group>
            </v-form-group>
        </div>

        <v-form-group class="bg-gray-50 flex items-center justify-end gap-2 text-right border-t border-gray-200">
            <v-content-body class="flex gap-4">
                <progress v-if="form.progress" :value="form.progress.percentage" max="100">
                    {{ form.progress.percentage }}%
                </progress>

                <v-button :href="route('panel.roles.index')" color="white" :disabled="form.processing">
                    Cancel
                </v-button>

                <template v-if="!role">
                    <v-button :disabled="form.processing">
                        Create Role
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
import _ from "lodash";

export default {
    name: "RolesCreateEdit",
    layout: Layout,
    props: {
        documentCategories: {
            type: Object,
            required: false,
            defualt: () => {},
        },
        permissions: {
            type: Object,
            required: false,
            defualt: () => {},
        },
        role: {
            type: Object,
            required: false,
            default: () => {},
        },
    },
    data() {
        return {
            _,
            form: useForm({
                name: "",
                document_category_ids: [],
                document_type_ids: [],
                permission_ids: [],
            }),
        };
    },
    computed: {
        permissionGroups() {
            const result = {};

            this.permissions.data.forEach((permission) => {
                // Split the permission name by "."
                const parts = permission.name.split(".");
                // The model is the first part
                const model = parts.shift();

                if (!result[model]) {
                    result[model] = [];
                }

                result[model].push(permission);
            });

            return result;
        },
    },
    watch: {
        role: {
            handler(value) {
                if (!value || !value.data) {
                    return;
                }

                this.form.name = value.data.name;
                this.form.permission_ids = value.data.permissions.map((permission) => permission.id);
            },
            immediate: true,
        },
    },
    methods: {
        submitForm() {
            this.form.clearErrors();
            
            if (this.role && this.role.data && this.role.data.id) {
                this.updateRequest();
            } else {
                this.storeRequest();
            }
        },
        storeRequest() {
            this.form.post(this.route('panel.roles.store'), {
                onSuccess: () => this.$toast().success("Role created successfully!"),
                onError: (errors) => {
                    Object.keys(errors).forEach((key) => {
                        const error = errors[key];
                        this.$toast().error(error);
                    });
                },
            });
        },
        updateRequest() {
            this.form.patch(this.route('panel.roles.update', { role: this.role.data.id }), {
                onSuccess: () => this.$toast().success("Role updated successfully!"),
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
                    this.form.delete(this.route('panel.roles.destroy', { role: this.role.data.id }), {
                        onSuccess: () => {
                            this.$toast().success("Role deleted successfully!");
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
        getDocumentTypePermission(documentTypeId, action) {
            return this.permissions.data.find((permission) => (permission.name == `document-type.${documentTypeId}.${action}`));
        },
    },
};
</script>
