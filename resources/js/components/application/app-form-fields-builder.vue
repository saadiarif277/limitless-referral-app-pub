<template>
    <v-content-body class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
        <template v-for="(field, fieldIndex) in fields" :key="'field_' + fieldIndex">
            <!--
                Field Type: Heading
            -->
            <template v-if="field.type === 'heading'">
                <template v-if="!('visible' in field) || field.visible">
                    <div class="w-full col-span-full">
                        <span class="text-base font-semibold leading-6 text-gray-700">
                            {{ field.label }}
                        </span>
                    </div>
                </template>
            </template>

            <!--
                Field Type: Divider
            -->
            <template v-else-if="field.type === 'divider'">
                <template v-if="!('visible' in field) || field.visible">
                    <div class="w-full col-span-full border-t border-b border-gray-200 border-dashed"></div>
                </template>
            </template>

            <!--
                Field Type: Component
            -->
            <template v-else-if="field.type === 'component'">
                <template v-if="!('visible' in field) || field.visible">
                    <component
                        :is="field.component"
                        :field="field"
                        v-bind="field.bind"
                        class="w-full col-span-full"
                    />
                </template>
            </template>

            <!--
                Field Type: Form Fields
            -->
            <template v-else>
                <v-app-form-field :field="field" />
            </template>
        </template>
    </v-content-body>
</template>

<script>
export default {
    name: "AppFormFieldsBuilder",
    props: {
        fields: {
            type: Array,
            required: true,
        },
    },
};
</script>
