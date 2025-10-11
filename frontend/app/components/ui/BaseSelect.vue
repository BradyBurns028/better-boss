<template>
    <ClientOnly>
        <div class="flex flex-col" :class="wrapperClass">
            <label v-if="showLabel" for="select" class="mb-0.5 text-sm">
                {{ label }}
            </label>
            <Select
                id="select"
                :modelValue="modelValue"
                :options="options"
                :optionLabel="optionLabel"
                :optionValue="optionValue"
                :disabled="disabled"
                :placeholder="placeholder"
                class=""
                :filter="filter"
                :loading="!options.length"
                :editable="editable"
                @change="updateValue"
                :showClear="clearable && !!modelValue"
                scrollHeight="30vh"
                :size="size"
            >
                <template
                    v-if="showDescriptive && modelValue"
                    #value="{ value }"
                >
                    {{ label }}: {{ getLabelByValue(value) }}
                </template>
                <template v-slot:empty>
                    <span>Loading...</span>
                </template>
            </Select>
        </div>
    </ClientOnly>
</template>

<script>
export default {
    name: 'BaseSelect',
    props: {
        id: String,
        label: String,
        options: {
            type: Array,
            default: () => [],
        },
        modelValue: {
            type: [String, Number],
        },
        disabled: Boolean,
        baseOption: String,
        optionLabel: String,
        optionValue: String,
        filter: {
            type: Boolean,
            default: false,
        },
        editable: Boolean,
        showLabel: {
            type: Boolean,
            default: true,
        },
        showDescriptive: {
            type: Boolean,
            default: false,
        },
        placeholder: {
            type: String,
            default: 'Select an option',
        },
        clearable: {
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
            this.$emit('update:modelValue', value.value);
        },
        getLabelByValue(value) {
            if (!this.optionLabel) return value;
            const option = this.options.find(
                (option) => option[this.optionValue] === value
            );
            return option ? option[this.optionLabel] : '';
        },
    },
};
</script>
