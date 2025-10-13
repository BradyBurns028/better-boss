<template>
    <ClientOnly>
        <div class="flex flex-col h-full" :class="wrapperClass">
            <label v-if="showLabel" for="input" class="mb-0.5 text-sm">
                {{ label }}
            </label>
            <InputText
                :id="label"
                :type="type"
                :value="modelValue"
                @input="updateValue($event.target.value)"
                :placeholder="placeholder"
                :required="required"
                :step="step"
                :size="size"
                :disabled="disabled"
            />
        </div>
    </ClientOnly>
</template>

<script lang="ts">
export default defineNuxtComponent({
    name: 'BaseInput',
    props: {
        label: String,
        type: {
            type: String,
            default: 'text',
        },
        placeholder: String,
        required: Boolean,
        modelValue: [String, Number],
        step: String,
        showLabel: {
            type: Boolean,
            default: false,
        },
        wrapperClass: String,
        size: {
            type: String,
            default: 'small'
        },
        disabled: {
            type: Boolean,
            default: false
        }
    },
    emits: ['update:model-value'],
    methods: {
        updateValue(value) {
            this.$emit('update:model-value', value);
        },
    },
});
</script>

