<template>
    <div class="v-section-heading w-full flex flex-col sm:flex-row gap-2 items-center justify-between">
        <div :class="`w-full ${$slots.description ? 'py-0.5' : '' }`">
            <h2 :size="6" class="text-base font-semibold leading-6 text-gray-700" v-if="$slots.title">
                <slot name="title" />
            </h2>

            <p class="text-sm leading-6 text-gray-500" v-if="$slots.description">
                <slot name="description" />
            </p>
        </div>

        <template v-if="$slots.actions">
            <div class="w-full sm:w-auto flex-shrink-0">
                <slot name="actions" />
            </div>
        </template>
    </div>
</template>

<script>
export default {
    name: "SectionHeadingComponent",
};
</script>
