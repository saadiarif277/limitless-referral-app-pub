<template>
    <div class="w-full grid grid-cols-1 gap-12">
        <template v-for="(fieldGroup, fieldGroupIndex) in fieldGroups" :key="'fieldGroup_' + fieldGroupIndex">
            <div>
                <v-section-divider>
                    {{ fieldGroup.title }}
                </v-section-divider>

                <v-app-form-fields-builder :fields="fieldGroup.fields" />
            </div>
        </template>
    </div>
</template>

<script>
export default {
    props: {
        fieldGroups: {
            type: Array,
            required: true,
        },
    },
};
</script>
