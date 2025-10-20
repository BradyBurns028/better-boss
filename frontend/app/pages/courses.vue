<script setup lang="ts">
definePageMeta({
  layout: 'student-header'
})

// selected term
const selectedTerm = ref('')
</script>
<template>
  <main class="p-6">
    <!-- prompt term select if no term is selected -->
    <div v-if="!selectedTerm" class="max-w-md mx-auto bg-white border border-gray-300 rounded-lg shadow-sm p-4">
      <TermSelect v-model="selectedTerm" />
    </div>
    <!-- show page content when a term is selected -->
    <div v-else class="text-center text-2xl">
      place holder for courses page
    </div>
  </main>
</template>