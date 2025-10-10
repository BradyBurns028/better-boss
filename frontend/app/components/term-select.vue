<script lang="ts">
export default defineComponent({

  // props passed from parent component
  props: {
    // selected term
    modelValue: { type: String, default: '' },
    // list of terms
    terms: { 
      type: Array as () => string[], 
      default: () => ['Fall 2025', 'Winter 2026', 'Spring 2026', 'Summer 2026'] 
    }
  },
  // events that this component can emit
  emits: ['update:model-value'],
  methods: {
    // triggered when the select value changes
    onChange(event: Event) {
      // cast event target to HTMLSelectElement to access value
      const selectEl = event.target as HTMLSelectElement
      // emit the updated value to parent
      this.$emit('update:model-value', selectEl.value)
    }
  }
})
</script>
<template>
  <div class="w-full">
    <!-- section header -->
    <h2 class="text-lg font-semibold mb-2">Select Term</h2>
    <!-- dropdown select for terms -->
    <select 
      :value="modelValue" 
      @change="onChange" 
      class="w-full px-3 py-2 border border-gray-400 rounded-md shadow-sm text-gray-900 bg-white">
      <!-- placeholder option -->
      <option value="" disabled hidden>Select a term</option>
      <!-- render each term as an option -->
      <option v-for="term in terms" :key="term" :value="term">{{term}}</option>
    </select>
  </div>
</template>
