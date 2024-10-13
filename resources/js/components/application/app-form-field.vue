<template>
    <v-form-group :class="field.class" v-if="!('visible' in field) || field.visible">
        <template v-if="field.label">
            <v-form-label>
                <div class="flex items-baseline gap-1">
                    {{ field.label }}

                    <template v-if="field.required">
                        <v-form-error>*</v-form-error>
                    </template>
                </div>

                <div>
                    <template v-if="['file'].includes(field.type) && field.model">
                        <a href="#" class="font-medium text-red-500 hover:text-red-600" @click.prevent="field.model = null">
                            <div class="max-w-full overflow-hidden line-clamp-1 text-xs uppercase">
                                Cancel
                            </div>
                        </a>
                    </template>
                </div>
            </v-form-label>
        </template>

        <!--
            Form Field: Text, Email, Password, Date
        -->
        <template v-if="['text', 'email', 'password', 'date'].includes(field.type)">
            <v-form-input
                :type="field.type"
                :required="field.required"
                v-model="field.model"
                v-bind="field.attributes"
            />
        </template>

        <!--
            Form Field: Textarea
        -->
        <template v-else-if="['textarea'].includes(field.type)">
            <v-form-textarea
                :required="field.required"
                v-model="field.model"
                v-bind="field.attributes"
            />
        </template>

        <!--
            Form Field: Select
        -->
        <template v-else-if="['select'].includes(field.type)">
            <v-form-select
                :options="field.options"
                :required="field.required"
                v-model="field.model"
                v-bind="field.attributes"
            />
        </template>

        <!--
            Form Field: Checkbox Array
        -->
        <template v-else-if="['checkbox-array'].includes(field.type)">
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3">
                <template v-for="(option, optionIndex) in field.options" :key="'option_' + optionIndex">
                    <v-form-checkbox :value="option.value" :required="field.required" v-model="field.model" v-bind="field.attributes">
                        {{ option.label }}
                    </v-form-checkbox>
                </template>
            </div>
        </template>

        <!--
            Form Field: Radio Card Array
        -->
        <template v-else-if="['radio-card-array'].includes(field.type)">
            <template v-if="!field.options || !field.options.length">
                <v-form-error>No options available.</v-form-error>
            </template>

            <template v-else>
                <RadioGroup v-model="field.model">
                    <div class="mt-4 grid grid-cols-1 gap-y-6 sm:grid-cols-3 sm:gap-x-4">
                        <RadioGroupOption as="template" v-for="(option, optionIndex) in field.options" :key="'option_' + optionIndex" :value="option.value" v-slot="{ active, checked }">
                            <div :class="[active ? 'border-primary-600 ring-1 ring-primary-600' : 'border-gray-300', 'relative flex flex-col cursor-pointer rounded-lg border bg-white p-4 shadow-sm focus:outline-none focus:ring-1 focus:ring-inset']">
                                <div class="flex-1">
                                    <span class="w-full">
                                        <RadioGroupLabel as="span" class="block text-sm font-medium text-gray-700">{{ option.label }}</RadioGroupLabel>
                                        <RadioGroupDescription as="div" class="mt-1 max-w-full flex items-center text-sm text-gray-500">
                                            <div class="truncate">
                                                {{ option.description }}
                                            </div>
                                        </RadioGroupDescription>
                                    </span>
                                </div>
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" :class="[!checked ? 'invisible' : '', 'absolute top-2 right-2 h-5 w-5 text-primary-600']">
                                    <path fill-rule="evenodd" d="M2.25 12c0-5.385 4.365-9.75 9.75-9.75s9.75 4.365 9.75 9.75-4.365 9.75-9.75 9.75S2.25 17.385 2.25 12Zm13.36-1.814a.75.75 0 1 0-1.22-.872l-3.236 4.53L9.53 12.22a.75.75 0 0 0-1.06 1.06l2.25 2.25a.75.75 0 0 0 1.14-.094l3.75-5.25Z" clip-rule="evenodd" />
                                </svg>

                                <span :class="[active ? 'border' : 'border-2', checked ? 'border-primary-600' : 'border-transparent', 'pointer-events-none absolute -inset-px rounded-lg']" aria-hidden="true" />
                            </div>
                        </RadioGroupOption>
                    </div>
                </RadioGroup>
            </template>
        </template>

        <!--
            Form Field: Toggle
        -->
        <template v-else-if="['toggle'].includes(field.type)">
            <v-form-toggle v-model="field.model" :required="field.required" v-bind="field.attributes">
                {{ field.toggleLabel || field.label }}
            </v-form-toggle>
        </template>

        <!--
            Form Field: File
        -->
        <template v-else-if="['file'].includes(field.type)">
            <template v-if="!field.modelCurrent">
                <v-form-file
                    :required="field.required"
                    v-model="field.model"
                    v-bind="field.attributes"
                />
            </template>

            <template v-else>
                <div class="flex items-stretch justify-between xl:grid-cols-2 rounded-md overflow-hidden divide-y xl:divide-y-0 divide-x-0 xl:divide-x divide-gray-200 border border-gray-100">
                    <template v-if="field.file && field.file.showFile && field.permissions.can_show">
                        <a :href="field.file.showFile(field)" class="flex-1 bg-gray-100 hover:bg-gray-200 px-4 py-2.5 font-medium text-primary-500 hover:text-primary-600" target="_blank">
                            <div class="max-w-full overflow-hidden line-clamp-1 flex items-center justify-center text-xs uppercase gap-2">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-5 h-5">
                                    <path fill-rule="evenodd" d="M10.5 3.75a6 6 0 0 0-5.98 6.496A5.25 5.25 0 0 0 6.75 20.25H18a4.5 4.5 0 0 0 2.206-8.423 3.75 3.75 0 0 0-4.133-4.303A6.001 6.001 0 0 0 10.5 3.75Zm2.25 6a.75.75 0 0 0-1.5 0v4.94l-1.72-1.72a.75.75 0 0 0-1.06 1.06l3 3a.75.75 0 0 0 1.06 0l3-3a.75.75 0 1 0-1.06-1.06l-1.72 1.72V9.75Z" clip-rule="evenodd" />
                                </svg>

                                Download
                            </div>
                        </a>
                    </template>

                    <template v-if="field.file && field.file.destroyFile && field.permissions.can_delete">
                        <a class="cursor-pointer bg-gray-100 hover:bg-red-200 px-4 py-2.5 font-medium text-red-500 hover:text-red-600" @click.stop="field.file.destroyFile(field)">
                            <div class="max-w-full overflow-hidden line-clamp-1 flex items-center justify-center text-xs uppercase gap-2">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-5 h-5">
                                    <path fill-rule="evenodd" d="M16.5 4.478v.227a48.816 48.816 0 0 1 3.878.512.75.75 0 1 1-.256 1.478l-.209-.035-1.005 13.07a3 3 0 0 1-2.991 2.77H8.084a3 3 0 0 1-2.991-2.77L4.087 6.66l-.209.035a.75.75 0 0 1-.256-1.478A48.567 48.567 0 0 1 7.5 4.705v-.227c0-1.564 1.213-2.9 2.816-2.951a52.662 52.662 0 0 1 3.369 0c1.603.051 2.815 1.387 2.815 2.951Zm-6.136-1.452a51.196 51.196 0 0 1 3.273 0C14.39 3.05 15 3.684 15 4.478v.113a49.488 49.488 0 0 0-6 0v-.113c0-.794.609-1.428 1.364-1.452Zm-.355 5.945a.75.75 0 1 0-1.5.058l.347 9a.75.75 0 1 0 1.499-.058l-.346-9Zm5.48.058a.75.75 0 1 0-1.498-.058l-.347 9a.75.75 0 0 0 1.5.058l.345-9Z" clip-rule="evenodd" />
                                </svg>
                            </div>
                        </a>
                    </template>
                </div>
            </template>
        </template>

        <!--
            Form Field: Unknown
        -->
        <template v-else>
            <v-form-error>Form field "{{ field.type }}" is not supported.</v-form-error>
        </template>

        <!-- Form Error -->
        <v-form-error v-if="field.error">
            {{ field.error }}
        </v-form-error>
    </v-form-group>
</template>

<script>
import { RadioGroup, RadioGroupDescription, RadioGroupLabel, RadioGroupOption } from "@headlessui/vue";

export default {
    name: "AppFormField",
    components: {
        RadioGroup,
        RadioGroupDescription,
        RadioGroupLabel,
        RadioGroupOption
    },
    props: {
        field: {
            type: Object,
            required: true,
        },
    },
};
</script>
