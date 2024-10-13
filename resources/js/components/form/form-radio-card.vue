<template>
    <label :class="`bg-gray-100 hover:bg-gray-200 flex items-center gap-4 px-4 py-4 rounded-md border border-transparent cursor-pointer ${attrs.disabled ? 'opacity-50 cursor-not-allowed' : ''}`">
        <input type="radio" v-bind="attrs" v-model="value">
        <div class="text-sm text-gray-700 font-medium line-clamp-2">
            <slot />
        </div>
    </label>
</template>

<script>
export default {
    name: "FormCheckboxComponent",
    props: ["modelValue", "error"],
    emits: ["update:modelValue"],
    computed: {
        attrs() {
            return {
                ...this.$attrs,
                ...{
                    class: "v-form-radio focus:ring-0 h-4 w-4 text-primary-500 border-gray-300 rounded-full",
                },
            };
        },
        value: {
            get() {
                return this.modelValue;
            },
            set(value) {
                this.$emit("update:modelValue", value);
            }
        }
    },
};
</script>
