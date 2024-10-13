<template>
    <div class="px-2 py-2 bg-gray-100 rounded-md text-sm">
        <input type="file" v-bind="$attrs" @change="updateValue" ref="fileInput" />
    </div>
</template>

<script>
export default {
    name: "FormFileComponent",
    props: ["modelValue"],
    emits: ['update:modelValue'],
    watch: {
        modelValue(newValue) {
            if (newValue === null || newValue === '' || newValue === undefined) {
                this.resetInput();
            }
        },
    },
    methods: {
        resetInput() {
            this.$refs.fileInput.value = '';
        },
        updateValue(event) {
            this.$emit('update:modelValue', event.target.files[0]);
        },
    },
};
</script>
