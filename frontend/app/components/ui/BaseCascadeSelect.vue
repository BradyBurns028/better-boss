<template>
    <div class="flex flex-col" :class="wrapperClass">
        <label v-if="showLabel" for="select" class="mb-0.5 text-sm">
            {{ label }}
        </label>
        <CascadeSelect
            id="select"
            :modelValue="modelValue"
            :options="options"
            :optionLabel="optionLabel"
            :optionValue="optionValue"
            :optionGroupLabel="optionGroupLabel"
            :optionGroupChildren="optionGroupChildren"
            :disabled="disabled"
            :placeholder="placeholder"
            class=""
            :filter="filter"
            :loading="!options.length"
            :editable="editable"
            @change="updateValue"
            :showClear="clearable && !!modelValue"
            :size="size"
        >
            <template
                v-if="showDescriptive && modelValue"
                #value="{ value }"
            >
                {{ label }}: {{ getLabelByValue(value) }}
            </template>
        </CascadeSelect>
    </div>
</template>

<script lang="ts">
export default defineNuxtComponent({
    name: 'BaseCascadeSelect',
    props: {
        id: String,
        label: String,
        options: {
            type: Array as unknown as PropType<Array<Record<string, any>>>,
            default: () => [],
        },
        modelValue: {
            type: [String, Number] as unknown as PropType<string | number | undefined>,
            default: undefined,
        },
        required: Boolean,
        disabled: Boolean,
        baseOption: String,
        optionLabel: String,
        optionGroupLabel: String,
        optionValue: String,
        optionGroupChildren: Array,
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
            default: 'small',
        },
    },
    emits: {
        'update:modelValue': (_v: string | number | undefined) => true,
    },
    methods: {
        updateValue(
            this: { $emit: (e: 'update:modelValue', v: string | number | undefined) => void },
            value: any
        ): void {
            this.$emit('update:modelValue', value?.value);
        },
        getLabelByValue(
            this: {
                optionLabel?: string;
                optionValue?: string;
                options: Array<Record<string, any>>;
            },
            value: any
        ) {
            if (!this.optionLabel || !this.optionValue) return value;
            const option = this.options.find(
                (o) => o?.[this.optionValue as string] === value
            );
            return option ? option[this.optionLabel as string] : '';
        },
    },
});
</script>
