<template>
    <ClientOnly>
        <div class="flex flex-col" :class="wrapperClass">
            <label v-if="showLabel" for="input" class="mb-0.5 text-sm">
                {{ label }}
            </label>
            <AutoComplete
                id="input"
                v-model="internalValue"
                :suggestions="filteredSuggestions"
                :placeholder="placeholder"
                :required="required"
                @complete="search"
                @select="handleComplete"
                :size="size"
                inputClass="w-full"
            />
        </div>
    </ClientOnly>
</template>

<script lang="ts">
export default defineNuxtComponent({
    name: 'BaseAutoComplete',
    props: {
        label: String,
        placeholder: String,
        required: Boolean,
        suggestions: {
            type: Array,
            default: () => [],
        },
        modelValue: {
            type: String,
            default: '',
        },
        showLabel: {
            type: Boolean,
            default: false,
        },
        wrapperClass: String,
        size: {
            type: String,
            default: 'small',
        }
    },
    data() {
        return {
            filteredSuggestions: [],
        };
    },
    emits: ['update:modelValue', 'complete'],
    computed: {
        internalValue: {
            get() {
                return this.modelValue;
            },
            set(value) {
                this.$emit('update:modelValue', value);
            },
        },
    },
    methods: {
        search(query) {
            if (query.query.length > 0) {
                if (this.suggestions) {
                    this.filteredSuggestions = this.suggestions.filter((item) =>
                        item.toLowerCase().includes(query.query.toLowerCase())
                    );
                }
            } else {
                this.filteredSuggestions = [...this.suggestions];
            }
        },
        handleComplete() {
            this.$emit('complete', this.internalValue);
        },
    },
    watch: {
        suggestions(newSuggestions) {
            this.filteredSuggestions = [...newSuggestions];
        },
    },
    mounted() {
        this.filteredSuggestions = [...this.suggestions];
    },
});
</script>
