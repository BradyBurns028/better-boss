<template>
    <ClientOnly>
        <div class="flex flex-col" :class="childClass">
            <label v-if="showLabel" for="input" class="mb-0.5 text-sm">
                {{ label }}
            </label>
            <Textarea
                :id="label"
                :value="modelValue"
                @input="updateValue($event.target.value)"
                :placeholder="placeholder"
                :required="required"
                :size="size"
                :auto-resize="autoResize"
            />
        </div>
    </ClientOnly>
</template>

<script lang="ts">
export default defineNuxtComponent({
    name: 'BaseTextarea',
    props: {
        label: {
            type: String,
            default: ''
        },
        placeholder: {
            type: String,
            default: ''
        },
        required: Boolean,
        modelValue: {
            type: String,
            default: ''
        },
        step: String,
        showLabel: {
            type: Boolean,
            default: true,
        },
        childClass: String,
        size: {
            type: String,
            default: 'small'
        },
        autoResize: {
            type: Boolean,
            default: true
        }
    },
    emits: ['update:model-value'],
    methods: {
        updateValue(value: any) {
            this.$emit('update:model-value', value);
        },
    },
});
</script>

