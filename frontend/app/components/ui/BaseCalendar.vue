<template>
    <ClientOnly>
        <div class="flex flex-col" :class="wrapperClass">
            <label v-if="showLabel" for="input" class="mb-0.5 text-sm">
                {{ label }}
            </label>
            <Calendar
                id="input"
                :modelValue="modelValue"
                @update:modelValue="updateValue"
                :placeholder="placeholder"
                :required="required"
                :size="size"
                class="rounded text-sm form-input w-full"
            />
        </div>
    </ClientOnly>
</template>

<script lang="ts">
export default defineNuxtComponet({
    name: 'BaseInput',
    props: {
        label: String,
        placeholder: String,
        required: Boolean,
        modelValue: [Date, String],
        showLabel: {
            type: Boolean,
            default: false,
        },
        wrapperClass: String,
        size: {
            type: String,
            default: 'small'
        }
    },
    emits: ['update:modelValue'],
    methods: {
        updateValue(value) {
            this.$emit('update:modelValue', value);
        },
    },
});
</script>
<style scoped>
.p-inputtext {
    padding-left: 0.25rem;
}
</style>
