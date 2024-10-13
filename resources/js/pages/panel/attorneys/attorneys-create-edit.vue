<template>
    <v-inertia-head :title="(!user) ? 'Create User' : `Editing '${user.data.name}' user.`" />

    <div class="h-full flex flex-col">
        <v-form @submit.prevent="submitForm">
            <v-content-body class="border-b border-gray-200">
                <v-section-heading>
                    <template #title>
                        <template v-if="!user">
                            Create Attorney
                        </template>

                        <template v-else>
                            Editing '{{ user.data.name }}' attorney.
                        </template>
                    </template>

                    <template #description>
                        <template v-if="!user">
                            Fill up the form below to create a new attorney for the application.
                        </template>

                        <template v-else>
                            Change and/or modify the details for the attorney.
                        </template>
                    </template>

                    <template #actions>
                        <template v-if="user && this.$auth().can(['attorney.destroy'])">
                            <div class="flex gap-2">
                                <v-button color="danger" :disabled="form.processing" @click.prevent="deleteRequest">
                                    Delete Attorney
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

                    <v-content-body class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-x-6 gap-y-4">
                        <v-form-group>
                            <v-form-label>Name</v-form-label>
                            <v-form-input type="text" v-model="form.name" :error="form.errors.name" :disabled="form.processing" />
                            <v-form-error v-if="form.errors.name">{{ form.errors.name }}</v-form-error>
                        </v-form-group>

                        <v-form-group>
                            <v-form-label>Email</v-form-label>
                            <v-form-input type="text" v-model="form.email" :error="form.errors.email" :disabled="form.processing" />
                            <v-form-error v-if="form.errors.email">{{ form.errors.email }}</v-form-error>
                        </v-form-group>

                        <v-form-group>
                            <v-form-label>Phone Number</v-form-label>
                            <v-form-input type="text" v-model="form.phone_number" :error="form.errors.phone_number" :disabled="form.processing" />
                            <v-form-error v-if="form.errors.phone_number">{{ form.errors.phone_number }}</v-form-error>
                        </v-form-group>

                        <v-form-group>
                            <v-form-label>Birthdate</v-form-label>
                            <v-form-input type="date" v-model="form.birthdate" :error="form.errors.birthdate" :disabled="form.processing" />
                            <v-form-error v-if="form.errors.birthdate">{{ form.errors.birthdate }}</v-form-error>
                        </v-form-group>
                    </v-content-body>
                </v-form-group>

                <v-form-group class="divide-y divide-gray-200">
                    <v-section-divider>
                        Location Details
                    </v-section-divider>

                    <v-content-body class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-x-6 gap-y-4">
                        <v-form-group class="md:col-span-2">
                            <v-form-label>Address Line 1 </v-form-label>
                            <v-form-input type="text" v-model="form.address_line_1" :error="form.errors.address_line_1" :disabled="form.processing" />
                            <v-form-error v-if="form.errors.address_line_1">{{ form.errors.address_line_1 }}</v-form-error>
                        </v-form-group>

                        <v-form-group class="md:col-span-2">
                            <v-form-label>Address Line 2</v-form-label>
                            <v-form-input type="text" v-model="form.address_line_2" :error="form.errors.address_line_2" :disabled="form.processing" />
                            <v-form-error v-if="form.errors.address_line_2">{{ form.errors.address_line_2 }}</v-form-error>
                        </v-form-group>

                        <v-form-group>
                            <v-form-label>City</v-form-label>
                            <v-form-input type="text" v-model="form.city" :error="form.errors.city" :disabled="form.processing" />
                            <v-form-error v-if="form.errors.city">{{ form.errors.city }}</v-form-error>
                        </v-form-group>

                        <v-form-group>
                            <v-form-label>State</v-form-label>
                            <v-form-select
                                :options="states.data.map((state) => ({ label: state.name, value: state.state_id }))"
                                :error="form.errors.state_id"
                                :disabled="form.processing"
                                v-model="form.state_id"
                            />
                            <v-form-error v-if="form.errors.state_id">{{ form.errors.state_id }}</v-form-error>
                        </v-form-group>

                        <v-form-group>
                            <v-form-label>Zip Code</v-form-label>
                            <v-form-input type="text" v-model="form.zip_code" :error="form.errors.zip_code" :disabled="form.processing" />
                            <v-form-error v-if="form.errors.zip_code">{{ form.errors.zip_code }}</v-form-error>
                        </v-form-group>

                        <v-form-group>
                            <v-form-label>Timezone</v-form-label>
                            <v-form-select
                                :options="Object.entries(timezoneOptions).map(([value, label]) => ({ label, value }))"
                                :error="form.errors.timezone"
                                :disabled="form.processing"
                                v-model="form.timezone"
                            />
                            <v-form-error v-if="form.errors.timezone">{{ form.errors.timezone }}</v-form-error>
                        </v-form-group>
                    </v-content-body>
                </v-form-group>
            </div>

            <v-form-group class="bg-gray-50 flex items-center justify-end gap-2 text-right border-t border-gray-200">
                <v-content-body class="flex gap-4">
                    <progress v-if="form.progress" :value="form.progress.percentage" max="100">
                        {{ form.progress.percentage }}%
                    </progress>

                    <v-button :href="route('panel.attorneys.index')" color="white" :disabled="form.processing">
                        Cancel
                    </v-button>

                    <template v-if="!user">
                        <v-button :disabled="form.processing">
                            Create Attorney
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
    </div>
</template>

<script>
import Layout from "@/layouts/panel/index.vue";
import { useForm } from "@inertiajs/vue3";

export default {
    name: "UsersCreateEdit",
    layout: Layout,
    props: {
        states: {
            type: Object,
            required: false,
            default: () => {},
        },
        timezoneOptions: {
            type: Object,
            required: true,
        },
        user: {
            type: Object,
            required: false,
            default: () => {},
        },
    },
    data() {
        return {
            form: useForm({
                // General Information
                name: "",
                email: "",
                phone_number: "",
                birthdate: "",

                // Location Information
                address_line_1: "",
                address_line_2: "",
                city: "",
                state_id: "",
                zip_code: "",
                timezone: "",
            }),
        };
    },
    watch: {
        user: {
            handler(value) {
                if (!value || !value.data) {
                    return;
                }

                // General Information
                this.form.name = value.data.name;
                this.form.email = value.data.email;
                this.form.phone_number = value.data.phone_number;
                this.form.birthdate = value.data.birthdate;

                // Location Information
                this.form.address_line_1 = value.data.address_line_1;
                this.form.address_line_2 = value.data.address_line_2;
                this.form.city = value.data.city;
                this.form.state_id = value.data.state_id;
                this.form.zip_code = value.data.zip_code;
                this.form.timezone = value.data.timezone;
            },
            immediate: true,
        },
    },
    methods: {
        submitForm() {
            this.form.clearErrors();
            
            if (this.user && this.user.data && this.user.data.user_id) {
                this.updateRequest();
            } else {
                this.storeRequest();
            }
        },
        storeRequest() {
            this.form.post(this.route('panel.attorneys.store'), {
                onSuccess: () => this.$toast().success("User created successfully!"),
                onError: (errors) => {
                    Object.keys(errors).forEach((key) => {
                        const error = errors[key];
                        this.$toast().error(error);
                    });
                },
            });
        },
        updateRequest() {
            this.form.patch(this.route('panel.attorneys.update', { attorney: this.user.data.user_id }), {
                onSuccess: () => this.$toast().success("Attorney updated successfully!"),
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
                    this.form.delete(this.route('panel.attorneys.destroy', { attorney: this.user.data.user_id }), {
                        onSuccess: () => {
                            this.$toast().success("Attorney deleted successfully!");
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
