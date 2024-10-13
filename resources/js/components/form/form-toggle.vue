<template>
    <SwitchGroup as="div" class="flex items-center py-2 px-2 border border-gray-200 rounded-md">
        <Switch :modelValue="value" @update:modelValue="updateValue" :class="[value ? 'bg-primary-500' : 'bg-gray-200', 'relative inline-flex h-6 w-11 flex-shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out focus:outline-none']">
            <span aria-hidden="true" :class="[value ? 'translate-x-5' : 'translate-x-0', 'pointer-events-none inline-block h-5 w-5 transform rounded-full bg-white shadow ring-0 transition duration-200 ease-in-out']" />
        </Switch>
        <SwitchLabel as="span" class="ml-3 text-gray-700 text-sm">
            <slot></slot>
        </SwitchLabel>
    </SwitchGroup>
</template>

<script>
import { Switch, SwitchGroup, SwitchLabel } from '@headlessui/vue';

export default {
    components: {
        Switch,
        SwitchGroup,
        SwitchLabel
    },
    props: {
        modelValue: {
            type: Boolean,
            default: false
        }
    },
    emits: [
        'update:modelValue',
    ],
    computed: {
        value: {
            get() { return this.modelValue; },
            set(value) { this.$emit('update:modelValue', value); }
        },
    },
    methods: {
        updateValue(value) {
            this.$emit('update:modelValue', value);
        },
    },
};
</script>
